<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?><?= config('App')->siteName ?></title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="icon.png">

    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <script src="https://kit.fontawesome.com/9ce245a629.js" crossorigin="anonymous"></script>
    <?= $this->renderSection('styles') ?>
</head>

<body>

    <?= $this->include('_app_nav') ?>

    <div class="container mx-auto px-4 py-8" id="main">
        <?= $this->renderSection('main') ?>
    </div>

    <script src="<?= base_url('js/app.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>
