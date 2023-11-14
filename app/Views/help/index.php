<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <?= view('help/_header') ?>
<?= $this->endSection() ?>

<?= $this->section('main')  ?>

    <div id="help-content-container">
        <?= view('help/_index', ['pages' => $pages]) ?>
    </div>

<?= $this->endSection() ?>
