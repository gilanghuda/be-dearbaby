<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiToken = $request->query('api_token');
        if (!$apiToken) {
            return response()->json(['message' => 'API token is required.'], 401);
        }

        $user = User::where('api_token', $apiToken)->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid API token.'], 401);
        }

        // Set user ke request agar bisa diakses di controller
        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}
