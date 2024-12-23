<?php

namespace App\Policies\V1;

use App\Models\User;
use App\Permissions\V1\Abilities;

class EngineerPolicy
{
    public function view(User $user)
    {
        return $user->tokenCan(Abilities::ViewEngineer);
    }

    public function show(User $user)
    {
        return $user->tokenCan(Abilities::ShowEngineer);
    }

    public function store(User $user)
    {
        return $user->tokenCan(Abilities::StoreEngineer);
    }
}
