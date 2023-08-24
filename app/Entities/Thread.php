<?php

namespace App\Entities;

use App\Concerns\RendersContent;
use App\Models\ForumModel;
use CodeIgniter\Entity\Entity;

class Thread extends Entity
{
    use RendersContent;

    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at', 'edited_at'];
    protected $casts   = [
        'forum_id' => 'integer',
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
        $forum = model(ForumModel::class)->find($this->forum_id);

        return route_to('thread', $forum->slug, $this->slug);
    }
}
