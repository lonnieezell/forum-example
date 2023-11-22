<?php

namespace App\Concerns;

use App\Models\PostModel;
use App\Models\ThreadModel;
use CodeIgniter\Entity\Entity;

trait HasThreadsAndPosts
{
    /**
     * Adds the author and editor to each post.
     */
    public function withThreadsAndPosts(array|Entity|null $records): array|Entity|null
    {
        if (empty($records)) {
            return $records;
        }

        $wasSingle = ! is_array($records);
        if (! is_array($records)) {
            $records = [$records];
        }

        $threadIds = $threads = [];
        $postIds   = $posts   = [];

        foreach ($records as $record) {
            if ($record->resource_type === 'thread') {
                $threadIds[] = $record->resource_id;
            } else {
                $postIds[] = $record->resource_id;
            }
        }

        if ($threadIds !== []) {
            $threads = model(ThreadModel::class)
                ->threads()
                ->withDeleted()
                ->withTags()
                ->whereIn('threads.id', array_unique($threadIds))
                ->findAll();

            $threads = $this->withUsers($threads);
            $threads = array_column($threads, null, 'id');
        }

        if ($postIds !== []) {
            $posts = model(PostModel::class)
                ->posts()
                ->withDeleted()
                ->whereIn('posts.id', array_unique($postIds))
                ->findAll();

            $posts = $this->withUsers($posts);
            $posts = array_column($posts, null, 'id');
        }

        // Add the resource (thread or post) to the records
        array_walk($records, static function (&$entry) use ($threads, $posts) {
            if ($entry->resource_type === 'thread') {
                $entry->resource = $threads[$entry->resource_id] ?? null;
            } else {
                $entry->resource = $posts[$entry->resource_id] ?? null;
            }
        });

        return $wasSingle
            ? array_shift($records)
            : $records;
    }
}
