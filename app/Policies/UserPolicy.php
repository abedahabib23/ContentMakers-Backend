<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    public function view(User $user, User $target): bool
    {
        return $user->can('users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    /**
     * A user may always update their own profile; anyone else needs
     * users.update. Checked here rather than left to Gate::before, since
     * that only short-circuits on an exact ability-name match and this
     * policy's ability is "update", not "users.update".
     */
    public function update(User $user, User $target): bool
    {
        return $user->id === $target->id || $user->can('users.update');
    }

    /**
     * Can't delete yourself, and can't delete a super_admin — even someone
     * holding users.delete shouldn't be able to remove the account that
     * bypasses every other check.
     */
    public function delete(User $user, User $target): bool
    {
        return ! $user->is($target) && ! $target->isSuperAdmin() && $user->can('users.delete');
    }
}
