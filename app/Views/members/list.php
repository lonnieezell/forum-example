<?= $this->extend('master')  ?>

<?= $this->section('main')  ?>
    <h1>Members</h1>

    <div class="member-list">
        <?= view_cell('MemberList') ?>
    </div>

<?= $this->endSection() ?>
