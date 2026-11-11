<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        $userRole = strtolower($user->role ?? 'warga');

        // Normalisasi alias role:
        // 'rw' mencakup 'admin', 'rw', 'super_admin'
        // 'admin' mencakup 'admin', 'super_admin', 'rw'
        $allowed = [];
        foreach ($roles as $r) {
            $r = strtolower($r);
            $allowed[] = $r;
            if ($r === 'rw' || $r === 'admin') {
                $allowed = array_merge($allowed, ['admin', 'super_admin', 'rw']);
            }
        }
        $allowed = array_unique($allowed);

        if (in_array($userRole, $allowed)) {
            return $next($request);
        }

        // Tentukan rute dashboard tujuan sesuai role pengguna
        $targetRoute = 'dashboard';
        if (in_array($userRole, ['admin', 'rw', 'super_admin', 'bendahara'])) {
            $targetRoute = 'dashboard.rw';
        } elseif ($userRole === 'rt') {
            $targetRoute = 'dashboard.rt';
        } else {
            $targetRoute = 'dashboard.warga';
        }

        return redirect()->route($targetRoute)->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman tersebut.');
    }
}
