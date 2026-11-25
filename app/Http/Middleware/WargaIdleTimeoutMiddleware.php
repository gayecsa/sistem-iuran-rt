<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WargaIdleTimeoutMiddleware
{
    /**
     * Waktu batas idle dalam detik (15 menit = 900 detik).
     */
    public const IDLE_TIMEOUT_SECONDS = 900;

    /**
     * Handle an incoming request.
     * Khusus memantau sesi pengguna role 'warga' yang aktif dan bukan sedang diimpersonasi.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $isImpersonating = session('is_impersonating', false);

            // Aturan ketat: HANYA berlaku untuk role 'warga' murni (bukan impersonasi admin)
            if ($user && $user->role === 'warga' && !$isImpersonating) {
                $lastActivity = session('warga_last_activity');
                $now = time();

                if ($lastActivity && ($now - $lastActivity) > self::IDLE_TIMEOUT_SECONDS) {
                    $idleSeconds = $now - $lastActivity;

                    // Catat activity log fail-safe
                    try {
                        ActivityLog::record(
                            'auth',
                            'auth.idle_timeout',
                            'Sesi warga berakhir otomatis karena tidak aktif selama 15 menit',
                            [
                                'role' => 'warga',
                                'user_id' => $user->id,
                                'idle_seconds' => $idleSeconds,
                                'last_activity_at' => date('Y-m-d H:i:s', $lastActivity),
                            ],
                            $user->id
                        );
                    } catch (\Throwable $e) {
                        // Fail-safe
                    }

                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    $warningMessage = 'Sesi Anda telah berakhir otomatis karena tidak aktif selama 15 menit. Silakan masuk kembali.';

                    if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                        return response()->json([
                            'message' => $warningMessage,
                            'redirect' => route('login'),
                        ], 401);
                    }

                    return redirect()->route('login')->with('warning', $warningMessage);
                }

                // Perbarui waktu aktivitas terakhir warga di session
                session(['warga_last_activity' => $now]);
            }
        }

        return $next($request);
    }
}
