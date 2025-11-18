<?= $this->extend('discussion_layout')  ?>

<?= $this->section('header') ?>
    <div class="header-row">
        <div>
            <h2 class="text-neutral-600">
                <span>Discussions in </span>
                <?= esc($activeCategory->title); ?>
            </h2>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('main')  ?>

    <div id="discussion-list">
        <?= $this->view('discussions/_list_items', ['threads' => $threads, 'table' => $table, 'searchUrl' => $searchUrl]); ?>
    </div>

<?= $this->endSection() ?>
