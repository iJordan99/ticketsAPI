<?php

namespace App\Policies\V1;

use App\Models\User;
use App\Permissions\V1\Abilities;

class AuthorTickets
{
    public function view(User $user): bool
    {
        return $user->tokenCan(Abilities::ViewAuthorTicket);
    }

    public function store(User $user): bool
    {
        return $user->tokenCan(Abilities::CreateAuthorTicket);
    }

    public function replace(User $user): bool
    {
        return $user->tokenCan(Abilities::ReplaceAuthorTicket);
    }

    public function update(User $user): bool
    {
        return $user->tokenCan(Abilities::UpdateAuthorTicket);
    }

    public function delete(User $user): bool
    {
        return $user->tokenCan(Abilities::DeleteTicket);
    }

}
