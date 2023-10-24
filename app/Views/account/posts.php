<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <?= view('account/_header', ['user' => $user]) ?>
<?= $this->endSection() ?>


<?= $this->section('main')  ?>
    <?= view('account/_post-head', [
        'title' => 'My Posts',
        'subTitle' => 'Any posts that you have created while replying to other discussions.'
    ]) ?>

    <?php foreach ($posts as $post): ?>
        <?= view('account/_post', ['post' => $post, 'user' => $user]); ?>
    <?php endforeach; ?>

    <div class="mt-6 text-center" hx-boost="true">
        <?= $pager->links() ?>
    </div>

<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>
    <?= view('account/_sidebar') ?>
<?= $this->endSection() ?>
