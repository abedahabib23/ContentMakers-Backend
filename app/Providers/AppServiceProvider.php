<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Applies everywhere Password::default() is used (registration,
        // password reset). uncompromised() fails open on network errors —
        // it never blocks a request just because the HIBP check timed out.
        Password::defaults(fn () => Password::min(10)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised());

        // The reset link must open the frontend's own form (only it can
        // collect the new password), not an API route.
        ResetPassword::createUrlUsing(fn ($notifiable, string $token) => sprintf(
            '%s/reset-password?token=%s&email=%s',
            rtrim(config('app.frontend_url'), '/'),
            $token,
            urlencode($notifiable->getEmailForPasswordReset()),
        ));

        // Every ability check ($user->can(...), the `permission` middleware,
        // future Policies) is granted through here first. Returning null
        // (permission missing) falls through to any registered Policy
        // instead of denying outright.
        Gate::before(fn (User $user, string $ability) => $user->hasPermissionTo($ability) ? true : null);
    }
}
