<div class="thread mt-6 p-6 border rounded bg-base-200 border-base-300">
    <h3 class="text-xl leading-loose font-bold">
        <a href="<?= esc($thread->link(), 'attr') ?>">
            <?= esc($thread->title) ?>
        </a>
    </h3>

    <?php if ($thread->tags): ?>
        <?= view('discussions/tags/_thread', ['tags' => $thread->tags]) ?>
    <?php endif; ?>

    <!-- Thread Meta -->
    <div class="thread-meta">
        <div class="flex">
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
    </div>

    <!-- Thread Content -->
    <div class="thread-content prose !max-w-full mt-6">
        <?= $thread->render() ?>
    </div>
</div>
