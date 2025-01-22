<?php

namespace App\Http\Middleware;

use App\Exceptions\BaseException;
use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws BaseException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            throw new BaseException("Unauthenticated", 401, ['Token expirado']);
        }

        $personalAccessToken = PersonalAccessToken::findToken($token);

        if (!$personalAccessToken || !$personalAccessToken->tokenable instanceof Admin) {
            throw new BaseException("Unauthenticated", 401, ['Token expirado']);
        }

        return $next($request);
    }
}
