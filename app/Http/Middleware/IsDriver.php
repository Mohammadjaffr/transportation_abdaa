<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsDriver
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->role === 'driver' && !empty($user->driver_id)) {
            if ($user->is_banned) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors(['email' => 'عذراً، هذا الحساب محظور من الإدارة ولا يمكنك الدخول.']);
            }
            return $next($request);
        }

        if ($user && $user->role === 'admin') {
            return redirect()->route('home')->with('error', 'هذه الصفحة مخصصة للسائقين فقط.');
        }

        abort(403, 'هذا الحساب غير مرتبط بسائق أو لا يملك صلاحية الدخول.');
    }
}