<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Forum extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'parent_id' => 'integer',
        'order'     => 'integer',
        'active'    => 'boolean',
        'private'   => 'boolean',
        'thread_count' => 'integer',
        'post_count' => 'integer',
        'permissions' => 'array',
    ];

    /**
     * Returns a link to this forum's page for use in views.
     */
    public function link()
    {
        return route_to('forum', $this->slug);
    }
}
