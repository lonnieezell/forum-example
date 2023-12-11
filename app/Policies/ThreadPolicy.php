<?php

namespace App\Policies;

use App\Entities\Thread;
use App\Entities\User;
use App\Libraries\Policies\PolicyInterface;

class ThreadPolicy implements PolicyInterface
{
    /**
     * Determines if the current user can edit a thread.
     */
    public function edit(User $user, Thread $thread): bool
    {
        if (! service('policy')->checkCategoryPermissions($thread->category_id)) {
            return false;
        }

        return $user->can('threads.edit', 'moderation.threads')
            || $user->id === $thread->author_id;
    }

    /**
     * Determines if the current user can accept the answer for a thread.
     */
    public function manageAnswer(User $user, Thread $thread): bool
    {
        if (! service('policy')->checkCategoryPermissions($thread->category_id)) {
            return false;
        }

        return $user->can('threads.manageAnswer') || $user->id === $thread->author_id;
    }

    /**
     * Determines if the current user can delete a thread.
     */
    public function delete(User $user, Thread $thread): bool
    {
        if (! service('policy')->checkCategoryPermissions($thread->category_id)) {
            return false;
        }

        return $user->can('threads.delete', 'moderation.threads')
            || $user->id === $thread->author_id;
    }
}
