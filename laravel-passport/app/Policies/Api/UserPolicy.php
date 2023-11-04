<?php

namespace App\Policies\Api;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return ($user->uuid === $model->uuid || $user->role === User::USER_ROLE_ADMIN) ? true : false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return false; //$user->role === User::USER_ROLE_ADMIN;
    }
}
