<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Image extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at'];
    protected $casts   = [
        'id' => 'integer',
        'user_id' => 'integer',
        'thread_id' => '?integer',
        'post_id' => '?integer',
        'size' => 'integer',
        'is_used' => 'bool',
    ];
}
