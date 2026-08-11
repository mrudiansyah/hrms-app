<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ApiCustomAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Check for Bearer Token in authorization header
        $authHeader = $request->header('Authorization');
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $decoded = base64_decode($token, true);
            if ($decoded !== false) {
                $parts = explode('|', $decoded);
                if (count($parts) === 3) {
                    list($userId, $timestamp, $signature) = $parts;
                    // Verify signature
                    $expectedSignature = hash_hmac('sha256', $userId . '|' . $timestamp, config('app.key'));
                    if (hash_equals($expectedSignature, $signature)) {
                        // Check expiry (e.g., 30 days)
                        if (time() - $timestamp < 30 * 24 * 60 * 60) {
                            $user = User::find($userId);
                            if ($user) {
                                Auth::login($user);
                                return $next($request);
                            }
                        }
                    }
                }
            }
        }

        // 2. Check for username/password in JSON body
        if ($request->isJson() && $request->has('username') && $request->has('password')) {
            $credentials = [
                'email' => $request->json('username'),
                'password' => $request->json('password'),
            ];
            if (Auth::once($credentials)) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
