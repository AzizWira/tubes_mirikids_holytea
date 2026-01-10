<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthenticated'], 401)
                : abort(401);
        }

        if (property_exists($user, 'is_active') && !$user->is_active) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'User inactive'], 403)
                : abort(403, 'User inactive');
        }

        if (!in_array($user->role, $roles, true)) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Forbidden'], 403)
                : abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
