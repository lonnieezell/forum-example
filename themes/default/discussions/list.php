<?= $this->extend('discussion_layout')  ?>

<?= $this->section('header') ?>
    <div class="header-row">
        <div>
            <h2>Discussions</h2>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('main')  ?>

    <div class="mt-4" id="discussion-list">
        <?= $this->view('discussions/_list_items', ['threads' => $threads, 'table' => $table, 'searchUrl' => $searchUrl]); ?>
    </div>

<?= $this->endSection() ?>
