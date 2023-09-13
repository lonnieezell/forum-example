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
     * Returns a paginated list of posts for the thread.
     */
    public function forThread(int $threadId, int $perPage = 10)
    {
        $posts = $this->where('thread_id', $threadId)
            ->visible()
            ->orderBy('id', 'asc')
            ->paginate($perPage);

        return $this->withUsers($posts);
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
