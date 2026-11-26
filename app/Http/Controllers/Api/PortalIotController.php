<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BankSampahSaldo;
use App\Models\IotDevice;
use App\Models\PortalGateLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortalIotController extends Controller
{
    /**
     * Memeriksa kecocokan API Key dari header atau request query/body.
     */
    protected function validateApiKey(Request $request): bool
    {
        $configuredKey = (string) config('services.iot.key', env('IOT_PORTAL_KEY', 'haven-rw013-gate-key-2026'));
        $receivedKey = (string) (
            $request->header('X-API-KEY')
            ?? $request->header('X-Api-Key')
            ?? $request->input('api_key')
            ?? $request->bearerToken()
        );

        if (!$configuredKey || !$receivedKey) {
            return false;
        }

        return hash_equals($configuredKey, $receivedKey);
    }

    /**
     * Endpoint POST /api/iot/portal/tap
     * Dipanggil oleh ESP32 setiap kali kartu RFID di-tap.
     * Mendukung 4 mode operasional: portal, ronda, bank_sampah, register.
     */
    public function tap(Request $request): JsonResponse
    {
        // 1. Validasi API Key
        if (!$this->validateApiKey($request)) {
            return response()->json([
                'access' => false,
                'status' => 'unauthorized',
                'error' => 'Unauthorized',
                'message' => 'API Key gerbang tidak valid atau tidak disediakan.',
            ], 401);
        }

        // 2. Normalisasi Payload
        $rawUid = (string) $request->input('uid', '');
        $uid = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $rawUid));
        $deviceId = trim((string) $request->input('device_id', 'GATE-01')) ?: 'GATE-01';

        if (empty($uid)) {
            return response()->json([
                'access' => false,
                'status' => 'invalid_input',
                'message' => 'RFID UID tidak boleh kosong.',
            ], 422);
        }

        // 3. Update Status Keberadaan Alat (Heartbeat & Last UID)
        $device = IotDevice::firstOrCreate(
            ['device_code' => $deviceId],
            [
                'name' => 'Portal & Reader Utama RW 013',
                'mode' => 'portal',
            ]
        );

        $device->update([
            'last_seen_at' => now(),
            'last_uid' => $uid,
        ]);

        // 4. Proses Berdasarkan Mode Operasi Aktif Perangkat
        return match ($device->mode) {
            'register' => $this->handleRegistrationMode($device, $uid, $deviceId),
            'ronda' => $this->handleRondaMode($uid, $deviceId),
            'bank_sampah' => $this->handleBankSampahMode($uid, $deviceId),
            default => $this->handlePortalAccessMode($uid, $deviceId),
        };
    }

    /**
     * MODE 1: REGISTRASI KARTU BARU (mode = register)
     */
    protected function handleRegistrationMode(IotDevice $device, string $uid, string $deviceId): JsonResponse
    {
        $targetUser = $device->target_user_id ? User::find($device->target_user_id) : null;

        if (!$targetUser) {
            $device->update([
                'mode' => 'portal',
                'target_user_id' => null,
            ]);

            return response()->json([
                'access' => false,
                'mode' => 'register',
                'message' => 'Target warga tidak ditemukan. Mode alat dikembalikan ke portal.',
            ], 422);
        }

        // Cek apakah UID sudah dipakai oleh warga lain
        $existing = User::where('rfid_uid', $uid)
            ->where('id', '!=', $targetUser->id)
            ->first();

        if ($existing) {
            $existingRt = $existing->clean_rt_number ?? $existing->rt_number ?? '001';
            return response()->json([
                'access' => false,
                'mode' => 'register',
                'message' => "Kartu UID {$uid} sudah terdaftar atas nama: {$existing->name} (RT {$existingRt}).",
            ], 422);
        }

        // Simpan UID kartu ke warga target
        $targetUser->update(['rfid_uid' => $uid]);

        $chipType = strlen($uid) >= 12 ? 'e-KTP Fisik' : 'Kartu RFID';
        $maskedUid = strlen($uid) >= 12
            ? substr($uid, 0, 4) . '******' . substr($uid, -4)
            : (strlen($uid) > 4 ? substr($uid, 0, 2) . '****' . substr($uid, -2) : '***' . $uid);

        // Catat log
        PortalGateLog::create([
            'user_id' => $targetUser->id,
            'rfid_uid' => $uid,
            'device_id' => $deviceId,
            'mode' => 'register',
            'status' => 'granted',
            'reason' => "Rekam {$chipType} Berhasil",
            'tapped_at' => now(),
        ]);

        // Reset mode perangkat ESP32 kembali ke 'portal'
        $device->update([
            'mode' => 'portal',
            'target_user_id' => null,
        ]);

        ActivityLog::record(
            'keamanan',
            'portal.rfid_registered',
            "Perekaman {$chipType} ({$maskedUid}) berhasil untuk warga: {$targetUser->name}",
            [
                'uid' => $uid,
                'chip_type' => $chipType,
                'target_user_id' => $targetUser->id,
                'target_name' => $targetUser->name,
                'device_id' => $deviceId,
            ]
        );

        return response()->json([
            'access' => true,
            'mode' => 'register',
            'chip_type' => $chipType,
            'uid' => $uid,
            'masked_uid' => $maskedUid,
            'name' => $targetUser->name,
            'rt' => $targetUser->clean_rt_label ?? 'RT 001',
            'message' => "{$chipType} berhasil didaftarkan untuk {$targetUser->name}",
        ], 200);
    }

    /**
     * MODE 2: PORTAL GERBANG MASUK (mode = portal)
     */
    protected function handlePortalAccessMode(string $uid, string $deviceId): JsonResponse
    {
        $user = User::where('rfid_uid', $uid)->first();

        $isGranted = false;
        $reason = 'Kartu Tidak Dikenal';

        if ($user) {
            $isActive = ($user->is_active !== false)
                && ($user->status !== 'rejected')
                && ($user->status !== 'inactive');

            if ($isActive) {
                $isGranted = true;
                $reason = 'Warga Terdaftar';
            } else {
                $reason = 'Akun Nonaktif';
            }
        }

        $log = PortalGateLog::create([
            'user_id' => $user?->id,
            'rfid_uid' => $uid,
            'device_id' => $deviceId,
            'mode' => 'portal',
            'status' => $isGranted ? 'granted' : 'denied',
            'reason' => $reason,
            'tapped_at' => now(),
        ]);

        if ($isGranted) {
            ActivityLog::record(
                'keamanan',
                'portal.gate_opened',
                "Akses Gerbang Utama dibuka untuk warga {$user->name} ({$uid})",
                [
                    'log_id' => $log->id,
                    'uid' => $uid,
                    'user_id' => $user->id,
                    'device_id' => $deviceId,
                ]
            );

            $cleanRt = $user->clean_rt_number ?? $user->rt_number ?? '001';
            $rtLabel = 'RT ' . str_pad($cleanRt, 3, '0', STR_PAD_LEFT);

            return response()->json([
                'access' => true,
                'mode' => 'portal',
                'status' => 'granted',
                'name' => $user->name,
                'rt' => $rtLabel,
                'role' => ucfirst($user->role ?? 'Warga'),
                'message' => 'Portal Terbuka',
            ], 200);
        }

        ActivityLog::record(
            'keamanan',
            'portal.gate_denied',
            "Akses Gerbang Utama ditolak untuk UID {$uid} ({$reason})",
            [
                'log_id' => $log->id,
                'uid' => $uid,
                'device_id' => $deviceId,
                'reason' => $reason,
            ]
        );

        return response()->json([
            'access' => false,
            'mode' => 'portal',
            'status' => 'denied',
            'message' => 'Kartu Tidak Terdaftar',
        ], 200);
    }

    /**
     * MODE 3: ABSENSI RONDA MALAM (mode = ronda)
     */
    protected function handleRondaMode(string $uid, string $deviceId): JsonResponse
    {
        $user = User::where('rfid_uid', $uid)->first();

        $isGranted = false;
        $reason = 'Kartu Tidak Dikenal';

        if ($user) {
            $isActive = ($user->is_active !== false)
                && ($user->status !== 'rejected')
                && ($user->status !== 'inactive');

            if ($isActive) {
                $isGranted = true;
                $reason = 'Absensi Ronda Hadir';
            } else {
                $reason = 'Akun Nonaktif';
            }
        }

        $log = PortalGateLog::create([
            'user_id' => $user?->id,
            'rfid_uid' => $uid,
            'device_id' => $deviceId,
            'mode' => 'ronda',
            'status' => $isGranted ? 'granted' : 'denied',
            'reason' => $reason,
            'tapped_at' => now(),
        ]);

        if ($isGranted) {
            ActivityLog::record(
                'keamanan',
                'ronda.rfid_checkin',
                "Absensi kehadiran ronda malam via RFID tercatat untuk {$user->name} ({$uid})",
                [
                    'log_id' => $log->id,
                    'uid' => $uid,
                    'user_id' => $user->id,
                    'device_id' => $deviceId,
                ]
            );

            $cleanRt = $user->clean_rt_number ?? $user->rt_number ?? '001';
            $rtLabel = 'RT ' . str_pad($cleanRt, 3, '0', STR_PAD_LEFT);

            return response()->json([
                'access' => true,
                'mode' => 'ronda',
                'status' => 'granted',
                'name' => $user->name,
                'rt' => $rtLabel,
                'role' => ucfirst($user->role ?? 'Warga'),
                'message' => 'Absen Ronda Tercatat',
            ], 200);
        }

        return response()->json([
            'access' => false,
            'mode' => 'ronda',
            'status' => 'denied',
            'message' => 'Kartu Tidak Terdaftar',
        ], 200);
    }

    /**
     * MODE 4: IDENTIFIKASI NASABAH BANK SAMPAH (mode = bank_sampah)
     */
    protected function handleBankSampahMode(string $uid, string $deviceId): JsonResponse
    {
        $user = User::where('rfid_uid', $uid)->first();

        $isGranted = false;
        $reason = 'Kartu Tidak Dikenal';

        if ($user) {
            $isActive = ($user->is_active !== false)
                && ($user->status !== 'rejected')
                && ($user->status !== 'inactive');

            if ($isActive) {
                $isGranted = true;
                $reason = 'Identifikasi Nasabah Bank Sampah';
            } else {
                $reason = 'Akun Nonaktif';
            }
        }

        $log = PortalGateLog::create([
            'user_id' => $user?->id,
            'rfid_uid' => $uid,
            'device_id' => $deviceId,
            'mode' => 'bank_sampah',
            'status' => $isGranted ? 'granted' : 'denied',
            'reason' => $reason,
            'tapped_at' => now(),
        ]);

        if ($isGranted) {
            $saldo = BankSampahSaldo::where('user_id', $user->id)->first();
            $saldoAktif = $saldo ? (float) $saldo->saldo_aktif : 0;
            $saldoFormatted = $saldo ? $saldo->saldo_aktif_formatted : 'Rp 0';
            $totalKg = $saldo ? (float) $saldo->total_kg_terkumpul : 0;

            ActivityLog::record(
                'keamanan',
                'bank_sampah.rfid_identified',
                "Identifikasi nasabah Bank Sampah via RFID: {$user->name} ({$uid})",
                [
                    'log_id' => $log->id,
                    'uid' => $uid,
                    'user_id' => $user->id,
                    'device_id' => $deviceId,
                    'saldo_aktif' => $saldoAktif,
                ]
            );

            $cleanRt = $user->clean_rt_number ?? $user->rt_number ?? '001';
            $rtLabel = 'RT ' . str_pad($cleanRt, 3, '0', STR_PAD_LEFT);

            return response()->json([
                'access' => true,
                'mode' => 'bank_sampah',
                'status' => 'granted',
                'user_id' => $user->id,
                'name' => $user->name,
                'rt' => $rtLabel,
                'saldo' => $saldoAktif,
                'saldo_formatted' => $saldoFormatted,
                'total_kg' => $totalKg,
                'message' => 'Nasabah Bank Sampah Teridentifikasi',
            ], 200);
        }

        return response()->json([
            'access' => false,
            'mode' => 'bank_sampah',
            'status' => 'denied',
            'message' => 'Kartu Nasabah Tidak Dikenal',
        ], 200);
    }

    /**
     * Endpoint GET /api/iot/portal/live-status
     * Polling real-time oleh frontend dashboard web.
     */
    public function liveStatus(Request $request): JsonResponse
    {
        $device = IotDevice::getDefault();
        $targetUser = $device->target_user_id ? User::find($device->target_user_id) : null;

        $lastTap = PortalGateLog::with('user:id,name,role,rt_number,house_number')
            ->latest('tapped_at')
            ->first();

        $recentLogs = PortalGateLog::with('user:id,name,role,rt_number,house_number')
            ->latest('tapped_at')
            ->take(15)
            ->get();

        // Data tambahan jika last tap adalah bank sampah
        $extraBankSampah = null;
        if ($lastTap && $lastTap->mode === 'bank_sampah' && $lastTap->user_id) {
            $saldo = BankSampahSaldo::where('user_id', $lastTap->user_id)->first();
            if ($saldo) {
                $extraBankSampah = [
                    'saldo_aktif' => (float) $saldo->saldo_aktif,
                    'saldo_formatted' => $saldo->saldo_aktif_formatted,
                    'total_kg' => (float) $saldo->total_kg_terkumpul,
                ];
            }
        }

        return response()->json([
            'device' => [
                'code' => $device->device_code,
                'name' => $device->name,
                'is_online' => $device->isOnline(),
                'mode' => $device->mode,
                'target_user' => $targetUser ? [
                    'id' => $targetUser->id,
                    'name' => $targetUser->name,
                    'rt' => 'RT ' . str_pad($targetUser->clean_rt_number ?? $targetUser->rt_number ?? '001', 3, '0', STR_PAD_LEFT),
                ] : null,
                'last_seen_at' => $device->last_seen_at?->toIso8601String(),
                'last_seen_human' => $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Belum pernah terhubung',
                'last_uid' => $device->last_uid,
            ],
            'last_tap' => $lastTap ? [
                'id' => $lastTap->id,
                'rfid_uid' => $lastTap->rfid_uid,
                'device_id' => $lastTap->device_id,
                'mode' => $lastTap->mode,
                'status' => $lastTap->status,
                'reason' => $lastTap->reason,
                'tapped_at' => $lastTap->tapped_at?->toIso8601String(),
                'tapped_at_formatted' => $lastTap->tapped_at?->translatedFormat('d M Y, H:i:s'),
                'tapped_at_human' => $lastTap->tapped_at?->diffForHumans(),
                'bank_sampah' => $extraBankSampah,
                'user' => $lastTap->user ? [
                    'id' => $lastTap->user->id,
                    'name' => $lastTap->user->name,
                    'role' => ucfirst($lastTap->user->role ?? 'Warga'),
                    'rt' => 'RT ' . str_pad($lastTap->user->clean_rt_number ?? $lastTap->user->rt_number ?? '001', 3, '0', STR_PAD_LEFT),
                    'house_number' => $lastTap->user->house_number ?? '-',
                ] : null,
            ] : null,
            'recent_logs' => $recentLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'rfid_uid' => $log->rfid_uid,
                    'device_id' => $log->device_id,
                    'mode' => $log->mode,
                    'status' => $log->status,
                    'reason' => $log->reason,
                    'tapped_at_formatted' => $log->tapped_at?->translatedFormat('d/m/Y H:i:s'),
                    'tapped_at_human' => $log->tapped_at?->diffForHumans(),
                    'user_name' => $log->user?->name ?? 'Tamu / Asing',
                    'user_rt' => $log->user ? ('RT ' . str_pad($log->user->clean_rt_number ?? $log->user->rt_number ?? '001', 3, '0', STR_PAD_LEFT)) : '-',
                    'user_role' => $log->user ? ucfirst($log->user->role ?? 'Warga') : '-',
                ];
            }),
            'timestamp' => now()->toIso8601String(),
        ], 200);
    }
}

