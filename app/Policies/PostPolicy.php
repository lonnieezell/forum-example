<?php

namespace App\Policies;

use App\Entities\Post;
use App\Entities\User;
use App\Libraries\Policies\PolicyInterface;
use CodeIgniter\Exceptions\ModelException;
use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Exceptions\LogicException;
use Config\TrustLevels;
use InvalidArgumentException;
use RuntimeException;

class PostPolicy implements PolicyInterface
{
    /**
     * Users might be restricted from creating posts above a certain limit,
     * based on their trust level and the trust level settings.
     *
     * Additionally, their role or user permissions should be checked.
     */
    public function create(User $user): bool
    {
        // Does the user have the trust level to create posts?
        // Or have they exceeded the maximum number of posts per the trust level?
        if (! $user->canTrustTo('reply') && $user->post_count >= TrustLevels::POST_THRESHOLD) {
            return false;
        }

        return $user->can('posts.create');
    }

    /**
     * Determines if the current user can edit a post.
     */
    public function edit(User $user, Post $post): bool
    {
        $isOwnPost = $user->id === $post->author_id;

        if ($isOwnPost &&
            ! $user->canTrustTo('edit-own') &&
            $post->created_at->isBefore(Time::now()->subHours(24))
        ) {
            return false;
        }

        if (! service('policy')->checkCategoryPermissions($post->category_id)) {
            return false;
        }

        return $user->can('posts.edit') || $isOwnPost;
    }

    public function delete(User $user, Post $post): bool
    {
        if (! service('policy')->checkCategoryPermissions($post->category_id)) {
            return false;
        }

        return $user->can('posts.delete') || $user->id === $post->author_id;
    }
}
