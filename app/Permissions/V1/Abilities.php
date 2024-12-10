<?php


namespace App\Permissions\V1;

use App\Models\User;

final class Abilities
{
    public const CreateAuthorTicket = 'author:ticket:create';
    public const UpdateAuthorTicket = 'author:ticket:update';
    public const ReplaceAuthorTicket = 'author:ticket:replace';
    public const ViewAuthorTicket = 'author:ticket:view';
    public const ViewAuthor = 'author:view';
    public const ReplaceOwnTicket = 'ticket:own:replace';
    public const DeleteTicket = 'ticket:delete';
    public const UpdateOwnTicket = 'ticket:own:update';
    public const DeleteOwnTicket = 'ticket:own:delete';
    public const CreateOwnTicket = 'ticket:own:create';
    public const ViewOwnTicket = 'ticket:own:view';
    public const CreateUser = 'user:create';
    public const UpdateUser = 'user:update';
    public const ReplaceUser = 'user:replace';
    public const DeleteUser = 'user:delete';

    public static function getAbilities(User $user): array
    {
        if ($user->is_admin) {
            return [
                self::ViewAuthorTicket,
                self::UpdateAuthorTicket,
                self::CreateAuthorTicket,
                self::CreateUser,
                self::ReplaceAuthorTicket,
                self::UpdateUser,
                self::DeleteTicket,
                self::ReplaceUser,
                self::DeleteUser,
                self::ViewAuthor
            ];
        } else {
            return [
                self::UpdateOwnTicket,
                self::DeleteOwnTicket,
                self::CreateOwnTicket,
                self::ViewOwnTicket,
                self::ReplaceOwnTicket,
            ];
        }
    }
}
