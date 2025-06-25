<?php

namespace App\Policies\V1;

use App\Models\Ticket;
use App\Models\User;
use App\Permissions\V1\Abilities;

class TicketPolicy
{
    public function show(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::ViewAuthorTicket)) {
            return true;
        } else if ($user->tokenCan(Abilities::ViewOwnTicket)) {
            return $user->id === $ticket->user_id;
        }

        return false;
    }

    public function store(User $user): bool
    {
        return $user->tokenCan(Abilities::CreateAuthorTicket) ||
            $user->tokenCan(Abilities::CreateOwnTicket);
    }

    public function replace(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::ReplaceAuthorTicket)) {
            return true;
        } else if ($user->tokenCan(Abilities::ReplaceOwnTicket)) {
            return $user->id === $ticket->user_id;
        }

        return false;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::UpdateAuthorTicket)) {
            return true;
        } else if ($user->tokenCan(Abilities::UpdateOwnTicket)) {
            return $user->id === $ticket->user_id;
        }

        return false;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::DeleteTicket)) {
            return true;
        } else if ($user->tokenCan(Abilities::DeleteOwnTicket)) {
            return $user->id === $ticket->user_id;
        }

        return false;
    }

    public function assign(User $user): bool
    {
        return $user->tokenCan(Abilities::AssignEngineer);

    }

    public function comment(User $user)
    {
        return $user->tokenCan(Abilities::CommentOnTicket);
    }

    public function viewAssigned(User $user): bool
    {
        return $user->tokenCan(Abilities::ViewAssignedTickets);
    }
}
