<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $apiToken = $request->query('api_token');
        if (!$apiToken) {
            return response()->json(['message' => 'API token is required.'], 401);
        }

        $user = User::where('api_token', $apiToken)->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid API token.'], 401);
        }

        if ($user->family_role !== $role) {
            return response()->json(['message' => 'Forbidden. Insufficient role.'], 403);
        }

        // Optionally, set user to request for controller access
        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}
