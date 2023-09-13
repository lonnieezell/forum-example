<?= $this->extend('master')  ?>

<?= $this->section('main')  ?>
    <h1>Discussions</h1>

    <div id="discussion-list">
        <?= view('discussions/_list_items', ['threads' => $threads, 'table' => $table]); ?>
    </div>

<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>

    <?php if (auth()->user()?->can('threads.create')): ?>
        <div class="py-4" hx-boost="true">
            <a class="btn btn-primary mt-4 w-full" href="<?= url_to('thread-create'); ?>"">
                Start a Discussion
            </a>
        </div>
    <?php endif; ?>

    <?= view_cell('CategoryListCell', ['activeCategory' => $activeCategory ?? null]) ?>

<?= $this->endSection() ?>
