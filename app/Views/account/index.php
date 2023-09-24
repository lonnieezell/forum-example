<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <?= view('account/_header', ['user' => $user]) ?>
<?= $this->endSection() ?>


<?= $this->section('main')  ?>
    <h1>My Account</h1>

<?= $this->endSection() ?>


<?= $this->section('sidebar')  ?>
    <?= view('account/_sidebar') ?>
<?= $this->endSection() ?>
