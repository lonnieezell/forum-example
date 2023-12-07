<?= $this->extend('discussion_layout')  ?>

<?= $this->section('header') ?>
    <div class="header-row">
        <div>
            <h2><span>Discussions tagged </span><?= esc($table['search']['tag']); ?></h2>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('main')  ?>

    <div id="discussion-list">
        <?= $this->view('discussions/_list_items', ['threads' => $threads, 'table' => $table, 'searchUrl' => $searchUrl]); ?>
    </div>

<?= $this->endSection() ?>
