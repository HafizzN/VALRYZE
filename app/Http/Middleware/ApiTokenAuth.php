<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract Bearer token or fallback to api_token input parameter
        $token = $request->bearerToken() ?? $request->input('api_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token otentikasi tidak ditemukan.'
            ], 401);
        }

        // Find user by api_token
        $user = User::where('api_token', $token)->first();

        if (!$user || $user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Token otentikasi tidak valid atau akun dinonaktifkan.'
            ], 401);
        }

        // Authenticate the user for the current request
        Auth::login($user);

        return $next($request);
    }
}
