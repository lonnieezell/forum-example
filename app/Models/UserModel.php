<?php

namespace App\Models;

use App\Concerns\HasReactions;
use App\Concerns\HasStats;
use App\Concerns\RestoreEntry;
use App\Entities\User;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Models\UserModel as ShieldUser;
use Exception;
use ReflectionException;

class UserModel extends ShieldUser
{
    use HasStats;
    use RestoreEntry;
    use HasReactions;

    protected $returnType = User::class;

    protected function initialize(): void
    {
        parent::initialize();

        // Merge properties with parent
        $this->allowedFields = [...$this->allowedFields, 'handed', 'thread_count', 'post_count', 'avatar', 'country', 'timezone', 'name', 'company', 'location', 'website', 'signature', 'two_factor_auth_email', 'reaction_count', 'trust_level',
        ];

        // Add event after insert
        $this->afterInsert[] = 'createNotificationSettings';

        // Add event after delete and restore
        $this->afterDelete[]  = 'afterUserDelete';
        $this->afterRestore[] = 'afterUserRestore';
    }

    /**
     * Only return users that are active.
     */
    public function active(): self
    {
        return $this->where('active', 1);
    }

    /**
     * Only return users that have been active on the site within
     * the last 24 hours.
     *
     * NOTE: This requires that Config\Auth::recordActiveDate is set to true.
     */
    public function activeToday(): self
    {
        return $this->where('last_active >=', Time::now()->subHours(24)->toDateTimeString());
    }

    /**
     * Prepare keys => values for dropdown.
     */
    public function dropdown(string $key, string $value, array $ids): array
    {
        $results = $this
            ->select("{$key}, {$value}")
            ->when(
                $ids !== [],
                static fn ($query) => $query->whereIn($key, $ids)
            )
            ->findAll();

        return array_column($results, $value, $key);
    }

    public function searchMembers(array $search, int $page, int $perPage, string $sortColumn, string $sortDirection): ?array
    {
        $selects = [
            'users.*',
            '(thread_count + post_count) AS count',
        ];

        $results = $this
            ->withIdentities()
            ->select(implode(', ', $selects))
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id', 'left')
            ->when(
                isset($search['username']) && $search['username'] !== '',
                static fn ($query) => $query->like('users.username', $search['username'], 'both')
            )
            ->when(
                isset($search['country']) && $search['country'] !== '',
                static fn ($query) => $query->like('users.country', $search['country'], 'both')
            )
            ->when(
                isset($search['role']) && $search['role'] !== 'all',
                static fn ($query) => $query->where('auth_groups_users.group', $search['role'])
            )
            ->when(
                isset($search['type']) && $search['type'] === 'new',
                static fn ($query) => $query->where('(thread_count + post_count) <=', 1)
            )
            ->groupBy('users.id')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage, 'default', $page);

        if (empty($results)) {
            return [];
        }

        // Determine groups for users
        $userIds   = array_map('intval', array_column($results, 'id'));
        $userRoles = model(GroupModel::class)->getForUsers($userIds);

        $roleNames = array_combine(
            array_keys(setting('AuthGroups.groups')),
            array_column(setting('AuthGroups.groups'), 'title')
        );

        foreach ($results as $row) {
            // Set user-friendly group names
            if (isset($userRoles[$row->id])) {
                $roles = [];

                foreach ($userRoles[$row->id] as $role) {
                    $roles[] = $roleNames[$role];
                }
                $row->roles = $roles;
            }
        }

