<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->validated('email'),
            $request->validated('password'),
            $request,
        );

        return ApiResponse::success([
            'access_token' => $result['accessToken'],
            'user' => new UserResource($result['user']),
        ], __('auth.login_success'))
            ->withCookie($this->authService->makeRefreshTokenCookie(
                $result['refreshToken'],
                $result['refreshTokenExpiresAt'],
                $request,
            ));
    }

    public function refresh(Request $request): JsonResponse
    {
        $result = $this->authService->refresh(
            $request->cookie(config('refresh_tokens.cookie_name')),
            $request,
        );

        return ApiResponse::success(
            ['access_token' => $result['accessToken']],
            __('auth.token_refreshed'),
        )->withCookie($this->authService->makeRefreshTokenCookie(
            $result['refreshToken'],
            $result['refreshTokenExpiresAt'],
            $request,
        ));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->cookie(config('refresh_tokens.cookie_name')));

        return ApiResponse::success(null, __('auth.logout_success'))
            ->withCookie($this->authService->forgetRefreshTokenCookie());
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(new UserResource($request->user()), __('auth.me_success'));
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        Password::sendResetLink($request->validated());

        // Same response whether or not the email is registered — otherwise
        // this endpoint becomes a way to enumerate accounts.
        return ApiResponse::success(null, __('auth.password_reset_sent'));
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password])->save();
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return ApiResponse::error(__('auth.password_reset_failed'), 422);
        }

        return ApiResponse::success(null, __('auth.password_reset_success'));
    }

    /**
     * Reached by clicking the link in the verification email — protected by
     * the `signed` route middleware, not a bearer token, since the browser
     * that opens this link has neither.
     */
    public function verifyEmail(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        abort_unless(hash_equals($hash, sha1($user->getEmailForVerification())), 403);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return redirect(rtrim(config('app.frontend_url'), '/').'/login?verified=1');
    }

    public function resendVerification(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return ApiResponse::error(__('auth.email_already_verified'), 422);
        }

        $request->user()->sendEmailVerificationNotification();

        return ApiResponse::success(null, __('auth.verification_sent'));
    }
}
