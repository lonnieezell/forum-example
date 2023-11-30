<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <?= $this->view('account/_header', ['user' => $user]) ?>
<?= $this->endSection() ?>


<?= $this->section('main')  ?>
    <?= $this->view('account/_post-head', [
        'title' => 'My Threads',
        'subTitle' => 'Any threads that you have created.'
    ]) ?>

    <?php foreach ($threads as $thread): ?>
        <?= $this->view('account/_thread', ['thread' => $thread, 'user' => $user]); ?>
    <?php endforeach; ?>

    <div class="mt-6 text-center" hx-boost="true">
        <?= $pager->links() ?>
    </div>

<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>
    <?= $this->view('account/_sidebar') ?>
<?= $this->endSection() ?>
