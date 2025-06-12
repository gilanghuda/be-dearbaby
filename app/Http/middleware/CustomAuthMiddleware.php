<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CustomAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try{   
        $apiToken = $request->cookie('api_token');
                if (!$apiToken) {
                    return response()->json([`message' => 'API token is required =${apiToken}`], 401);
                } 

                $user = User::where('api_token', $apiToken)->first();
                if (!$user) {
                    return response()->json(['message' => `Invalid API token. = ${apiToken}`], 401);
                }
                $request->merge(['auth_user' => $user]);
                $request->setUserResolver(fn() => $user);
                return $next($request);
        }
        catch (\Exception $e) {
        return response()->json(['message' => 'Server error'], 500);
    }
    }
}
