<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = 'web'): Response
    {
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('login');
        }

        // Additional check for regular user type
        $user = Auth::guard($guard)->user();
        if (!$user->userType || $user->userType->guard_name !== 'web') {
            Auth::guard($guard)->logout();
            return redirect()->route('login');
        }

        return $next($request);
        return $next($request);
    }
}
