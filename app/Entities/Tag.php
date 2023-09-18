<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Tag extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at'];
    protected $casts   = [];

    /**
     * Returns a link to this tag's page for use in views.
     */
    public function link()
    {
        return route_to('tag', $this->name);
    }
}
