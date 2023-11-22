<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ModerationLog extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at'];
    protected $casts   = [
        'id'            => 'integer',
        'resource_id'   => 'integer',
        'resource_type' => 'string',
        'status'        => 'string',
        'author_id'     => 'integer',
    ];
}
