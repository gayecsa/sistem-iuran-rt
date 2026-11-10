<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menyimpan riwayat halaman yang dikunjungi (stack) di session
 * supaya tombol Back bisa mundur beberapa langkah.
 */
class NavigationHistory
{
    public const KEY = 'nav_history';
    private const MAX = 30;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->isMethod('GET') || $request->ajax() || $request->routeIs('nav.back')
            || !$request->hasSession() || $response->getStatusCode() !== 200
            || !str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        $url = $request->fullUrl();
        $stack = $request->session()->get(self::KEY, []);
        $count = count($stack);

        if ($count && end($stack) === $url) {
            // refresh halaman yang sama -> tidak berubah
        } elseif ($count >= 2 && $stack[$count - 2] === $url) {
            // user kembali pakai tombol back browser -> pop
            array_pop($stack);
        } else {
            $stack[] = $url;
            $stack = array_slice($stack, -self::MAX);
        }

        $request->session()->put(self::KEY, $stack);
        return $response;
    }
}
