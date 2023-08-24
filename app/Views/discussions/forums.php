<?= $this->extend('master')  ?>

<?= $this->section('main')  ?>
    <?php if (empty($forums)): ?>
        <p>There are no categories to display.</p>
    <?php else: ?>
        <?php foreach ($forums as $forum): ?>
            <div class="mt-6 p-6 border rounded bg-base-200 border-base-300">
                <h2 class="flex justify-between text-xl leading-loose font-bold border-b border-secondary-focus mb-4">
                    <a href="<?= esc($forum->link(), 'attr') ?>">
                        <?= esc($forum->title) ?>
                    </a>

                    <?php if ($forum->private) : ?>
                        <i class="fa-regular fa-eye-slash mt-2 opacity-30"></i>
                    <?php endif; ?>
                </h2>

                <?php foreach($forum->children as $child) : ?>
                    <?= view('discussions/_summary', ['child' => $child]) ?>
                <?php endforeach ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

<?= $this->endSection() ?>
