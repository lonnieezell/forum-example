<?php

namespace App\Policies;

use App\Entities\User;
use App\Libraries\Policies\PolicyInterface;

class UserPolicy implements PolicyInterface
{
    /**
     * Determines if the current user can delete a user.
     */
    public function changePassword(User $user, User $targetUser): bool
    {
        return $user->can('users.changePassword') || $user->id === $targetUser->id;
    }

    /**
     * Determines if the current user can change 2FA settings for a user.
     */
    public function twoFactorAuthEmail(User $user, User $targetUser): bool
    {
        return $user->can('users.twoFactorAuthEmail') || $user->id === $targetUser->id;
    }

    /**
     * Determines if the current user can delete a user.
     */
    public function delete(User $user, User $targetUser): bool
    {
        return $user->can('users.delete') || $user->id === $targetUser->id;
    }
}
