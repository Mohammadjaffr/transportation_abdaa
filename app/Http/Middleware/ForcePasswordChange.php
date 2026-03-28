<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
           if (auth()->check() && auth()->user()->require_password_change) {
            
            // منع حلقة التوجيه (Redirect Loop) عن طريق استثناء مسار التغيير نفسه ومسار تسجيل الخروج
            if (!$request->routeIs('driver.force-change-password') && !$request->routeIs('logout')) {
                return redirect()->route('driver.force-change-password');
            }
        }
        return $next($request);
    }
}
