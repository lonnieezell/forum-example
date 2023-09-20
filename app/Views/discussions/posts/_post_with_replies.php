<div class="post-container my-6">

    <?= view('discussions/posts/_post', ['post' => $post]) ?>

    <!-- Replies -->
    <?php if (isset($post->replies)) : ?>
        <div class="post-replies ml-10">
            <?php if ($post->replies_count > 2): ?>
                <div class="my-6 text-center">
                    <a class="btn btn-xs btn-primary"
                       hx-get="<?= route_to('post-replies-load', $post->id); ?>"
                       hx-target="closest .post-replies"
                       hx-swap="innerHTML show:top"
                    >
                        Load previous replies
                    </a>
                </div>
            <?php endif; ?>
            <?php foreach ($post->replies as $reply) : ?>
                <?= view('discussions/posts/_post_with_replies', ['post' => $reply]) ?>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <?php if ($post->reply_to === null): ?>
        <div class="ml-10">
            <div id="post-reply-<?= $post->id; ?>" class="<?= $post->replies_count === 0 ? 'my-6' : '' ?>"></div>
        </div>
    <?php endif; ?>
</div>
