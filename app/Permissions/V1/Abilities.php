<?php


namespace App\Permissions\V1;

use App\Models\User;

final class Abilities
{
    public const CreateTicket = 'ticket:create';
    public const UpdateTicket = 'ticket:update';
    public const ReplaceTicket = 'ticket:replace';
    public const DeleteTicket = 'ticket:delete';

    public const UpdateOwnTicket = 'ticket:own:update';
    public const DeleteOwnTicket = 'ticket:own:delete';
    const CreateOwnTicket = 'ticket:own:create';

    public const CreateUser = 'user:create';
    public const UpdateUser = 'user:update';
    public const ReplaceUser = 'user:replace';
    public const DeleteUser = 'user:delete';

    public const FetchUser = 'user:fetch';

    public static function getAbilities(User $user)
    {
        if ($user->is_admin) {
            return [
                self::UpdateTicket,
                self::CreateTicket,
                self::CreateUser,
                self::ReplaceTicket,
                self::UpdateUser,
                self::DeleteTicket,
                self::ReplaceUser,
                self::DeleteUser,
                self::FetchUser
            ];
        } else {
            return [
                self::UpdateOwnTicket,
                self::DeleteOwnTicket,
                self::CreateOwnTicket
            ];
        }
    }
}
