<?php declare(strict_types=1);

namespace Tests\Support\Policies;

use App\Entities\User;
use App\Libraries\Policies\PolicyInterface;

class TestPolicy implements PolicyInterface
{
    public function before(User $user, string $permission): ?bool
    {
        if ($user->inGroup('developer')) {
            return true;
        }

        return null;
    }

    /**
     * Determines if the current user can create a new user.
     */
    public function create(User $user): bool
    {
        return $user->inGroup('admin');
    }

    /**
     * Determines if the current user can edit another user.
     */
    public function edit(User $user, User $targetUser): bool
    {
        return $user->inGroup('admin') &&
            ! $targetUser->inGroup('superadmin');
    }

    /**
     * Determines if the current user can delete another user.
     */
    public function delete(User $user, User $targetUser): bool
    {
        return $user->inGroup('superadmin') &&
            ! $targetUser->inGroup('superadmin');
    }
}
