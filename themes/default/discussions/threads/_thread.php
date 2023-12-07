<div class="thread px-6 pb-4">
    <h3 class="text-3xl leading-loose font-bold pb-2">
        <a href="<?= esc($thread->link(), 'attr') ?>">
            <?= esc($thread->title) ?>
        </a>
    </h3>

    <!-- Thread Meta -->
    <div class="thread-meta">
        <div class="flex">
            <div class="avatar pr-4">
                <div class="mask mask-squircle">
                    <?= $thread->author->renderAvatar(72); ?>
                </div>
            </div>
            <div class="flex-1 opacity-50 dark:opacity-100">
                <?php if ($thread->author) : ?>
                    <a href="<?= $thread->author->link() ?>">
                        <b><?= esc($thread->author->username) ?></b>
                    </a>
                <?php endif ?>
                <p><?= \CodeIgniter\I18n\Time::parse($thread->created_at)->humanize() ?></p>

                <?php if (! $thread->tags->isEmpty()): ?>
                    <div>
                        <?= $this->view('discussions/tags/_thread', ['tags' => $thread->tags]) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Thread Content -->
    <div class="flex flex-col">
        <div class="thread-content prose lg:prose-lg max-w-none container mt-6">
            <?= $thread->render() ?>
        </div>

        <?= $this->view('actions/_action_bar', ['record' => $thread]) ?>
    </div>
</div>
