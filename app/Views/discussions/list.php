<?= $this->extend('master')  ?>

<?= $this->section('main')  ?>
    <h1>Discussions</h1>

    <div class="discussion-list">
        <?= view_cell('DiscussionList') ?>
    </div>

<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>

    <?= view_cell('CategoryListCell', ['activeCategory' => $activeCategory ?? null]) ?>

    <?php if (auth()->user()?->can('threads.create')): ?>
        <div class="flex justify-center" hx-boost="true">
            <a class="btn btn-primary mt-4" href="<?= url_to('thread-create'); ?>"">
                Start a Discussion
            </a>
        </div>
    <?php endif; ?>

<?= $this->endSection() ?>
