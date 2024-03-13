<?php

namespace App\Libraries;

use App\Entities\User;
use App\Models\PostModel;
use App\Models\ThreadModel;

/**
 * Provides tools for invalidating caches and other cache-related tasks.
 */
class CacheUtils
{
    /**
     * Invalidates the cache for the given user's signature.
     * This also requires invalidating the thread/post caches
     * for any posts/threads that the user has made.
     */
    public static function invalidateSignatureCache(User $user): void
    {
        cache()->delete($user->cacheKey('-sig'));

        // Invalidate the cache for all posts authored by this user.
        $postIds = model(PostModel::class)->where('author_id', $user->id)->findColumn('id');
        if (is_array($postIds) && $postIds !== []) {
            foreach ($postIds as $postId) {
                cache()->deleteMatching('post-' . $postId . '*');
            }
        }

        // Invalidate the cache fo all threads authored by this user.
        $threadIds = model(ThreadModel::class)->where('author_id', $user->id)->findColumn('id');
        if (is_array($threadIds) && $threadIds !== []) {
            foreach ($threadIds as $threadId) {
                cache()->deleteMatching('thread-' . $threadId . '*');
            }
        }
    }
}
