<?php

namespace App\Models;

use App\Concerns\Sluggable;
use App\Entities\Forum;
use CodeIgniter\Model;

class ForumModel extends Model
{
    use Sluggable;

    protected $DBGroup          = 'default';
    protected $table            = 'forums';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Forum::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title', 'slug', 'description', 'parent_id', 'order', 'active', 'private',
        'thread_count', 'post_count', 'permissions', 'last_thread_id',
    ];

    protected $beforeInsert = ['generateSlug'];

    /**
     * Scope method to only return active forums.
     *
     * Example:
     *  $forums = model(ForumModel::class)->active()->findAll();
     */
    public function active()
    {
        return $this->where('forums.active', 1);
    }

    /**
     * Scope method to only return public forums.
     *
     * Example:
     *  $forums = model(ForumModel::class)->public()->findAll();
     */
    public function public()
    {
        return $this->where('forums.private', 0);
    }

    /**
     * Scope method to only return forums with no parent.
     *
     * Example:
     *  $forums = model(ForumModel::class)->parents()->findAll();
     */
    public function parents()
    {
        return $this->where('forums.parent_id', null);
    }

    /**
     * Scope method to only return forums with a parent.
     *
     * Example:
     *  $forums = model(ForumModel::class)->children()->findAll();
     */
    public function children()
    {
        return $this->where('parent_id !=', null);
    }

    /**
     * Returns a list of all forums, nested by parent.
     */
    public function findAllNested()
    {
        $selects = [
            'forums.*',
            'threads.title as last_thread_title',
            'threads.updated_at as last_thread_updated_at',
            'users.username as last_thread_author',
        ];

        $forums = $this
            ->active()
            ->orderBy('order', 'asc')
            ->parents()
            ->select(implode(', ', $selects))
            ->join('threads', 'threads.id = forums.last_thread_id')
            ->join('users', 'users.id = threads.author_id')
            ->findAll();

        $allchildren = $this
            ->active()
            ->children()
            ->select(implode(', ', $selects))
            ->join('threads', 'threads.id = forums.last_thread_id')
            ->join('users', 'users.id = threads.author_id')
            ->orderBy('order', 'asc')
            ->findAll();

        foreach ($forums as $forum) {
            $children = [];
            foreach ($allchildren as $child) {
                if ($child->parent_id == $forum->id) {
                    $children[] = $child;
                }
            }
            $forum->children = $children;
        }

        return $forums;
    }

    /**
     * Returns a list of all forums, prepared for dropdown.
     */
    public function findAllNestedDropdown()
    {
        $selects = [
            'id', 'title', 'parent_id',
        ];

        $forums = $this
            ->active()
            ->orderBy('parent_id', 'asc')
            ->orderBy('order', 'asc')
            ->select(implode(', ', $selects))
            ->findAll();

        $resultArray = [];

        foreach ($forums as $item) {
            if ($item->parent_id === null) {
                $resultArray[$item->title] = [];
            } else {
                $parentTitle = null;
                foreach ($forums as $parent) {
                    if ($parent->id === $item->parent_id) {
                        $parentTitle = $parent->title;
                        break;
                    }
                }

                if (! empty($parentTitle)) {
                    $resultArray[$parentTitle][$item->id] = $item->title;
                }
            }
        }

        return $resultArray;
    }
}
