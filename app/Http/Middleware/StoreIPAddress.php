<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StoreIPAddress
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user tidak login
        if (!$request->user()) {
            // Simpan IP address ke dalam session
            $request->session()->put('ip_address', $request->ip());
        }

        return $next($request);
    }
}
