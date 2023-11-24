<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ModerationIgnored extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at'];
    protected $casts   = [
        'id'                   => 'integer',
        'moderation_report_id' => 'integer',
        'user_id'              => 'integer',
    ];
}
