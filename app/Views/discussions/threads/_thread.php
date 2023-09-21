<div class="thread mt-6 px-6 py-4 border rounded bg-base-200 border-base-300">
    <h3 class="text-xl leading-loose font-bold pb-2">
        <a href="<?= esc($thread->link(), 'attr') ?>">
            <?= esc($thread->title) ?>
        </a>
    </h3>

    <!-- Thread Meta -->
    <div class="thread-meta">
        <div class="flex">
            <div class="avatar pr-2">
                <div class="mask mask-squircle">
                    <?= $thread->author->renderAvatar(25); ?>
                </div>
            </div>
            <div class="flex-1 opacity-50">
                <?php if ($thread->author) : ?>
                    <a href="<?= $thread->author->link() ?>">
                        <b><?= esc($thread->author->username) ?></b>
                    </a>
                <?php endif ?>
                <?= \CodeIgniter\I18n\Time::parse($thread->created_at)->humanize() ?>
            </div>
            <div class="flex-auto text-right">
                <?php if ($thread->author_id === user_id() || auth()->user()?->can('threads.edit')): ?>
                <a class="btn btn-xs btn-primary"
                   hx-get="<?= route_to('thread-edit', $thread->id); ?>"
                   hx-target="#thread"
                   hx-swap="innerHTML show:top"
                >
                    Edit
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($thread->tags): ?>
            <?= view('discussions/tags/_thread', ['tags' => $thread->tags]) ?>
        <?php endif; ?>
    </div>

    <!-- Thread Content -->
    <div class="thread-content prose !max-w-full mt-6">
        <?= $thread->render() ?>
    </div>
</div>
