<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ModerationReport extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at'];
    protected $casts   = [
        'id' => 'integer',
        'resource_id' => 'integer',
        'resource_type' => 'string',
        'comment' => 'string',
        'author_id' => '?integer',
    ];
}
