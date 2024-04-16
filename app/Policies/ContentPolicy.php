<?php

namespace App\Policies;

use App\Entities\Post;
use App\Entities\Thread;
use App\Entities\User;
use App\Libraries\Policies\PolicyInterface;

class ContentPolicy implements PolicyInterface
{
    /**
     * Determines if the current report a thread/post
     */
    public function report(User $user, Post|Thread $record): bool
    {
        // Can't report your own content.
        if ($user->id == $record->author_id) {
            return false;
        }

        // Otherwise, base it on the user's trust level.
        return $user->canTrustTo('report');
    }
}
