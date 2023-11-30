<?php

use App\Libraries\CountryHelper;
?>
<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <?= $this->view('account/_header', ['user' => $user]) ?>
<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>
    <?= $this->view('account/_sidebar') ?>
<?= $this->endSection() ?>


<?= $this->section('main')  ?>

    <?= $this->view('account/_post-head', [
        'title' => 'My Profile',
        'subTitle' => 'Set your profile information that will be displayed to the public.'
    ]) ?>

    <?= $this->view('account/_profile', [
        'user' => $user,
        'validator' => $validator,
        'maxUpload' => $maxUpload,
    ]) ?>

<?= $this->endSection() ?>
