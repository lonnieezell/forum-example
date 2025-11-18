<div class="post-container my-6">

    <?= $this->view('discussions/posts/_post', ['post' => $post, 'thread' => $thread]) ?>

    <!-- Replies -->
    <?php if (isset($post->replies)) : ?>
        <div class="post-replies ml-10">
            <?php if (isset($loadedReplies[$post->id])): ?>
                <?php foreach ($loadedReplies[$post->id] as $reply) : ?>
                    <?= $this->view('discussions/posts/_post_with_replies', ['post' => $reply]) ?>
                <?php endforeach ?>
            <?php else: ?>
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
                    <?= $this->view('discussions/posts/_post_with_replies', ['post' => $reply]) ?>
                <?php endforeach ?>
            <?php endif; ?>
        </div>
    <?php endif ?>
    <?php if ($post->reply_to === null): ?>
        <div class="ml-10">
            <div id="post-reply-<?= $post->id; ?>" class="<?= $post->replies_count === 0 ? 'my-6' : '' ?>"></div>
        </div>
    <?php endif; ?>
</div>
