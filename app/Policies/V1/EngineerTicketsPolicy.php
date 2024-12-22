<?php

namespace App\Policies\V1;


use App\Models\User;
use App\Permissions\V1\Abilities;

class EngineerTicketsPolicy
{
    public function view(User $user): bool
    {
        return $user->tokenCan(Abilities::ViewAssignedTickets);
    }

    public function show(User $user): bool
    {
        return $user->tokenCan(Abilities::ViewEngineerTickets);
    }
}
