<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return $next($request);
            }
            if (Auth::user()->role === 'driver') {
                return redirect()->route('driver.dashboard');
            }
        }

        return redirect('/')->with('error', 'ليس لديك صلاحية الدخول كمسؤول.');
    }
}
