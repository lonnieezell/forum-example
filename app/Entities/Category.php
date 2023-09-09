<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Category extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'id' => 'integer',
        'parent_id' => '?integer',
        'order'     => 'integer',
        'active'    => 'boolean',
        'private'   => 'boolean',
        'thread_count' => 'integer',
        'post_count' => 'integer',
        'last_thread_id' => '?integer',
        'permissions' => 'array',
    ];

    /**
     * Returns a link to this category's page for use in views.
     */
    public function link()
    {
        return route_to('category', $this->slug);
    }
}
