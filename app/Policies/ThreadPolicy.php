<?php

namespace App\Policies;

use App\Entities\Thread;
use App\Entities\User;
use App\Libraries\Policies\PolicyInterface;
use CodeIgniter\I18n\Time;
use Config\TrustLevels;

class ThreadPolicy implements PolicyInterface
{
    public function create(User $user): bool
    {
        $hasTrust = $user->canTrustTo('start-discussion');
        if (! $hasTrust && $user->thread_count >= TrustLevels::THREAD_THRESHOLD) {
            return false;
        }

        return $user->can('threads.create');
    }

    /**
     * Determines if the current user can edit a thread.
     */
    public function edit(User $user, Thread $thread): bool
    {
        $isOwnThread = $user->id === $thread->author_id;

        // If the user doesn't have the trust level to edit their own threads after 24 hours,
        // and the thread is older than 24 hours, they can't edit it.
        if ($isOwnThread &&
            ! $user->canTrustTo('edit-own') &&
            $thread->created_at->isBefore(Time::now()->subHours(24))
        ) {
            return false;
        }

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

    /**
     * Determines if the current user can attach a file to threads/posts.
     */
    public function uploadImage(User $user): bool
    {
        // Is the image upload feature enabled?
        if (! config('ImageUpload')->enabled) {
            return false;
        }

        // Otherwise, check if the user has the permission to upload images.
        return $user->canTrustTo('attach');
    }
}
