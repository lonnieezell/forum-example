<div class="collapse collapse-arrow category-list" x-data="{listOpen: false}" x-init="listOpen=window.innerWidth > 450">
    <div class="collapse-title text-lg font-medium py-2" x-on:click="listOpen = !listOpen">Categories</div>

    <div class="collapse-content" x-show="listOpen">
    <?php if (empty($categories)): ?>
        <p>There are no categories to display.</p>
    <?php else: ?>
        <ul class="menu">
            <?php foreach($categories as $category) : ?>
                <?php if (is_countable($category->children) ? count($category->children) : 0) : ?>
                    <li x-data="{ open: <?= $category->id === $parentId ? 'true' : 'false' ?> }">
                        <details x-transition <?= $category->id === $parentId ? 'open' : '' ?>>
                            <summary x-on:click="open = !open">
                                <?= esc($category->title) ?>
                            </summary>
                            <ul x-show="open">
                                <?php foreach($category->children as $child) : ?>
                                    <li>
                                        <a href="<?= route_to('category', $child->slug) ?>"
                                            class="<?= $activeId === $child->id ? 'active' : '' ?>"
                                            hx-boost="true">
                                            <?= esc($child->title) ?>
                                        </a>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </details>
                    </li>
                <?php endif ?>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
    </div>
</div>
