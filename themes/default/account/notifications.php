<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <?= $this->view('account/_header', ['user' => $user]) ?>
<?= $this->endSection() ?>


<?= $this->section('main')  ?>

    <?= $this->view('account/_post-head', [
        'title' => 'My Notifications',
        'subTitle' => 'Your notification settings for discussions and new posts.'
    ]) ?>

    <?= $this->view('account/_notifications', [
        'notification' => $notification,
        'validator'    => $validator,
]) ?>

<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>
    <?= $this->view('account/_sidebar') ?>
<?= $this->endSection() ?>
