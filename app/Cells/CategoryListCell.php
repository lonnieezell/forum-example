<?php

namespace App\Cells;

use App\Entities\Category;
use App\Managers\CategoryManager;
use CodeIgniter\View\Cells\Cell;

class CategoryListCell extends Cell
{
    protected string $view = 'category_list_cell';
    public array $categories;
    public int $activeId = 0;
    public int $parentId = 0;

    public function mount(?Category $activeCategory = null)
    {
        $this->activeId = $activeCategory ? $activeCategory->id : 0;
        $this->parentId = $activeCategory ? $activeCategory->parent_id : 0;

        $this->categories = manager(CategoryManager::class)->findAllNested();
    }
}
