<div>
    <div class="thread mt-6 p-6 border rounded bg-base-200 border-base-300">
        <h3 class="text-xl leading-loose font-bold">
            <a href="<?= esc($thread->link(), 'attr') ?>">
                <?= esc($thread->title) ?>
            </a>
        </h3>

        <!-- Thread Meta -->
        <div class="thread-meta">
            <div class="flex gap-4 ">
                <i class="fa-solid fa-user"></i>
                <div class="flex-1 opacity-50">
                    <i class="fa-solid fa-pencil"></i>
                    <?php if ($thread->author) : ?>
                        <a href="<?= $thread->author->link() ?>">
                            <b><?= esc($thread->author->username) ?></b>
                        </a>
                    <?php endif ?>
                    <?= \CodeIgniter\I18n\Time::parse($thread->created_at)->humanize() ?>
                </div>
            </div>
        </div>

        <!-- Thread Content -->
        <div class="thread-content prose mt-6">
            <?= $thread->render() ?>
        </div>
    </div>

    <!-- Replies -->
    <?php if (! empty($posts)) : ?>
    <div class="replies mt-6">
        <div class="text-center">
            <?= $pager->links() ?>
        </div>

        <?php foreach ($posts as $post) : ?>
            <?= view('discussions/_post', ['post' => $post]) ?>
        <?php endforeach ?>

        <div class="text-center">
            <?= $pager->links() ?>
        </div>
    </div>
    <?php endif ?>
</div>
