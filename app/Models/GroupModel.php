<?php

namespace App\Models;

use CodeIgniter\Shield\Models\GroupModel as ShieldGroup;

class GroupModel extends ShieldGroup
{
    public function getForUsers(array $userIds): array
    {
        $results = $this->builder()
            ->select('user_id, group')
            ->whereIn('user_id', $userIds)
            ->get()
            ->getResultArray();

        if (empty($results)) {
            return [];
        }

        $users = [];

        foreach ($results as $row) {
            $users[$row['user_id']][] = $row['group'];
        }

        return $users;
    }
}
