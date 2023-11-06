<?php

use App\Libraries\CountryHelper;
?>
<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <?= view('account/_header', ['user' => $user]) ?>
<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>
    <?= view('account/_sidebar') ?>
<?= $this->endSection() ?>


<?= $this->section('main')  ?>

    <?= view('account/_post-head', [
        'title' => 'My Profile',
        'subTitle' => 'Set your profile information that will be displayed to the public.'
    ]) ?>

    <?= view('account/_profile', ['user' => $user, 'validator' => $validator]) ?>

<?= $this->endSection() ?>
