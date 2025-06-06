<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticateAdminApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = 'admin-api'): Response
    {
        if (!Auth::guard($guard)->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::guard($guard)->user();
        if (!$user->userType || $user->userType->guard_name !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
