<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
<?= view('account/_header', ['user' => $user]) ?>
<?= $this->endSection() ?>


<?= $this->section('main')  ?>

<?= view('account/_post-head', [
    'title' => 'My Notifications',
    'subTitle' => 'Your notification settings for discussions and new posts.'
]) ?>

<?= view('account/_notifications', [
    'notification' => $notification,
    'validator'    => $validator,
]) ?>

<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>
<?= view('account/_sidebar') ?>
<?= $this->endSection() ?>
