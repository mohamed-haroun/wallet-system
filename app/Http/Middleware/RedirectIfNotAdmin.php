<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = 'admin'): Response
    {
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('admin.login');
        }

        // Additional check for admin user type
        $user = Auth::guard($guard)->user();
        if (!$user->userType || $user->userType->guard_name !== 'admin') {
            Auth::guard($guard)->logout();
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
