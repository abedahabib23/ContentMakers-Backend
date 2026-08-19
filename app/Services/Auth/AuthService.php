<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\InvalidRefreshTokenException;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

class AuthService
{
    /**
     * @return array{user: User, accessToken: string, refreshToken: string, refreshTokenExpiresAt: Carbon}
     */
    public function login(string $email, string $password, Request $request): array
    {
        $accessToken = Auth::guard('api')->attempt([
            'email' => $email,
            'password' => $password,
        ]);

        if (! $accessToken) {
            throw new InvalidCredentialsException;
        }

        /** @var User $user */
        $user = Auth::guard('api')->user();

        $refresh = $this->issueRefreshToken($user, $request);

        return [
            'user' => $user,
            'accessToken' => $accessToken,
            'refreshToken' => $refresh['raw'],
            'refreshTokenExpiresAt' => $refresh['expiresAt'],
        ];
    }

    /**
     * @param  array{name: string, email: string, password: string}  $attributes
     * @return array{user: User, accessToken: string, refreshToken: string, refreshTokenExpiresAt: Carbon}
     */
    public function register(array $attributes, Request $request): array
    {
        $user = User::create($attributes);

        event(new Registered($user));

        $accessToken = Auth::guard('api')->login($user);
        $refresh = $this->issueRefreshToken($user, $request);

        return [
            'user' => $user,
            'accessToken' => $accessToken,
            'refreshToken' => $refresh['raw'],
            'refreshTokenExpiresAt' => $refresh['expiresAt'],
        ];
    }

    /**
     * @return array{user: User, accessToken: string, refreshToken: string, refreshTokenExpiresAt: Carbon}
     */
    public function refresh(?string $rawToken, Request $request): array
    {
        if (! $rawToken) {
            throw new InvalidRefreshTokenException;
        }

        $stored = RefreshToken::where('token_hash', RefreshToken::hash($rawToken))->first();

        if (! $stored) {
            throw new InvalidRefreshTokenException;
        }

        if ($stored->isRevoked()) {
            // This exact token was already rotated away — presenting it
            // again means it was copied/stolen. Kill every other active
            // token for this user so the thief (and the legitimate user)
            // both have to log in again.
            RefreshToken::where('user_id', $stored->user_id)
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);

            throw new InvalidRefreshTokenException;
        }

        if ($stored->isExpired()) {
            throw new InvalidRefreshTokenException;
        }

        /** @var User $user */
        $user = $stored->user;

        $refresh = $this->issueRefreshToken($user, $request);

        // Direct assignment, not update(['revoked_at' => ...]) — revoked_at
        // and replaced_by_id are deliberately absent from RefreshToken's
        // #[Fillable] list (nothing should mass-assign a revocation), which
        // means ->update() with those keys would silently no-op them.
        $stored->revoked_at = now();
        $stored->replaced_by_id = $refresh['model']->id;
        $stored->save();

        $accessToken = Auth::guard('api')->login($user);

        return [
            'user' => $user,
            'accessToken' => $accessToken,
            'refreshToken' => $refresh['raw'],
            'refreshTokenExpiresAt' => $refresh['expiresAt'],
        ];
    }

    public function logout(?string $rawToken): void
    {
        if ($rawToken) {
            RefreshToken::where('token_hash', RefreshToken::hash($rawToken))
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);
        }

        // Blacklists the access token that authenticated this request.
        Auth::guard('api')->logout();
    }

    public function makeRefreshTokenCookie(string $rawToken, Carbon $expiresAt, Request $request): SymfonyCookie
    {
        return cookie(
            name: config('refresh_tokens.cookie_name'),
            value: $rawToken,
            minutes: now()->diffInMinutes($expiresAt, absolute: true),
            path: '/api/auth',
            domain: null,
            secure: $request->secure(),
            httpOnly: true,
            raw: false,
            sameSite: config('refresh_tokens.same_site'),
        );
    }

    public function forgetRefreshTokenCookie(): SymfonyCookie
    {
        return Cookie::forget(config('refresh_tokens.cookie_name'), '/api/auth');
    }

    /**
     * @return array{raw: string, model: RefreshToken, expiresAt: Carbon}
     */
    private function issueRefreshToken(User $user, Request $request): array
    {
        $raw = bin2hex(random_bytes(32));
        $expiresAt = now()->addDays(config('refresh_tokens.ttl_days'));

        $model = RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => RefreshToken::hash($raw),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
            'ip_address' => $request->ip(),
            'expires_at' => $expiresAt,
        ]);

        return ['raw' => $raw, 'model' => $model, 'expiresAt' => $expiresAt];
    }
}
