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
    private function preparePermissions(?int $categoryId = null): array
    {
        $cacheKey = 'category-permissions';

        if (! $permissions = cache($cacheKey)) {
            $categories = model(CategoryModel::class)->findAllPermissions();

            foreach ($categories as $category) {
                $permissions[$category->id] = $category->permissions;
            }

            cache()->save($cacheKey, $permissions, 0);
        }

        if ($categoryId !== null) {
            return $permissions[$categoryId] ?? [];
        }

        return $permissions;
    }

    /**
     * Check if current user should have access to the given category.
     */
    public function checkCategoryPermissions(int $categoryId)
    {
        $policy = service('policy');

        if ($user = auth()->user()) {
            $policy->withUser($user);
        }

        $permissions = $this->preparePermissions($categoryId);

        return $policy->hasAny($permissions);
    }

    /**
     * Filter threads by category permissions.
     */
    public function filterThreadsByPermissions(array $threads): array
    {
        $policy = service('policy');

        if ($user = auth()->user()) {
            $policy->withUser($user);
        }

        $permissions = $this->preparePermissions();

        return array_filter($threads, static function ($thread) use ($permissions, $user, $policy) {
            if (($permissions[$thread->category_id] ?? []) === []) {
                return true;
            }

            if ($user === null) {
                return false;
            }

            return $policy->hasAny($permissions[$thread->category_id]);
        });
    }

    /**
     * Filter categories by permissions.
     */
    public function filterCategoriesByPermissions(array $categories): array
    {
        $policy = service('policy');

        if ($user = auth()->user()) {
            $policy->withUser($user);
        }

        $permissions = $this->preparePermissions();
        $removed     = [];

        $categories = array_filter($categories, static function ($category) use ($permissions, $user, $policy, &$removed) {
            if (($permissions[$category->id] ?? []) === []) {
                return true;
            }

            if ($user === null) {
                return false;
            }

            if ($policy->hasAny($permissions[$category->id])) {
                return true;
            }

            $removed[] = $category->id;

            return false;
        });

        // Filter out children of removed categories
        return array_filter($categories, static fn ($category) => ! (in_array($category->parent_id, $removed, true)));
    }

    /**
     * Returns a list of all categories, nested by parent.
     */
    public function findAllNested(): array
    {
        [$categories, $allChildren] = model(CategoryModel::class)->findAllNested();

        $categories  = $this->filterCategoriesByPermissions($categories);
        $allChildren = $this->filterCategoriesByPermissions($allChildren);

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
        $categories = model(CategoryModel::class)->findAllNestedDropdown();
        $categories = $this->filterCategoriesByPermissions($categories);

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
