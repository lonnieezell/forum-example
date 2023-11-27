<?php

namespace App\Concerns;

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\ThreadModel;
use App\Models\UserModel;
use Exception;

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
     *
     * @throws Exception
     */
    protected function decrementThreadCount(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        // The $data['id'] is always an array on delete
        $threads = $this->allowCallbacks(false)->onlyDeleted()->find($data['id']);

        foreach ($threads as $thread) {
            $postModel = model(PostModel::class);
            // Soft-delete all the posts for this thread
            $postModel->builder()
                ->update(
                    ['deleted_at' => $thread->deleted_at],
                    ['thread_id' => $thread->id, 'deleted_at' => null]
                );

            // Update post_count for every affected user
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->distinct()
                ->select('author_id')
                ->where('thread_id', $thread->id)
                ->where('deleted_at', $thread->deleted_at);
            // Run sub query
            $query = $this->db->newQuery()->fromSubquery($subQuery, 'authors')
                ->select('authors.author_id AS id, COALESCE(COUNT(posts.id), 0) AS post_count')
                ->join('posts', 'authors.author_id = posts.author_id AND posts.deleted_at IS NULL', 'left')
                ->groupBy('authors.author_id');

            $this->builder('users')
                ->setQueryAsData($query)
                ->onConstraint('id')
                ->updateBatch();

            // Update thread_count, post_count and last_thread_id for every affected category
            $query = $this->db->table('threads')
                ->select('category_id AS id, COALESCE(COUNT(id), 0) AS thread_count, COALESCE(SUM(post_count), 0) AS post_count, MAX(id) AS last_thread_id')
                ->where('category_id', $thread->category_id)
                ->where('deleted_at', null)
                ->groupBy('category_id');

            $this->builder('categories')
                ->setQueryAsData($query)
                ->onConstraint('id')
                ->updateBatch();

            // Decrement User thread count
            model(UserModel::class)->decrementStats($thread->author_id, 'thread_count');
        }

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

        // The $data['id'] is always an array on delete
        $posts = $this->allowCallbacks(false)->onlyDeleted()->find($data['id']);

        foreach ($posts as $post) {
            if ($post->reply_to !== null) {
                // If this is a reply post, we have to check if the parent is deleted too or not
                if ($parent = $this->allowCallbacks(false)->where('marked_as_deleted !=', null)->find($post->reply_to)) {
                    // Check if there are any not deleted replies
                    $repliesForParentCount = $this->where('reply_to', $parent->id)->countAllResults();
                    if ($repliesForParentCount === 0) {
                        // Set parent post as deleted, since there are no replies anymore
                        $this->builder()->update(['deleted_at' => $parent->marked_as_deleted], ['id' => $parent->id]);
                    }
                }
            } else {
                // Check if there are any replies for this post
                $repliesForParentCount = $this->where('reply_to', $post->id)->countAllResults();
                if ($repliesForParentCount > 0) {
                    // If there are replies for this post, mark it as deleted
                    $this->builder()->update(['deleted_at' => null, 'marked_as_deleted' => $post->deleted_at], ['id' => $post->id]);
                }
            }

            $threadModel = model(ThreadModel::class);
            // Get the thread for post
            $thread = $threadModel->allowCallbacks(false)->find($post->thread_id);
            // Check if we have to update the last_post_id in the thread
            if ($thread->last_post_id === $post->id) {
                $latestPost = $this->allowCallbacks(false)->where('thread_id', $post->thread_id)->orderBy('created_at', 'desc')->limit(1)->find();
                $threadModel->builder()->update(['last_post_id' => $latestPost->id], ['id' => $thread->id]);
            }

            // Decrement Category post count
            model(CategoryModel::class)->decrementStats($post->category_id, 'post_count');
            // Decrement Thread post count
            $threadModel->decrementStats($post->thread_id, 'post_count');
            // Decrement User post count
            model(UserModel::class)->decrementStats($post->author_id, 'post_count');
        }

        return $data;
    }
}
