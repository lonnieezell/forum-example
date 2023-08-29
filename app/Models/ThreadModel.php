<?php

namespace App\Models;

use App\Concerns\HasAuthorsAndEditors;
use App\Concerns\ImpactsForumCounts;
use App\Concerns\Sluggable;
use App\Entities\Thread;
use CodeIgniter\Model;

class ThreadModel extends Model
{
    use Sluggable;
    use ImpactsForumCounts;
    use HasAuthorsAndEditors;

    protected $DBGroup          = 'default';
    protected $table            = 'threads';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = Thread::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'forum_id', 'title', 'slug', 'body', 'author_id', 'editor_id', 'edited_at', 'edited_reason', 'views', 'closed', 'sticky', 'visible', 'last_post_id', 'post_count', 'answer_post_id', 'markup',
    ];

    protected $beforeInsert = ['generateSlug'];
    protected $afterInsert = ['incrementThreadCount', 'touchForum'];
    protected $afterDelete = ['decrementThreadCount'];
    protected $afterUpdate = ['touchForum'];

    /**
     * Scope method to only return open threads.
     *
     * Example:
     *  $threads = model(ThreadModel::class)->open()->findAll();
     */
    public function open()
    {
        return $this->where('threads.closed', false);
    }

    /**
     * Scope method to only return open threads.
     *
     * Example:
     *  $threads = model(ThreadModel::class)->open()->findAll();
     */
    public function visible()
    {
        return $this->where('threads.visible', true);
    }

    /**
     * Returns a paginated list of threads for the forum.
     */
    public function forList(array $params = [])
    {
        $selects = [
            'threads.*',
            'forums.title as forum_title',
            'forums.slug as forum_slug',
            'posts.created_at as last_post_created_at',
            'users.username as last_post_author',
        ];

        $query = $this->select(implode(', ', $selects))
            ->open()
            ->visible()
            ->join('forums', 'forums.id = threads.forum_id')
            ->join('posts', 'posts.id = threads.last_post_id', 'left')
            ->join('users', 'users.id = posts.author_id');

        switch($params['type'] ?? 'recent-posts') {
            case 'recent-threads':
                $query = $query->orderBy('threads.created_at', 'desc');
                break;
            case 'unanswered':
                $query = $query->where('threads.answer_post_id', null)
                    ->orderBy('threads.created_at', 'desc');
                break;
            case 'my-threads':
                $query = $query->where('threads.author_id', user_id())
                    ->orderBy('posts.created_at', 'desc');
                break;
            case 'recent-posts':
            default:
                $query = $query->orderBy('posts.created_at', 'desc');
                break;
        }

        return $query->paginate(20);
    }

    /**
     * Returns a single thread by its slug.
     */
    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Update the forum's last_thread_id.
     */
    protected function touchForum(array $data)
    {
        if (empty($data['data']['forum_id'])) {
            return $data;
        }

        model(ForumModel::class)
            ->where('id', $data['data']['forum_id'])
            ->set('last_thread_id', $data['id'])
            ->update();

        return $data;
    }
}
