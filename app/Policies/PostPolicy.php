<?php

namespace App\Policies;

use App\Entities\Post;
use App\Entities\User;
use App\Libraries\Policies\PolicyInterface;

class PostPolicy implements PolicyInterface
{
    /**
     * Determines if the current user can edit a post.
     */
    public function edit(User $user, Post $post): bool
    {
        if (! service('policy')->checkCategoryPermissions($post->category_id)) {
            return false;
        }

        return $user->can('posts.edit') || $user->id === $post->author_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->can('posts.delete') || $user->id === $post->author_id;
    }
}
