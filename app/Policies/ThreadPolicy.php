<?php

namespace App\Policies;

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
    public function edit(): bool
    {
        return true;
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
