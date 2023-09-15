<?php

namespace App\Policies;

use App\Entities\Thread;
use App\Entities\User;
use App\Libraries\Policies\PolicyInterface;

class ThreadPolicy implements PolicyInterface
{
    /**
     * Determines if the current user can create a new thread.
     */
    public function create(): bool
    {
        return true;
    }

    /**
     * Determines if the current user can edit a thread.
     */
    public function edit(User $user, Thread $thread): bool
    {
        return $user->can('threads.edit') || $user->id === $thread->author_id;
    }

    /**
     * Determines if the current user can delete a thread.
     */
    public function delete(): bool
    {
        return true;
    }

    /**
     * Determines if the current user can reply to a thread.
     */
    public function reply(): bool
    {
        return true;
    }

    /**
     * Determines if the current user can view a thread.
     */
    public function view(): bool
    {
        return true;
    }
}
