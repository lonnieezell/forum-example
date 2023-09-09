<?php

namespace App\Cells;

use App\Entities\Category;
use App\Models\CategoryModel;
use CodeIgniter\View\Cells\Cell;

class CategoryListCell extends Cell
{
    public array $categories;
    public int $activeId = 0;
    public int $parentId = 0;

    public function mount(?Category $activeCategory = null)
    {
        $this->activeId = $activeCategory ? $activeCategory->id : 0;
        $this->parentId = $activeCategory ? $activeCategory->parent_id : 0;

        $this->categories = model(CategoryModel::class)
            ->active()
            ->parents()
            ->public()
            ->orderBy('order', 'asc')
            ->orderBy('title', 'asc')
            ->findAllNested();
    }
}
