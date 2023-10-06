<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class NotificationSetting extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at'];
    protected $casts   = [
        'user_id'          => 'integer',
        'email_thread'     => 'int-bool',
        'email_post'       => 'int-bool',
        'email_post_reply' => 'int-bool',
    ];
}
