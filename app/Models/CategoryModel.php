<?php

namespace App\Models;

use App\Concerns\HasStats;
use App\Concerns\Sluggable;
use App\Entities\Category;
use CodeIgniter\Model;

class CategoryModel extends Model
{
    use HasStats;
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
    public function findAllNested(array $categoryIds): array
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
            ->whereIn("{$this->table}.id", $categoryIds)
            ->select(implode(', ', $selects))
            ->join('threads', 'threads.id = categories.last_thread_id')
            ->join('users', 'users.id = threads.author_id')
            ->findAll();

        $allChildren = $this
            ->active()
            ->children()
            ->whereIn("{$this->table}.id", $categoryIds)
            ->select(implode(', ', $selects))
            ->join('threads', 'threads.id = categories.last_thread_id')
            ->join('users', 'users.id = threads.author_id')
            ->orderBy('order', 'asc')
            ->findAll();

        return [$categories, $allChildren];
    }

    /**
     * Returns a list of all categories, prepared for dropdown.
     */
    public function findAllNestedDropdown(array $categoryIds): array
    {
        $selects = [
            'id', 'title', 'parent_id', 'permissions',
        ];

        return $this
            ->active()
            ->whereIn('id', $categoryIds)
            ->orderBy('parent_id', 'asc')
            ->orderBy('order', 'asc')
            ->select(implode(', ', $selects))
            ->findAll();
    }

    /**
     * Find category by slug.
     */
    public function findBySlug(string $slug)
    {
        return $this
            ->active()
            ->children()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Load all categories with permissions.
     * If children's permissions are empty then
     * parents' permissions will be copied to them
     */
    public function findAllPermissions(): array
    {
        $selects = [
            "{$this->table}.id",
            "{$this->table}.parent_id",
            "COALESCE({$this->table}.permissions, p.permissions) AS permissions",
        ];

        return $this
            ->active()
            ->select(implode(', ', $selects))
            ->join("{$this->table} p", "p.id = {$this->table}.parent_id", 'left')
            ->orderBy("{$this->table}.parent_id", 'asc')
            ->orderBy("{$this->table}.order", 'asc')
            ->findAll();
    }
}
