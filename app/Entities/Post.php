<?php

namespace App\Entities;

use App\Concerns\RendersContent;
use CodeIgniter\Entity\Entity;

class Post extends Entity
{
    use RendersContent;

    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at', 'edited_at'];
    protected $casts   = [
        'forum_id'  => 'integer',
        'thread_id' => 'integer',
        'reply_to'  => 'integer',
        'author_id' => 'integer',
        'editor_id' => 'integer',
        'include_sig' => 'boolean',
        'visible'   => 'boolean',
    ];

    /**
     * Returns a link to this post's page for use in views.
     */
    public function link()
    {
        $forumSlug = model('ForumModel')->find($this->forum_id)->slug;
        $threadSlug = model('ThreadModel')->find($this->thread_id)->slug;

        return route_to('post', $forumSlug, $threadSlug, $this->id);
    }
}
