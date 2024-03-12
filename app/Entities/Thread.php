<?php

namespace App\Entities;

use App\Concerns\HasAuthorsAndEditors;
use App\Concerns\HasReactions;
use App\Concerns\RendersContent;
use App\Models\CategoryModel;
use CodeIgniter\Entity\Entity;

class Thread extends Entity
{
    use RendersContent;
    use HasAuthorsAndEditors;
    use HasReactions;

    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at', 'edited_at'];
    protected $casts   = [
        'category_id'    => 'integer',
        'author_id'      => 'integer',
        'editor_id'      => 'integer',
        'views'          => 'integer',
        'closed'         => 'boolean',
        'sticky'         => 'boolean',
        'visible'        => 'boolean',
        'last_post_id'   => '?integer',
        'post_count'     => 'integer',
        'answer_post_id' => '?integer',
    ];

    /**
     * Returns a link to this thread's page for use in views.
     */
    public function link()
    {
        $categorySlug = $this->category_slug ??
            model(CategoryModel::class)->find($this->category_id)?->slug;

        return route_to('thread', $categorySlug, $this->slug);
    }

    /**
     * Returns an original (initial) category_id value.
     */
    public function getOriginalCategoryId(): int
    {
        return (int) $this->original['category_id'];
    }

    /**
     * Weather thread is deleted.
     */
    public function isDeleted(): bool
    {
        return $this->attributes['deleted_at'] !== null;
    }

    public function cacheKey(string $suffix = ''): string
    {
        return 'post-' . $this->id . $suffix;
    }
}
