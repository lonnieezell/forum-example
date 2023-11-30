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

<body hx-ext="loading-states" class="pb-12">

    <?= $this->include('_app_nav') ?>

    <?= $this->renderSection('header') ?>

    <div class="flex flex-col sm:flex-row container mx-auto gap-4" id="main-with-sidebar">
        <div class="flex-1 px-1 sm:px-4 pt-4 pb-8 order-last sm:<?= (auth()->user()?->handed ?? 'right') === 'right'  ? 'order-first' : 'order-last' ?>" id="main">
            <?= $this->renderSection('main') ?>
        </div>

        <div class="flex-0 flex flex-col gap-4 w-full px-4 sm:w-64 pb-8 order-first sm:<?= (auth()->user()?->handed ?? 'right') === 'right' ? 'order-last' : 'order-first' ?>" id="sidebar">
            <?= $this->renderSection('sidebar') ?>
        </div>
    </div>

    <div id="modal-container"></div>

    <?= alerts()->container(); ?>

    <?= $this->renderSection('scripts') ?>
</body>

</html>
