<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 w-full">

<?php foreach ($pages->dirs(null, [1])->items() as $category): ?>

    <div class="inline-block" hx-boost="true">
        <ul class="menu bg-base-200 rounded-box">
            <li class="menu-title"><?= $category->getName(); ?></li>
            <?php foreach ($category->getFiles()->slice(0, 4)->items() as $file): ?>
                <li><a href="<?= url_to('page', $file->getPath()); ?>"><?= $file->getName(); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

<?php endforeach; ?>

</div>