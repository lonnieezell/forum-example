<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <?= view('account/_header', ['user' => $user]) ?>
<?= $this->endSection() ?>


<?= $this->section('main')  ?>
    <?= view('account/_post-head', [
        'title' => 'My Posts',
        'subTitle' => 'Any posts that you have created while replying to other discussions.'
    ]) ?>

<?= $this->endSection() ?>


<?= $this->section('sidebar')  ?>
    <?= view('account/_sidebar') ?>
<?= $this->endSection() ?>
