<?php

namespace App\Policies;

use App\Entities\Post;
use App\Entities\User;
use App\Libraries\Policies\PolicyInterface;

class PostPolicy implements PolicyInterface
{
    /**
     * Determines if the current user can edit a thread.
     */
    public function edit(User $user, Post $post): bool
    {
        return $user->can('posts.edit') || $user->id === $post->author_id;
    }
}
