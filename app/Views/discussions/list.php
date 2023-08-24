<?= $this->extend('master')  ?>

<?= $this->section('main')  ?>
    <h1>Discussions</h1>

    <div class="discussion-list">
        <?= view_cell('DiscussionList') ?>
    </div>

<?= $this->endSection() ?>
