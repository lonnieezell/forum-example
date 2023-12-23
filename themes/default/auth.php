<!doctype html>
<html class="no-js" lang="en" data-theme="<?= get_cookie('theme') ?>">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?><?= config('App')->siteName ?></title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="icon.png">

    <?= vite(['themes/default/js/app.js', 'themes/default/css/app.scss']) ?>
    <script src="https://kit.fontawesome.com/9ce245a629.js" crossorigin="anonymous"></script>
    <?= $this->renderSection('styles') ?>
</head>

<body class="bg-base-300 h-screen" hx-ext="loading-states">

    <?= $this->include('_app_nav') ?>

    <div class="px-1 sm:px-4 pt-4 pb-8" id="main">
        <?= $this->renderSection('main') ?>
    </div>

    <?= $this->view('_footer') ?>

    <?= alerts()->container(); ?>

    <?= $this->renderSection('scripts') ?>
</body>

</html>
