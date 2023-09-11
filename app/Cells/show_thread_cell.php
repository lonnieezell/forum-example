<div>
    <div id="thread">
        <?= view('discussions/threads/_thread', ['thread' => $thread]) ?>
    </div>

    <!-- Replies -->
    <?php if (! empty($posts)) : ?>
    <div class="replies mt-6">
        <div id="replies-header" class="flex justify-between">
            <?php if (auth()->user()?->can('posts.create')): ?>
                <div>
                    <a class="btn btn-primary"
                       hx-get="<?= route_to('post-create', $thread->id); ?>"
                       hx-target="#post-reply"
                       hx-swap="innerHTML show:top"
                    >
                        Post reply
                    </a>
                </div>
            <?php endif; ?>
            <?= $pager->links() ?>
        </div>

        <div id="replies-content">
            <?php foreach ($posts as $post) : ?>
                <?= view('discussions/posts/_post', ['post' => $post]) ?>
            <?php endforeach ?>
        </div>

        <div id="replies-footer" class="flex justify-between">
            <?php if (auth()->user()?->can('posts.create')): ?>
                <div>
                    <a class="btn btn-primary"
                       hx-get="<?= route_to('post-create', $thread->id); ?>"
                       hx-target="#post-reply"
                       hx-swap="innerHTML show:top"
                    >
                        Post reply
                    </a>
                </div>
            <?php endif; ?>
            <?= $pager->links() ?>
        </div>
    </div>
    <?php else: ?>
        <div id="replies-content"></div>
        <div class="text-center mt-6">
            <a class="btn btn-primary"
               hx-get="<?= route_to('post-create', $thread->id); ?>"
               hx-target="#post-reply"
               hx-swap="innerHTML show:top"
            >
                Be first to post a reply
            </a>
        </div>
    <?php endif ?>

    <div id="post-reply"></div>
</div>
