<?php

namespace App\Managers;

use App\Entities\Category;
use App\Entities\User;
use App\Models\CategoryModel;

class CategoryManager
{
    public function preparePermissions(?int $categoryId = null): array
    {
        $cacheKey = 'categoryPermissions';

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

    public function checkPermissions(int $categoryId)
    {
        $policy = service('policy');

        /** @var User $user */
        if ($user = auth()->user()) {
            $policy->withUser($user);
        }

        $permissions = $this->preparePermissions($categoryId);

        return $policy->hasAny($permissions);
    }

    /**
     * Filter categories by permissions.
     */
    public function filterByPermissions(array|Category $categories): array|null|Category
    {
        $policy = service('policy');

        /** @var User $user */
        if ($user = auth()->user()) {
            $policy->withUser($user);
        }

        if ($categories instanceof Category) {
            if (! isset($categories->permissions) || $categories->permissions === []) {
                return $categories;
            }

            if ($user === null) {
                return null;
            }

            if ($policy->hasAny($categories->permissions)) {
                return $categories;
            }

            return null;
        }

        $removed = [];

        $categories = array_filter($categories, static function ($category) use ($user, $policy, &$removed) {
            if (! isset($category->permissions) || $category->permissions === []) {
                return true;
            }

            if ($user === null) {
                return false;
            }

            if ($policy->hasAny($category->permissions)) {
                return true;
            }

            $removed[] = $category->id;

            return false;
        });

        // Filter out children of removed categories
        return array_filter($categories, static function ($category) use ($removed) {
            return ! (in_array($category->parent_id, $removed, true));
        });
    }

    /**
     * Returns a list of all categories, nested by parent.
     */
    public function findAllNested(): array
    {
        [$categories, $allChildren] = model(CategoryModel::class)->findAllNested();

        $categories  = $this->filterByPermissions($categories);
        $allChildren = $this->filterByPermissions($allChildren);

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
        $categories = $this->filterByPermissions($categories);

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