        return $results;
    }

    /**
     * Create default notification settings for user.
     *
     * @throws ReflectionException
     */
    protected function createNotificationSettings(array $eventData): void
    {
        if ($eventData['result']) {
            model(NotificationSettingModel::class)->insert(['user_id' => $eventData['id']]);
        }
    }

    /**
     * Soft-delete everything and update stats.
     */
    protected function afterUserDelete(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        if ($data['purge'] === true) {
            return $data;
        }

        foreach ($data['id'] as $userId) {
            // Get current user
            $user = $this->allowCallbacks(false)->onlyDeleted()->find($userId);

            // Soft-delete all posts created by this user
            $this->builder('posts')
                ->where('deleted_at', null)
                ->where('author_id', $user->id)
                ->set('deleted_at', $user->deleted_at)
                ->update();

            // Mark Soft-deleted posts which has replies
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->select('id')
                ->where('deleted_at', $user->deleted_at)
                ->where('author_id', $user->id);
            $subQuery = $this->db->newQuery()->fromSubquery($subQuery, 'p1');
            $subQuery = $this->db->table('posts')
                ->distinct()
                ->select('reply_to')
                ->whereIn('reply_to', $subQuery);
            $subQuery = $this->db->newQuery()->fromSubquery($subQuery, 'p2');
            // Run sub query
            $this->builder('posts')
                ->whereIn('id', $subQuery)
                ->set('marked_as_deleted', $user->deleted_at)
                ->set('deleted_at', null)
                ->update();

            // Soft-delete all threads created by this user
            $this->builder('threads')
                ->where('author_id', $user->id)
                ->where('deleted_at', null)
                ->set('deleted_at', $user->deleted_at)
                ->update();

            // Soft-delete all post in the threads created by this user
            $this->builder('posts')
                ->whereIn(
                    'thread_id',
                    static fn (BaseBuilder $builder) => $builder
                        ->select('id')
                        ->from('threads')
                        ->where('author_id', $user->id)
                        ->where('deleted_at', $user->deleted_at)
                )
                ->groupStart()
                ->where('deleted_at', null)
                ->orWhere('marked_as_deleted', $user->deleted_at)
                ->groupEnd()
                ->set('marked_as_deleted', null)
                ->set('deleted_at', $user->deleted_at)
                ->update();

            // Update post_count and thread_count for user requesting account delete
            $this->builder()->where('id', $userId)->update(['thread_count' => 0, 'post_count' => 0]);

            // Update post_count for every affected user
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->distinct()
                ->select('author_id')
                ->where('deleted_at', $user->deleted_at)
                ->where('author_id !=', $userId);
            // Run sub query
            $query = $this->db->newQuery()->fromSubquery($subQuery, 'authors')
                ->select('authors.author_id AS id, COALESCE(COUNT(posts.id), 0) AS post_count')
                ->join('posts', 'authors.author_id = posts.author_id AND posts.deleted_at IS NULL', 'left')
                ->groupBy('authors.author_id');

            $this->builder('users')
                ->setQueryAsData($query)
                ->onConstraint('id')
                ->updateBatch();

            // Update post_count and last_post_id for every affected thread
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->distinct()
                ->select('thread_id')
                ->where('deleted_at', $user->deleted_at)
                ->orWhere('marked_as_deleted', $user->deleted_at);
            // Run sub query
            $query = $this->db->newQuery()->fromSubquery($subQuery, 'threads')
                ->select('threads.thread_id AS id, COALESCE(COUNT(posts.id), 0) AS post_count, MAX(posts.id) AS last_post_id')
                ->join('posts', 'threads.thread_id = posts.thread_id AND posts.deleted_at IS NULL', 'left')
                ->groupBy('threads.thread_id');

            $this->builder('threads')
                ->setQueryAsData($query)
                ->onConstraint('id')
                ->updateBatch();

            // Update thread_count, post_count and last_thread_id for every affected category
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->distinct()
                ->select('category_id')
                ->where('deleted_at', $user->deleted_at)
                ->orWhere('marked_as_deleted', $user->deleted_at);
            // Run sub query
            $query = $this->db->newQuery()->fromSubquery($subQuery, 'categories')
                ->select('categories.category_id AS id, COALESCE(COUNT(threads.id), 0) AS thread_count, COALESCE(SUM(threads.post_count), 0) AS post_count, MAX(threads.id) AS last_thread_id')
                ->join('threads', 'categories.category_id = threads.category_id AND threads.deleted_at IS NULL', 'left')
                ->groupBy('categories.category_id');

            $this->builder('categories')
                ->setQueryAsData($query)
                ->onConstraint('id')
                ->updateBatch();

            // Get thread_id for soft-delete posts marked as an answer and created by this user
            $subQuery = $this->db->table('posts')
                ->select('thread_id')
                ->where('author_id', $user->id)
                ->where('deleted_at', $user->deleted_at)
                ->where('marked_as_answer !=', null);
            // Update threads
            $this->builder('threads')
                ->whereIn('id', $subQuery)
                ->update(['answer_post_id' => null]);
        }

        return $data;
    }

    /**
     * Restore everything and update stats.
     */
    protected function afterUserRestore(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        // Update post_count for every affected user
        // Prepare sub query
        $subQuery = $this->db->table('posts')
            ->distinct()
            ->select('author_id')
            ->where('deleted_at', $data['deletedAt'])
            ->orWhere('marked_as_deleted', $data['deletedAt']);
        $subQuery = $this->db->newQuery()->fromSubquery($subQuery, 'a');
        // Run sub query
        $query = $this->builder('posts')
            ->select('author_id AS id, COUNT(*) AS post_count')
            ->whereIn('author_id', $subQuery)
            ->groupStart()
            ->where('deleted_at', null)
            ->orWhere('deleted_at', $data['deletedAt'])
            ->orWhere('marked_as_deleted', $data['deletedAt'])
            ->groupEnd()
            ->groupBy('author_id');

        $this->builder('users')
            ->setQueryAsData($query)
            ->onConstraint('id')
            ->updateBatch();

        // Update post_count and last_post_id for every affected thread
        // Prepare sub query
        $subQuery = $this->db->table('posts')
            ->distinct()
            ->select('thread_id')
            ->where('deleted_at', $data['deletedAt'])
            ->orWhere('marked_as_deleted', $data['deletedAt']);
        $subQuery = $this->db->newQuery()->fromSubquery($subQuery, 't');
        // Run sub query
        $query = $this->builder('posts')
            ->select('thread_id AS id, COUNT(*) AS post_count, MAX(id) AS last_post_id')
            ->groupStart()
            ->where('deleted_at', null)
            ->orWhere('deleted_at', $data['deletedAt'])
            ->orWhere('marked_as_deleted', $data['deletedAt'])
            ->groupEnd()
            ->whereIn('thread_id', $subQuery)
            ->groupBy('thread_id');

        $this->builder('threads')
            ->setQueryAsData($query)
            ->onConstraint('id')
            ->updateBatch();

        // Update thread_count, post_count and last_thread_id for every affected category
        // Prepare sub query
        $subQuery = $this->db->table('posts')
            ->distinct()
            ->select('category_id')
            ->where('deleted_at', $data['deletedAt'])
            ->orWhere('marked_as_deleted', $data['deletedAt']);
        $subQuery = $this->db->newQuery()->fromSubquery($subQuery, 'c');
        // Run sub query
        $query = $this->builder('threads')
            ->select('category_id AS id, COALESCE(COUNT(id), 0) AS thread_count, COALESCE(SUM(post_count), 0) AS post_count, MAX(id) AS last_thread_id')
            ->groupStart()
            ->where('deleted_at', null)
            ->orWhere('deleted_at', $data['deletedAt'])
            ->groupEnd()
            ->whereIn('category_id', $subQuery)
            ->groupBy('category_id');

        $this->builder('categories')
            ->setQueryAsData($query)
            ->onConstraint('id')
            ->updateBatch();

        // Restore marked_as_answer
        // Get affected threads
        $subQuery = $this->db->table('posts')
            ->select('thread_id')
            ->where('author_id', $data['id'])
            ->where('deleted_at', $data['deletedAt'])
            ->where('marked_as_answer !=', null);
        // Prepare answers posts to restore (take the latest accepted answer only)
        $subQuery = $this->db->table('posts')
            ->select('thread_id AS id, id AS answer_post_id, ROW_NUMBER() OVER (PARTITION BY thread_id ORDER BY marked_as_answer DESC) AS answer_rank')
            ->whereIn('thread_id', $subQuery)
            ->groupStart()
            ->groupStart()
            ->where('author_id', $data['id'])
            ->where('deleted_at', $data['deletedAt'])
            ->groupEnd()
            ->orGroupStart()
            ->where('deleted_at', null)
            ->where('marked_as_deleted', null)
            ->groupEnd()
            ->groupEnd();
        $query = $this->db->newQuery()
            ->select('t.id, t.answer_post_id')
            ->fromSubquery($subQuery, 't')
            ->where('t.answer_rank', 1);
        // Update threads
        $this->builder('threads')
            ->setQueryAsData($query)
            ->onConstraint('id')
            ->updateBatch();

        // Restore all posts created by this user
        $this->builder('posts')
            ->where('author_id', $data['id'])
            ->groupStart()
            ->where('deleted_at', $data['deletedAt'])
            ->orWhere('marked_as_deleted', $data['deletedAt'])
            ->groupEnd()
            ->set('deleted_at', null)
            ->set('marked_as_deleted', null)
            ->update();

        // Restore all threads created by this user
        $this->builder('threads')
            ->where('deleted_at', $data['deletedAt'])
            ->where('author_id', $data['id'])
            ->set('deleted_at', null)
            ->update();

        // Update thread_count and post_count for this user
        $countThreads = $this->builder('threads')
            ->where('author_id', $data['id'])
            ->where('deleted_at', null)
            ->countAllResults();

        $this->builder()
            ->where('id', $data['id'])
            ->set('thread_count', $countThreads)
            ->update();

        return $data;
    }
}
