<?php

namespace App\Models;

use App\Concerns\HasAuthorsAndEditors;
use App\Concerns\ImpactsCategoryCounts;
use App\Concerns\ImpactsUserActivity;
use App\Entities\Post;
use CodeIgniter\Model;

class PostModel extends Model
{
    use ImpactsCategoryCounts;
    use ImpactsUserActivity;
    use HasAuthorsAndEditors;

    protected $table            = 'posts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Post::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id', 'thread_id', 'reply_to', 'author_id', 'editor_id', 'edited_at', 'edited_reason', 'body', 'ip_address', 'include_sig', 'visible', 'markup',
    ];
    protected $useTimestamps = true;
    protected $afterInsert   = ['incrementPostCount', 'touchThread', 'touchUser'];
    protected $afterDelete   = ['decrementPostCount'];
    protected $afterUpdate   = ['touchThread'];

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
    public function forThread(int $threadId, int $perPage = 10)
    {
        $posts = $this->where('thread_id', $threadId)
            ->visible()
            ->main()
            ->orderBy('id', 'asc')
            ->paginate($perPage);

        if (! $posts) {
            return [];
        }

        $posts = $this->withReplies($posts);

        return $this->withUsers($posts);
    }

    public function withReplies(array $posts, int $limit = 2): array
    {
        $postIds = array_map('intval', array_column($posts, 'id'));
        $replies = $this->getReplies($postIds, $limit);

        foreach ($posts as $post) {
            $post->replies = $replies[$post->id] ?? [];
        }

        return $posts;
    }

    public function getReplies(array $postIds, int $limit): array
    {
        $joinSubQuery = $this->builder('posts')
            ->select('reply_to, COUNT(*) AS reply_count')
            ->whereIn('reply_to', $postIds)
            ->groupBy('reply_to')
            ->getCompiledSelect();

        $subQuery = $this->builder('posts')
            ->select('*, ROW_NUMBER() OVER (PARTITION BY reply_to ORDER BY created_at DESC) AS reply_rank')
            ->whereIn('reply_to', $postIds);

        $result = $this->db
            ->newQuery()
            ->fromSubquery($subQuery, 'pr')
            ->select('pr.*, COALESCE(pc.reply_count, 0) AS reply_count')
            ->join('(' . $joinSubQuery . ') pc', 'pc.reply_to = pr.reply_to')
            ->where('pr.reply_rank <=', $limit)
            ->orderBy('pr.reply_to, pr.created_at', 'asc')
            ->get()
            ->getCustomResultObject(Post::class);

        $replies = [];

        foreach ($result as $row) {
            $replies[$row->reply_to][] = $row;
        }

        return $replies;
    }

    public function getAllReplies(int $postId)
    {

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
