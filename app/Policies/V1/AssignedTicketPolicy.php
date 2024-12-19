<?php

namespace App\Policies\V1;


use App\Models\User;
use App\Permissions\V1\Abilities;

class AssignedTicketPolicy
{
    public function view(User $user)
    {
        return $user->tokenCan(Abilities::ViewAssignedTickets);
    }

    public function show(User $user)
    {
        return $user->tokenCan(Abilities::ShowAssignedTicket);
    }
}
