<?php

namespace App\Models;

use App\Concerns\HasStats;
use App\Entities\User;
use CodeIgniter\Shield\Models\UserModel as ShieldUser;

class UserModel extends ShieldUser
{
    use HasStats;

    protected $returnType = User::class;

    protected function initialize(): void
    {
        parent::initialize();

        // Merge properties with parent
        $this->allowedFields = array_merge($this->allowedFields, [
            'handed', 'thread_count', 'post_count', 'avatar', 'country', 'timezone', 'name', 'company', 'location', 'website', 'signature',
        ]);
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
}
