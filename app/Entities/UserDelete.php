<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use Config\Forum;

class UserDelete extends Entity
{
    protected $datamap = [];
    protected $dates   = ['scheduled_at', 'created_at'];
    protected $casts   = [
        'user_id' => 'integer',
    ];

    /**
     * Generate link to restore user account.
     */
    public function restoreLink(): string
    {
        $days = config(Forum::class)->accountDeleteAfter * DAY;

        return signedurl()
            ->setExpiration($days)
            ->urlTo('action-cancel-account-delete', $this->attributes['user_id']);
    }
}
