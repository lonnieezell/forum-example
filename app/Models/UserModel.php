<?php

namespace App\Models;

use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Models\UserModel as ShieldUser;
use App\Entities\User;

class UserModel extends ShieldUser
{
    protected $returnType = User::class;

    protected function initialize(): void
    {
        parent::initialize();

        // Merge properties with parent
        $this->allowedFields = array_merge($this->allowedFields, [
            'handed',
            'thread_count',
            'post_count',
            'avatar',
            'country',
            'timezone',
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
            ->when(isset($search['username']) && $search['username'] !== '', static function ($query) use ($search) {
                $query->like('users.username', $search['username'], 'both');
            })
            ->when(isset($search['country']) && $search['country'] !== '', static function ($query) use ($search) {
                $query->like('users.country', $search['country'], 'both');
            })
            ->when(isset($search['role']) && $search['role'] !== 'all', static function ($query) use ($search) {
                $query->where('auth_groups_users.group', $search['role']);
            })
            ->when(isset($search['type']) && $search['type'] === 'new', static function ($query) {
                $query->where('(thread_count + post_count) <=', 1);
            })
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
            // Grab user email
            if ($identity = $row->getIdentities(Session::ID_TYPE_EMAIL_PASSWORD)[0] ?? null) {
                $row->setEmail($identity->secret);
            }

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
