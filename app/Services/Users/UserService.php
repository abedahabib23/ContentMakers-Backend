<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function list(): LengthAwarePaginator
    {
        return User::paginate();
    }

    /**
     * Accounts are only ever created by a super admin, never through public
     * self-registration, so there's no one to send a verification link to
     * confirm ownership of the email — the admin already vouched for it.
     *
     * @param  array{name: string, email: string, password: string, type: \App\Enums\UserType}  $attributes
     */
    public function create(array $attributes): User
    {
        $user = User::create($attributes);

        // email_verified_at is deliberately absent from User's $fillable —
        // nothing should mass-assign verification status.
        $user->forceFill(['email_verified_at' => now()])->save();

        return $user;
    }

    /**
     * @param  array{name?: string, email?: string, password?: string, type?: \App\Enums\UserType}  $attributes
     */
    public function update(User $user, array $attributes): User
    {
        $user->update($attributes);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
