<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class NotificationMuted extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at'];
    protected $casts   = [
        'user_id'   => 'integer',
        'thread_id' => 'integer',
    ];
}
