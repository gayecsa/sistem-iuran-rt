<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminViewOnlyMiddleware
{
    /**
     * Handle an incoming request.
     * Mencegah mutasi database saat Super Admin sedang mensimulasikan role dalam Mode Lihat Saja.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $isImpersonating = session('is_impersonating', false);
        $mode = session('impersonation_mode', 'view_only');

        // Jika sedang impersonasi dan dalam Mode Lihat Saja
        if ($isImpersonating && $mode === 'view_only') {
            $method = strtoupper($request->method());

            // Blokir semua mutasi HTTP (POST, PUT, PATCH, DELETE)
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                // Route yang dikecualikan (tetap diizinkan)
                if (
                    $request->routeIs(['switch.role', 'switch.role.revert', 'switch.role.mode', 'logout']) ||
                    $request->is('switch-role*', 'logout')
                ) {
                    return $next($request);
                }

                $message = 'Mode Lihat Saja aktif — ubah ke Mode Edit pada banner atas untuk melakukan perubahan data.';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $message,
                    ], 403);
                }

                return back()->with('error', $message);
            }
        }

        return $next($request);
    }
}
