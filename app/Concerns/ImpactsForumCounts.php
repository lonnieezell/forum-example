<?php

namespace App\Concerns;

use App\Entities\Forum;
use App\Models\ForumModel;
use App\Models\ThreadModel;
use App\Models\UserModel;

trait ImpactsForumCounts
{
    /**
     * Updates the forum's discussion count.
     */
    protected function incrementThreadCount(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        $thread = $this->find($data['id']);
        $forumModel = model(ForumModel::class);
        $userModel = model(UserModel::class);

        // Increment Forum thread count
        $forum = $forumModel->find($thread->forum_id);
        $forum->thread_count++;

        $forumModel->save($forum);

        // Decrement User thread count
        $user = $userModel->find($thread->author_id);
        $user->thread_count++;

        $userModel->save($user);

        return $data;
    }

    /**
     * Updates the forum's discussion count.
     */
    protected function decrementThreadCount(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        $thread = $this->find($data['id']);

        $forumModel = model(ForumModel::class);
        $userModel = model(UserModel::class);

        // Decrement Forum thread count
        $forum = $forumModel->find($thread->forum_id);
        $forum->thread_count--;

        $forumModel->save($forum);

        // Decrement User thread count
        $user = $userModel->find($thread->author_id);
        $user->thread_count--;

        $userModel->save($user);

        return $data;
    }

    /**
     * Updates the forum's post count.
     */
    protected function incrementPostCount(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        $post = $this->find($data['id']);
        $forumModel = model(ForumModel::class);
        $threadModel = model(ThreadModel::class);
        $userModel = model(UserModel::class);

        // Increment Forum post count
        $forum = $forumModel->find($post->forum_id);
        $forum->post_count++;

        $forumModel->save($forum);

        // Increment Thread post count
        $thread = $threadModel->find($post->thread_id);
        $thread->post_count++;

        $threadModel->save($thread);

        // Increment User post count
        $user = $userModel->find($post->author_id);
        $user->post_count++;

        $userModel->save($user);

        return $data;
    }

    /**
     * Updates the forum's post count.
     */
    protected function decrementPostCount(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        $post = $this->find($data['id']);
        $forumModel = model(ForumModel::class);
        $threadModel = model(ThreadModel::class);
        $userModel = model(UserModel::class);

        // Decrement Forum post count
        $forum = $forumModel->find($post->forum_id);
        $forum->post_count--;

        $forumModel->save($forum);

        // Decrement Thread post count
        $thread = $threadModel->find($post->thread_id);
        $thread->post_count--;

        $threadModel->save($thread);

        // Decrement User post count
        $user = $userModel->find($post->author_id);
        $user->post_count--;

        $userModel->save($user);

        return $data;
    }
}
