<?= $this->extend('master') ?>

<?= $this->section('main'); ?>
    <h1 class="text-lg font-semibold mb-4">Users Settings</h1>

    <div id="form-wrap">
        <?= $this->include('admin/settings/_users_form') ?>
    </div>

<?= $this->endSection(); ?>
