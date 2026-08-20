<?php

namespace App\Policies;

use App\Models\User;

class CenterSettingPolicy
{
    public function view(User $user): bool
    {
        return $user->can('center_settings.view');
    }

    public function update(User $user): bool
    {
        return $user->can('center_settings.update');
    }
}
