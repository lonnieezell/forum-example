<div>
    <div class="thread prose p-6  rounded bg-base-100">
        <h3 class="text-xl leading-loose font-bold">
            <a href="<?= esc($post->link(), 'attr') ?>">
                <?= esc($post->title) ?>
            </a>
        </h3>

        <!-- Thread Meta -->
        <div class="thread-meta">
            <div class="flex gap-4 ">
                <i class="fa-solid fa-user"></i>
                <div class="flex-1 opacity-50">
                    <i class="fa-solid fa-reply"></i>
                    <?php if ($post->author) : ?>
                        <a href="<?= $post->author->link() ?>">
                            <b><?= esc($post->author->username) ?></b>
                        </a>
                    <?php endif ?>
                    <?= \CodeIgniter\I18n\Time::parse($post->created_at)->humanize() ?>
                </div>
                <div class="flex-auto text-right">
                    <a class="btn btn-xs btn-primary"
                       hx-get="<?= route_to('post-create-reply', $post->thread_id, $post->id); ?>"
                       hx-target="#post-reply"
                       hx-swap="innerHTML show:top"
                    >
                        Reply
                    </a>
                </div>
            </div>
        </div>

        <!-- Thread Content -->
        <div class="thread-content prose !max-w-full mt-6">
            <?= $post->render() ?>
        </div>
    </div>

    <!-- Replies -->
    <?php if (! empty($posts)) : ?>
    <div class="replies mt-6">
        <?php foreach ($posts as $post) : ?>
            <?= view('discussions/_post', ['post' => $post]) ?>
        <?php endforeach ?>

        <?= $pager->links() ?>
    </div>
    <?php endif ?>
</div>
