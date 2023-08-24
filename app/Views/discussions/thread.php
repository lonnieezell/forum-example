<?= $this->extend('master')  ?>

<?= $this->section('main')  ?>
    <div id="thread-wrap">
        <?= view_cell('ShowThread', ['slug' => $slug]) ?>
    </div>
<?= $this->endSection() ?>
