<?php

namespace App\Models;

use App\Concerns\HasAuthorsAndEditors;
use App\Concerns\HasTags;
use App\Concerns\ImpactsCategoryCounts;
use App\Concerns\ImpactsUserActivity;
use App\Concerns\Sluggable;
use App\Entities\Thread;
use CodeIgniter\Model;
use ReflectionException;

class ThreadModel extends Model
{
    use Sluggable;
    use ImpactsCategoryCounts;
    use ImpactsUserActivity;
    use HasAuthorsAndEditors;
    use HasTags;

    protected $table            = 'threads';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Thread::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id', 'title', 'slug', 'body', 'author_id', 'editor_id', 'edited_at', 'edited_reason', 'views', 'closed', 'sticky', 'visible', 'last_post_id', 'post_count', 'answer_post_id', 'markup',
    ];
    protected $useTimestamps        = true;
    protected $cleanValidationRules = false;
    protected $beforeInsert         = ['generateSlug'];
    protected $afterInsert          = ['incrementThreadCount', 'touchCategory', 'touchUser'];
    protected $afterDelete          = ['decrementThreadCount'];
    protected $afterUpdate          = ['touchCategory', 'recalculateStats'];
    protected ?int $oldCategoryId   = null;

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
     * Returns a paginated list of threads for the category.
     */
    public function forList(array $search, int $perPage): ?array
    {
        $selects = [
            'threads.*',
            'categories.title as category_title',
            'categories.slug as category_slug',
            'posts.created_at as last_post_created_at',
            'users.username as last_post_author',
        ];

        $query = $this->select(implode(', ', $selects))
            ->open()
            ->visible()
            ->join('categories', 'categories.id = threads.category_id')
            ->join('posts', 'posts.id = threads.last_post_id', 'left')
            ->join('users', 'users.id = posts.author_id', 'left')
            ->when(
                isset($search['tag']),
                static fn ($query) => $query
                    ->join('thread_tags', 'thread_tags.thread_id = threads.id', 'left')
                    ->join('tags', 'tags.id = thread_tags.tag_id', 'left')
                    ->where('tags.name', strtolower((string) $search['tag']))
            )
            ->when(
                isset($search['category']),
                static fn ($query) => $query
                    ->where('categories.slug', strtolower((string) $search['category']))
            );

        $query = match ($search['type'] ?? 'recent-posts') {
            'recent-threads' => $query->orderBy('threads.created_at', 'desc'),
            'unanswered'     => $query->where('threads.answer_post_id', null)
                ->orderBy('threads.created_at', 'desc'),
            'my-threads' => $query->where('threads.author_id', user_id())
                ->orderBy('posts.created_at', 'desc'),
            default => $query->orderBy('posts.created_at', 'desc'),
        };

        return $query->paginate($perPage);
    }

    /**
     * Increment thread view count.
     */
    public function incrementViewCount(int $threadId): bool
    {
        return $this->builder()->where('id', $threadId)->increment('views', 1);
    }

    /**
     * Returns a single thread by its slug.
     */
    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Update the category's last_thread_id.
     */
    protected function touchCategory(array $data)
    {
        if (empty($data['data']['category_id'])) {
            return $data;
        }

        model(CategoryModel::class)
            ->where('id', $data['data']['category_id'])
            ->set('last_thread_id', $data['id'])
            ->update();

        return $data;
    }

    /**
     * Set the old category_id, so we can recalculate stats later.
     */
    public function withStats(int $categoryId)
    {
        $this->oldCategoryId = $categoryId;

        return $this;
    }

    /**
     * Recalculate stats for thread.
     *
     * @throws ReflectionException
     */
    protected function recalculateStats(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        if ($this->oldCategoryId === null || (is_countable($data['id']) ? count($data['id']) : 0) > 1) {
            return $data;
        }

        $categoryModel = model(CategoryModel::class);

        // Update stats for new category
        $newCategory = $categoryModel->find($data['data']['category_id']);
        $newCategory->thread_count++;
        $newCategory->post_count += $data['data']['post_count'];

        // Update the last_thread_id for new category
        $oldestThread = $this
            ->where('category_id', $newCategory->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($newCategory->last_thread_id !== $oldestThread->id) {
            $newCategory->last_thread_id = $oldestThread->id;
        }

        $categoryModel->save($newCategory);

        // Update stats for old category
        $oldCategory = $categoryModel->find($this->oldCategoryId);
        $oldCategory->thread_count--;
        $oldCategory->post_count -= $data['data']['post_count'];

        // Update the last_thread_id for old category
        if ($oldCategory->last_thread_id === $data['id'][0]) {
            $oldestThread = $this
                ->where('category_id', $oldCategory->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $oldCategory->last_thread_id = $oldestThread->id;
        }

        $categoryModel->save($oldCategory);

        return $data;
    }
}
