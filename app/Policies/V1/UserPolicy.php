<?php

namespace App\Policies\V1;

use App\Models\User;
use App\Permissions\V1\Abilities;

class UserPolicy
{
    public function view(User $user): bool
    {
        return $user->tokenCan(Abilities::ViewAuthor);
    }

    public function show(User $user): bool
    {
        return $user->tokenCan(Abilities::ViewAuthor);
    }

    /**
     * Determine whether the user can create models.
     */
    public function store(User $user): bool
    {
        return $user->tokenCan(Abilities::CreateUser);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function replace(User $user, User $model): bool
    {
        return $user->tokenCan(Abilities::ReplaceUser);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->tokenCan(Abilities::UpdateUser);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->tokenCan(Abilities::DeleteUser);
    }

}
