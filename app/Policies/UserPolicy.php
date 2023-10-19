<?php

namespace App\Policies;

use App\Entities\User;
use App\Libraries\Policies\PolicyInterface;

class UserPolicy implements PolicyInterface
{
    /**
     * Determines if the current user can delete a user.
     */
    public function delete(User $user, User $targetUser): bool
    {
        return $user->can('users.delete') || $user->id === $targetUser->id;
    }
}
