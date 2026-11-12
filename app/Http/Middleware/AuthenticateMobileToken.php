<?php

namespace App\Http\Middleware;

use App\Models\MobileApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMobileToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainTextToken = $request->bearerToken();

        if (!$plainTextToken) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $token = MobileApiToken::with('user')
            ->where('token_hash', hash('sha256', $plainTextToken))
            ->where('expires_at', '>', now())
            ->first();

        if (!$token || !$token->user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!$token->user->hasVerifiedEmail()) {
            $token->delete();
            return response()->json(['message' => 'Email akun HRMS belum diverifikasi.'], 403);
        }

        if ((int) $token->user->locked_status === 1) {
            $token->delete();
            return response()->json(['message' => 'Akun HRMS dikunci. Hubungi administrator.'], 403);
        }

        $passwordExpiryEnabled = DB::table('tb_utilities')
            ->where('atribut', 'expired_password')
            ->where('status', '1')
            ->exists();

        if ($passwordExpiryEnabled && $token->user->expired_date && $token->user->expired_date < now()->toDateString()) {
            $token->delete();
            return response()->json([
                'message' => 'Password HRMS sudah kedaluwarsa. Silakan ubah melalui HRMS web.',
            ], 403);
        }

        $now = now();
        $token->forceFill([
            'last_used_at' => $now,
            'expires_at' => $now->copy()->addMinutes(30),
        ])->save();
        $request->attributes->set('mobile_api_token', $token);
        $request->setUserResolver(static fn () => $token->user);

        return $next($request);
    }
}
