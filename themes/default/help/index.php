<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <?= $this->view('help/_header') ?>
<?= $this->endSection() ?>

<?= $this->section('main')  ?>

    <div id="help-content-container">
        <?= $this->view('help/_index', ['pages' => $pages]) ?>
    </div>

<?= $this->endSection() ?>
