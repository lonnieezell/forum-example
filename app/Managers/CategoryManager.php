<?php

namespace App\Managers;

use App\Entities\Category;
use App\Entities\User;
use App\Models\CategoryModel;

class CategoryManager
{
    /**
     * Prepare permissions as a category id => permissions array.
     */
    public function loadCategoryPermissions(?int $categoryId = null, bool $permissionsOnly = true): array
    {
        $cacheKey = 'category-permissions-' . (int) $permissionsOnly;

        if (! $permissions = cache($cacheKey)) {
            $categories = model(CategoryModel::class)->findAllPermissions();

            foreach ($categories as $category) {
                if ($permissionsOnly) {
                    $permissions[$category->id] = $category->permissions;
                } else {
                    $permissions[$category->id] = [
                        'parent_id'   => $category->parent_id,
                        'permissions' => $category->permissions,
                    ];
                }
            }

            cache()->save($cacheKey, $permissions, 0);
        }

        if ($categoryId !== null) {
            return $permissions[$categoryId] ?? [];
        }

        return $permissions;
    }

    /**
     * Returns an array of category_id which user can access.
     */
    public function filterCategoriesByPermissions(): array
    {
        $policy = service('policy');

        if (($user = auth()->user()) !== null) {
            $policy->withUser($user);
        }

        $categories = $this->loadCategoryPermissions(null, false);
        $removed    = [];
        // Filter out categories based on permissions
        $categories = array_filter($categories, static function ($category, $categoryId) use ($user, $policy, &$removed) {
            if ($category['permissions'] === null) {
                return true;
            }

            if ($user === null) {
                $removed[] = $categoryId;

                return false;
            }

            if ($policy->hasAny($category['permissions'])) {
                return true;
            }

            $removed[] = $categoryId;

            return false;
        }, ARRAY_FILTER_USE_BOTH);

        // Filter out children of removed parent categories
        $categories = array_filter($categories, static fn ($category) => ! (in_array($category['parent_id'], $removed, true)));

        return array_keys($categories);
    }

    /**
     * Returns a list of all categories, nested by parent.
     */
    public function findAllNested(): array
    {
        $categoryIds                = $this->filterCategoriesByPermissions();
        [$categories, $allChildren] = model(CategoryModel::class)->findAllNested($categoryIds);

        foreach ($categories as $category) {
            $children = [];

            foreach ($allChildren as $child) {
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
    public function findAllNestedDropdown(): array
    {
        $categoryIds = $this->filterCategoriesByPermissions();
        $categories  = model(CategoryModel::class)->findAllNestedDropdown($categoryIds);
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
}
