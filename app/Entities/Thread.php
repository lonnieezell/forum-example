<?php

namespace App\Entities;

use App\Concerns\RendersContent;
use App\Models\CategoryModel;
use CodeIgniter\Entity\Entity;

class Thread extends Entity
{
    use RendersContent;

    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at', 'edited_at'];
    protected $casts   = [
        'category_id' => 'integer',
        'author_id' => 'integer',
        'editor_id' => 'integer',
        'views' => 'integer',
        'closed' => 'boolean',
        'sticky' => 'boolean',
        'visible' => 'boolean',
    ];

    /**
     * Returns a link to this thread's page for use in views.
     */
    public function link()
    {
        $categorySlug = isset($this->category_slug) ?
            $this->category_slug :
            model(CategoryModel::class)->find($this->category_id)?->slug;

        return route_to('thread', $categorySlug, $this->slug);
    }
}
