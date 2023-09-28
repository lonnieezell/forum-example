<?php

namespace App\Concerns;

use App\Models\UserModel;
use CodeIgniter\I18n\Time;

trait ImpactsUserActivity
{
    /**
     * Updates the user's last activity.
     */
    protected function touchUser(array $data)
    {
        if (empty($data['data']['author_id'])) {
            return $data;
        }

        model(UserModel::class)
            ->allowCallbacks(false)
            ->where('id', $data['data']['author_id'])
            ->set('last_active', Time::now('UTC'))
            ->update();

        return $data;
    }
}
