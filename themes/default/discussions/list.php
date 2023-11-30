<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <div class="header-row">
        <div>
            <h2>Discussions</h2>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('main')  ?>

    <div id="discussion-list">
        <?= $this->view('discussions/_list_items', ['threads' => $threads, 'table' => $table, 'searchUrl' => $searchUrl]); ?>
    </div>

<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>

    <?php if (auth()->user()?->can('threads.create')): ?>
        <div class="w-full" hx-boost="true">
            <a class="btn btn-primary mt-4 w-full" href="<?= url_to('thread-create'); ?>">
                Start a Discussion
            </a>
        </div>
    <?php endif; ?>

    <?php if (auth()->loggedIn()): ?>
        <div id="mute-thread-cell"></div>
    <?php endif; ?>

    <?= view_cell('CategoryListCell', ['activeCategory' => $activeCategory ?? null]) ?>

<?= $this->endSection() ?>
