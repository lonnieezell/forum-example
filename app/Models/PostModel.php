<?php

namespace App\Models;

use App\Concerns\HasAuthorsAndEditors;
use App\Concerns\HasImages;
use App\Concerns\ImpactsCategoryCounts;
use App\Concerns\ImpactsUserActivity;
use App\Entities\Post;
use CodeIgniter\Model;

class PostModel extends Model
{
    use ImpactsCategoryCounts;
    use ImpactsUserActivity;
    use HasAuthorsAndEditors;
    use HasImages;

    protected $table            = 'posts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Post::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id', 'thread_id', 'reply_to', 'author_id', 'editor_id', 'edited_at', 'edited_reason', 'body', 'ip_address', 'include_sig', 'visible', 'markup',
    ];
    protected $useTimestamps        = true;
    protected $cleanValidationRules = false;
    protected $afterInsert          = ['updatePostImages', 'incrementPostCount', 'touchThread', 'touchUser'];
    protected $afterDelete          = ['decrementPostCount'];
    protected $afterUpdate          = ['updatePostImages', 'touchThread'];

    /**
     * Scope method to only return visible posts.
     *
     * Example:
     *  $posts = model(PostModel::class)->visible()->findAll();
     */
    public function visible()
    {
        return $this->where('visible', true);
    }

    /**
     * Scope method to only return top level posts.
     *
     * Example:
     *  $posts = model(PostModel::class)->main()->findAll();
     */
    public function main()
    {
        return $this->where('reply_to', null);
    }

    /**
     * Returns a paginated list of posts for the thread.
     */
    public function forThread(int $threadId, int $perPage = 10, ?int $page = null)
    {
        $posts = $this->where('thread_id', $threadId)
            ->visible()
            ->main()
            ->orderBy('id', 'asc')
            ->paginate($perPage, 'default', $page);

        if (! $posts) {
            return [];
        }

        $posts = $this->withReplies($posts);

        return $this->withUsers($posts);
    }

    /**
     * Adds last X replies for every post.
     */
    public function withReplies(array $posts, int $limit = 2): array
    {
        $postIds      = array_map('intval', array_column($posts, 'id'));
        $replies      = $this->getReplies($postIds, $limit);
        $repliesCount = $this->getRepliesCount($postIds);

        foreach ($posts as $post) {
            $post->replies       = $replies[$post->id] ?? [];
            $post->replies_count = $repliesCount[$post->id] ?? 0;
        }

        return $posts;
    }

    /**
     * Get latest replies for posts by given IDs.
     */
    public function getReplies(array $postIds, int $limit): array
    {
        // Used to get last replies using rank
        $subQuery = $this->builder()
            ->select('*, ROW_NUMBER() OVER (PARTITION BY reply_to ORDER BY created_at DESC) AS reply_rank')
            ->whereIn('reply_to', $postIds);

        $posts = $this->db
            ->newQuery()
            ->fromSubquery($subQuery, 'pr')
            ->where('pr.reply_rank <=', $limit)
            ->orderBy('pr.reply_to, pr.created_at', 'asc')
            ->get()
            ->getCustomResultObject(Post::class);

        $subQuery->resetQuery();

        $posts = $this->withUsers($posts);

        $replies = [];

        foreach ($posts as $post) {
            $replies[$post->reply_to][] = $post;
        }

        return $replies;
    }

    /**
     * Get number of replies for every given post id.
     */
    public function getRepliesCount(array $postIds): array
    {
        $results = $this->builder()
            ->select('reply_to, COUNT(*) AS reply_count')
            ->whereIn('reply_to', $postIds)
            ->groupBy('reply_to')
            ->get()
            ->getResultArray();

        return array_column($results, 'reply_count', 'reply_to');
    }

    /**
     * Get all replies for given post.
     */
    public function getAllReplies(int $postId): array
    {
        $posts = $this->where('reply_to', $postId)->findAll();

        return $this->withUsers($posts);
    }

    /**
     * Determine the page based on the postId.
     */
    public function getPageNumberForPost(int $threadId, int $postId, int $perPage = 10): ?int
    {
        $result = $this->builder()
            ->select('COUNT(*) AS position')
            ->where('thread_id', $threadId)
            ->where('id <=', $postId)
            ->where('reply_to', null)
            ->get()
            ->getRow();

        if ($result === null) {
            return null;
        }

        return (int) ceil($result->position / $perPage);
    }

    /**
     * Get all users ids for people who posted a reply to a given thread.
     */
    public function getAuthorIds(int $threadId, array $excludeUsers = []): array
    {
        $authors = $this->builder()
            ->distinct()
            ->select('author_id')
            ->where('thread_id', $threadId)
            ->whereNotIn('author_id', $excludeUsers)
            ->where('visible', true)
            ->get()
            ->getResultArray();

        return array_map('intval', array_column($authors, 'author_id'));
    }

    /**
     * Get all users ids for people who posted a reply to a given post.
     */
    public function getReplyAuthorIds(int $replyTo, array $excludeUsers = []): array
    {
        $authors = $this->builder()
            ->distinct()
            ->select('author_id')
            ->where('reply_to', $replyTo)
            ->whereNotIn('author_id', $excludeUsers)
            ->where('visible', true)
            ->get()
            ->getResultArray();

        return array_map('intval', array_column($authors, 'author_id'));
    }

    /**
     * Update the category's last_thread_id.
     */
    protected function touchThread(array $data)
    {
        if (empty($data['data']['thread_id'])) {
            return $data;
        }

        model(ThreadModel::class)
            ->where('id', $data['data']['thread_id'])
            ->set('last_post_id', $data['id'])
            ->update();

        return $data;
    }
}
