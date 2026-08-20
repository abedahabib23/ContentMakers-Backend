<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\RolePolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

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

        // Role lives in the spatie/laravel-permission package, outside
        // App\Models, so Laravel's policy auto-discovery can't find
        // RolePolicy on its own.
        Gate::policy(Role::class, RolePolicy::class);

        // super_admin holds no permission rows at all — it bypasses every
        // Gate/Policy check globally, keyed off the `type` column (not off
        // holding a role). Returning null lets everyone else fall through
        // to normal permission/Policy evaluation.
        Gate::before(fn (User $user) => $user->isSuperAdmin() ? true : null);
    }
}
