<?php

namespace App\Models;

use App\Concerns\HasStats;
use App\Concerns\RestoreEntry;
use App\Entities\User;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\RawSql;
use CodeIgniter\Shield\Models\UserModel as ShieldUser;
use ReflectionException;

class UserModel extends ShieldUser
{
    use HasStats;
    use RestoreEntry;

    protected $returnType = User::class;

    protected function initialize(): void
    {
        parent::initialize();

        // Merge properties with parent
        $this->allowedFields = array_merge($this->allowedFields, [
            'handed', 'thread_count', 'post_count', 'avatar', 'country', 'timezone', 'name', 'company', 'location', 'website', 'signature',
        ]);

        // Add event after insert
        $this->afterInsert[] = 'createNotificationSettings';

        // Add event after delete and restore
        $this->afterDelete[]  = 'afterUserDelete';
        $this->afterRestore[] = 'afterUserRestore';
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

            // Soft-delete all replies to this user posts
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->select('id')
                ->where('deleted_at', $user->deleted_at)
                ->where('author_id', $user->id);
            $subQuery = $this->db->newQuery()->fromSubquery($subQuery, 'p');
            // Run sub query
            $this->builder('posts')
                ->whereIn('reply_to', $subQuery)
                ->set('deleted_at', $user->deleted_at)
                ->update();

            // Soft-delete all threads created by this user
            $this->builder('threads')
                ->where('deleted_at', null)
                ->where('author_id', $user->id)
                ->set('deleted_at', $user->deleted_at)
                ->update();

            // Soft-delete all post in the threads created by this user
            $this->builder('posts')
                ->where('deleted_at', null)
                ->whereIn(
                    'thread_id',
                    static fn(BaseBuilder $builder) => $builder
                        ->select('id')
                        ->from('threads')
                        ->where('deleted_at', $user->deleted_at)
                        ->where('author_id', $user->id)
                )
                ->set('deleted_at', $user->deleted_at)
                ->update();

            // Update post_count for every affected user
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->distinct()
                ->select('author_id')
                ->where('deleted_at', $user->deleted_at);
            // Run sub query
            $query = $this->db->newQuery()->fromSubquery($subQuery, 'authors')
                ->select('authors.author_id AS id, COALESCE(COUNT(posts.id), 0) AS post_count')
                ->join('posts', 'authors.author_id = posts.author_id AND posts.deleted_at IS NULL', 'left')
                ->groupBy('authors.author_id');

            $this->builder('users')
                ->setQueryAsData($query)
                ->onConstraint('id')
                ->updateBatch();

            // Update thread_count for user requesting account delete
            $this->builder()->where('id', $userId)->update(['thread_count' => 0]);

            // Update post_count and last_post_id for every affected thread
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->distinct()
                ->select('thread_id')
                ->where('deleted_at', $user->deleted_at);
            // Run sub query
            $query = $this->db->newQuery()->fromSubquery($subQuery, 'threads')
                ->select('threads.thread_id AS id, COALESCE(COUNT(posts.id), 0) AS post_count, MAX(posts.id) AS last_post_id')
                ->join('posts', 'threads.thread_id = posts.thread_id AND posts.deleted_at IS NULL', 'left')
                ->groupBy('threads.thread_id');

            $this->builder('threads')
                ->setQueryAsData($query)
                ->onConstraint('id')
                ->updateBatch();

            // Update post_count for every affected category
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->distinct()
                ->select('category_id')
                ->where('deleted_at', $user->deleted_at);
            // Run sub query
            $query = $this->db->newQuery()->fromSubquery($subQuery, 'categories')
                ->select('categories.category_id AS id, COALESCE(COUNT(posts.id), 0) AS post_count')
                ->join('posts', 'categories.category_id = posts.category_id AND posts.deleted_at IS NULL', 'left')
                ->groupBy('categories.category_id');

            $this->builder('categories')
                ->setQueryAsData($query)
                ->onConstraint('id')
                ->updateBatch();

            // @todo
            // update answer_post_id, but this should probably involve
            // changes in the posts table to make it possible to mark the answer
            // at the post level, so we can restore the correct value if needed
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

        foreach ($data['id'] as $userId) {
            // Update post_count for every affected user
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->distinct()
                ->select('author_id')
                ->where('deleted_at', $data['deletedAt']);
            $subQuery = $this->db->newQuery()->fromSubquery($subQuery, 'a');
            // Run sub query
            $query = $this->builder('posts')
                ->select('author_id AS id, COUNT(*) AS post_count')
                ->groupStart()
                ->where('deleted_at', null)
                ->orWhere('deleted_at', $data['deletedAt'])
                ->groupEnd()
                ->whereIn('author_id', $subQuery)
                ->groupBy('author_id');

            $this->builder('users')
                ->setQueryAsData($query)
                ->onConstraint('id')
                ->updateBatch();

            // Update thread_count for this user
            $count = $this->builder('threads')
                ->where('author_id', $userId)
                ->countAllResults();

            $this->builder()->where('id', $userId)->set('thread_count', $count)->update();

            // Update post_count and last_post_id for every affected thread
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->distinct()
                ->select('thread_id')
                ->where('deleted_at', $data['deletedAt']);
            $subQuery = $this->db->newQuery()->fromSubquery($subQuery, 't');
            // Run sub query
            $query = $this->builder('posts')
                ->select('thread_id AS id, COUNT(*) AS post_count, MAX(id) AS last_post_id')
                ->groupStart()
                ->where('deleted_at', null)
                ->orWhere('deleted_at', $data['deletedAt'])
                ->groupEnd()
                ->whereIn('thread_id', $subQuery)
                ->groupBy('thread_id');

            $this->builder('threads')
                ->setQueryAsData($query)
                ->onConstraint('id')
                ->updateBatch();

            // Update post_count for every affected category
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->distinct()
                ->select('category_id')
                ->where('deleted_at', $data['deletedAt']);
            $subQuery = $this->db->newQuery()->fromSubquery($subQuery, 't');
            // Run sub query
            $query = $this->builder('posts')
                ->select('category_id AS id, COUNT(*) AS post_count')
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

            // Restore all replies for posts written by this user
            // Prepare sub query
            $subQuery = $this->db->table('posts')
                ->select('id')
                ->where('deleted_at', $data['deletedAt'])
                ->where('author_id', $userId);
            $subQuery = $this->db->newQuery()->fromSubquery($subQuery, 'p');
            // Run sub query
            $this->builder('posts')
                ->whereIn('reply_to', $subQuery)
                ->set('deleted_at', null)
                ->update();

            // Restore all posts created by this user
            $this->builder('posts')
                ->where('deleted_at', $data['deletedAt'])
                ->where('author_id', $userId)
                ->set('deleted_at', null)
                ->update();

            // Restore all post in the threads created by this user
            $this->builder('posts')
                ->where('deleted_at', $data['deletedAt'])
                ->whereIn(
                    'thread_id',
                    static fn (BaseBuilder $builder) => $builder
                        ->select('id')
                        ->from('threads')
                        ->where('deleted_at', $data['deletedAt'])
                        ->where('author_id', $userId)
                )
                ->set('deleted_at', null)
                ->update();

            // Restore all threads created by this user
            $this->builder('threads')
                ->where('deleted_at', $data['deletedAt'])
                ->where('author_id', $userId)
                ->set('deleted_at', null)
                ->update();

            // @todo
            // update answer_post_id, but this should probably involve
            // changes in the posts table to make it possible to mark the answer
            // at the post level, so we can restore the correct value if needed
        }

        return $data;
    }
}
