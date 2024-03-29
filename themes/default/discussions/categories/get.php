<?= $this->extend('master')  ?>

<?= $this->section('main')  ?>
    <h1>Category: <?= esc($activeCategory->name); ?></h1>

    <div id="discussion-list">
        <?= $this->view('discussions/_list_items', ['threads' => $threads, 'table' => $table, 'searchUrl' => $searchUrl]); ?>
    </div>

<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>

<?php if (policy('threads.create')): ?>
    <div class="py-4" hx-boost="true">
        <a class="btn btn-primary mt-4 w-full" href="<?= url_to('thread-create'); ?>">
        Start a Discussion
        </a>
    </div>
<?php endif; ?>

<?= view_cell('CategoryListCell', ['activeCategory' => $activeCategory ?? null]) ?>

<?= $this->endSection() ?>
