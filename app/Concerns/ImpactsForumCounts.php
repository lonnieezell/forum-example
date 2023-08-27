<?php

namespace App\Concerns;

use App\Entities\Forum;
use App\Models\ForumModel;
use App\Models\ThreadModel;

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
        $forum = model(ForumModel::class)->find($thread->forum_id);
        $forum->thread_count++;

        model(ForumModel::class)->save($forum);

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
        $forum = model(ForumModel::class)->find($thread->forum_id);
        $forum->thread_count--;

        model(ForumModel::class)->save($forum);

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

        // Increment Forum post count
        $forum = $forumModel->find($post->forum_id);
        $forum->post_count++;

        $forumModel->save($forum);

        // Increment Thread post count
        $thread = $threadModel->find($post->thread_id);
        $thread->post_count++;

        $threadModel->save($thread);

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

        $forum = model(ForumModel::class)->find($post->forum_id);
        $forum->post_count--;

        model(ForumModel::class)->save($forum);

        return $data;
    }
}
