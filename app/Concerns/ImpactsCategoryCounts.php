<?php

namespace App\Concerns;

use App\Models\CategoryModel;
use App\Models\ThreadModel;
use App\Models\UserModel;

trait ImpactsCategoryCounts
{
    /**
     * Updates the category's discussion count.
     */
    protected function incrementThreadCount(array $data): bool|array
    {
        if (! $data['result']) {
            return false;
        }

        $thread = $this->allowCallbacks(false)->find($data['id']);

        // Increment Category thread count
        model(CategoryModel::class)->incrementStats($thread->category_id, 'thread_count');
        // Increment User thread count
        model(UserModel::class)->incrementStats($thread->author_id, 'thread_count');

        return $data;
    }

    /**
     * Updates the category's discussion count.
     */
    protected function decrementThreadCount(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        $thread = $this->allowCallbacks(false)->onlyDeleted()->find($data['id']);

        // Decrement Category thread count
        model(CategoryModel::class)->decrementStats($thread[0]->category_id, 'thread_count');
        // Decrement User thread count
        model(UserModel::class)->decrementStats($thread[0]->author_id, 'thread_count');

        return $data;
    }

    /**
     * Updates the category's post count.
     */
    protected function incrementPostCount(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        $post = $this->allowCallbacks(false)->find($data['id']);

        // Increment Category post count
        model(CategoryModel::class)->incrementStats($post->category_id, 'post_count');
        // Increment Thread post count
        model(ThreadModel::class)->incrementStats($post->thread_id, 'post_count');
        // Increment User post count
        model(UserModel::class)->incrementStats($post->author_id, 'post_count');

        return $data;
    }

    /**
     * Updates the category's post count.
     */
    protected function decrementPostCount(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        $post = $this->allowCallbacks(false)->onlyDeleted()->find($data['id']);

        // Decrement Category post count
        model(CategoryModel::class)->decrementStats($post[0]->category_id, 'post_count');
        // Decrement Thread post count
        model(ThreadModel::class)->decrementStats($post[0]->thread_id, 'post_count');
        // Decrement User post count
        model(UserModel::class)->decrementStats($post[0]->author_id, 'post_count');

        return $data;
    }
}
