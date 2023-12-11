<?= $this->extend('master')  ?>

<?= $this->section('sidebar')  ?>
    <?= $this->view('discussions/_sidebar', ['activeCategory' => $activeCategory ?? null]) ?>
<?= $this->endSection() ?>
