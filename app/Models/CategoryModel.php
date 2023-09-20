<?php

namespace App\Models;

use App\Concerns\Sluggable;
use App\Entities\Category;
use CodeIgniter\Model;

class CategoryModel extends Model
{
    use Sluggable;

    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Category::class;
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title', 'slug', 'description', 'parent_id', 'order', 'active', 'private',
        'thread_count', 'post_count', 'permissions', 'last_thread_id',
    ];
    protected $useTimestamps = true;
    protected $beforeInsert  = ['generateSlug'];

    /**
     * Scope method to only return active categories.
     *
     * Example:
     *  $categories = model(CategoryModel::class)->active()->findAll();
     */
    public function active()
    {
        return $this->where('categories.active', 1);
    }

    /**
     * Scope method to only return public categories.
     *
     * Example:
     *  $categories = model(CategoryModel::class)->public()->findAll();
     */
    public function public()
    {
        return $this->where('categories.private', 0);
    }

    /**
     * Scope method to only return categories with no parent.
     *
     * Example:
     *  $categories = model(CategoryModel::class)->parents()->findAll();
     */
    public function parents()
    {
        return $this->where('categories.parent_id', null);
    }

    /**
     * Scope method to only return categories with a parent.
     *
     * Example:
     *  $categories = model(CategoryModel::class)->children()->findAll();
     */
    public function children()
    {
        return $this->where('parent_id !=', null);
    }

    /**
     * Returns a list of all categories, nested by parent.
     */
    public function findAllNested()
    {
        $selects = [
            'categories.*',
            'threads.title as last_thread_title',
            'threads.updated_at as last_thread_updated_at',
            'users.username as last_thread_author',
        ];

        $categories = $this
            ->active()
            ->orderBy('order', 'asc')
            ->parents()
            ->select(implode(', ', $selects))
            ->join('threads', 'threads.id = categories.last_thread_id')
            ->join('users', 'users.id = threads.author_id')
            ->findAll();

        $allchildren = $this
            ->active()
            ->children()
            ->select(implode(', ', $selects))
            ->join('threads', 'threads.id = categories.last_thread_id')
            ->join('users', 'users.id = threads.author_id')
            ->orderBy('order', 'asc')
            ->findAll();

        foreach ($categories as $category) {
            $children = [];

            foreach ($allchildren as $child) {
                if ($child->parent_id === $category->id) {
                    $children[] = $child;
                }
            }
            $category->children = $children;
        }

        return $categories;
    }

    /**
     * Returns a list of all categories, prepared for dropdown.
     */
    public function findAllNestedDropdown()
    {
        $selects = [
            'id', 'title', 'parent_id',
        ];

        $categories = $this
            ->active()
            ->orderBy('parent_id', 'asc')
            ->orderBy('order', 'asc')
            ->select(implode(', ', $selects))
            ->findAll();

        $resultArray = [];

        foreach ($categories as $item) {
            if ($item->parent_id === null) {
                $resultArray[$item->title] = [];
            } else {
                $parentTitle = null;

                foreach ($categories as $parent) {
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

    public function findBySlug(string $slug)
    {
        return $this
            ->active()
            ->children()
            ->where('slug', $slug)
            ->first();
    }
}
