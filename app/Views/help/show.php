<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
<?= view('help/_header', ['page' => $page]) ?>
<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>
<?= view('help/_sidebar') ?>
<?= $this->endSection() ?>

<?= $this->section('main')  ?>

    <?= $page->parse()->getContent(); ?>

<?= $this->endSection() ?>
