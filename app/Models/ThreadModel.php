<?php

namespace App\Models;

use App\Concerns\HasAuthorsAndEditors;
use App\Concerns\HasImages;
use App\Concerns\HasStats;
use App\Concerns\ImpactsCategoryCounts;
use App\Concerns\ImpactsUserActivity;
use App\Concerns\Sluggable;
use App\Entities\Thread;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use Exception;
use Michalsn\CodeIgniterTags\Traits\HasTags;
use ReflectionException;

class ThreadModel extends Model
{
    use Sluggable;
    use ImpactsCategoryCounts;
    use ImpactsUserActivity;
    use HasAuthorsAndEditors;
    use HasStats;
    use HasTags;
    use HasImages;

    protected $table            = 'threads';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Thread::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id', 'title', 'slug', 'body', 'author_id', 'editor_id', 'edited_at', 'edited_reason', 'views', 'closed', 'sticky', 'visible', 'last_post_id', 'post_count', 'answer_post_id', 'markup', 'reaction_count',
    ];
    protected $useTimestamps        = true;
    protected $cleanValidationRules = false;
    protected $beforeInsert         = ['generateSlug'];
    protected $afterInsert          = ['updateThreadImages', 'incrementThreadCount', 'touchCategory', 'touchUser'];
    protected $afterDelete          = ['decrementThreadCount'];
    protected $afterUpdate          = ['updateThreadImages', 'touchCategory', 'recalculateStats'];
    protected ?int $oldCategoryId   = null;

    protected function initialize(): void
    {
        $this->initTags();
    }

    /**
     * Include the 'has_reacted' field in the query.
     */
    public function includeHasReacted(?int $userId): self
    {
        if ($userId > 0) {
            $this->select('threads.*, (SELECT COUNT(*) from reactions WHERE thread_id = threads.id AND reactor_id = ' . $userId . ') as has_reacted');
        }

        return $this;
    }

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
     * Scope method to return all thread related data.
     *
     * Example:
     *  $threads = model(ThreadModel::class)->threads()->findAll();
     */
    public function threads()
    {
        $selects = [
            'threads.*',
            'categories.title AS category_title',
            'categories.slug AS category_slug',
            'threads.title AS thread_title',
            'threads.slug AS thread_slug',
        ];

        return $this
            ->select(implode(', ', $selects))
            ->join('categories', 'categories.id = threads.category_id', 'left');
    }

    /**
     * Returns a paginated list of threads for the category.
     */
    public function forList(array $search, int $perPage, array $categoryIds = []): ?array
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
                $categoryIds === [],
                static fn ($query) => $query->where('1 = 0')
            )
            ->when(
                $categoryIds !== [],
                static fn ($query) => $query->whereIn('threads.category_id', $categoryIds)
            )
            ->when(
                isset($search['tag']),
                static fn ($query) => $query
                    ->join('taggable', 'taggable.taggable_id = threads.id AND taggable.taggable_type = "threads"', 'left')
                    ->join('tags', 'tags.id = taggable.tag_id', 'left')
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
     * Set answer post.
     *
     * @throws Exception
     */
    public function setAnswer(int $threadId, int $postId): bool
    {
        if ($this->update($threadId, ['answer_post_id' => $postId])) {
            $builder = $this->builder('posts');

            $builder
                ->where([
                    'thread_id'         => $threadId,
                    'deleted_at'        => null,
                    'marked_as_deleted' => null,
                ])
                ->update([
                    'marked_as_answer' => null,
                ]);

            return $builder
                ->where([
                    'id' => $postId,
                ])
                ->update([
                    'marked_as_answer' => Time::now(),
                ]);
        }

        return false;
    }

    /**
     * Unset answer post.
     *
     * @throws ReflectionException
     */
    public function unsetAnswer(int $threadId, int $postId): bool
    {
        if ($this->where(['answer_post_id' => $postId])->update($threadId, ['answer_post_id' => null])) {
            return $this->builder('posts')
                ->where([
                    'thread_id'         => $threadId,
                    'deleted_at'        => null,
                    'marked_as_deleted' => null,
                ])
                ->update([
                    'marked_as_answer' => null,
                ]);
        }

        return false;
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
     * Get threads by user.
     */
    public function getThreadsByUser(int $userId, int $perPage): array
    {
        return $this
            ->threads()
            ->where('threads.author_id', $userId)
            ->orderBy('threads.created_at', 'desc')
            ->paginate($perPage);
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
            ->allowCallbacks(false)
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
        $newCategory = $categoryModel->allowCallbacks(false)->find($data['data']['category_id']);
        $newCategory->thread_count++;
        $newCategory->post_count += $data['data']['post_count'];

        // Update the last_thread_id for new category
        $oldestThread = $this
            ->allowCallbacks(false)
            ->where('category_id', $newCategory->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($newCategory->last_thread_id !== $oldestThread->id) {
            $newCategory->last_thread_id = $oldestThread->id;
        }

        $categoryModel->allowCallbacks(false)->save($newCategory);

        // Update stats for old category
        $oldCategory = $categoryModel->allowCallbacks(false)->find($this->oldCategoryId);
        $oldCategory->thread_count--;
        $oldCategory->post_count -= $data['data']['post_count'];

        // Update the last_thread_id for old category
        if ($oldCategory->last_thread_id === $data['id'][0]) {
            $oldestThread = $this
                ->allowCallbacks(false)
                ->where('category_id', $oldCategory->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $oldCategory->last_thread_id = $oldestThread?->id;
        }

        $categoryModel->allowCallbacks(false)->save($oldCategory);

        return $data;
    }
}
