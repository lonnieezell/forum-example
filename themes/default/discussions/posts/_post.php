<div class="post p-6 rounded bg-base-100 <?= $post->isAnswer($thread) ? 'bg-green-100' : ''; ?>" id="post-<?= $post->id ?>">
    <!-- Post Meta -->
    <div class="post-meta">
        <div class="flex gap-4">
            <div class="avatar">
                <div class="mask mask-squircle">
                    <?php if (! $post->isMarkedAsDeleted()): ?>
                        <?= $post->author->renderAvatar(25); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex-1 opacity-50">
                <i class="fa-solid fa-reply"></i>
                <?php if ($post->isMarkedAsDeleted() && ! isset($post->author)): ?>
                    <i>User Removed</i>
                <?php else: ?>
                    <?php if (isset($post->author)): ?>
                        <a href="<?= $post->author->link() ?>">
                            <b><?= esc($post->author->username) ?></b>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <?= \CodeIgniter\I18n\Time::parse($post->created_at)->humanize() ?>
            </div>
            <div class="flex gap-x-1 text-right">
                <?php if (service('policy')->can('threads.manageAnswer', $thread)): ?>
                    <?php if ($thread->answer_post_id === null): ?>
                        <?= form_open(route_to('thread-set-answer', $thread->id), [
                            'hx-post' => route_to('thread-set-answer', $thread->id),
                        ]); ?>
                            <?= form_hidden('post_id', $post->id); ?>
                            <button type="submit" class="btn btn-xs btn-success text-white">
                                Accept as answer
                            </button>
                        <?= form_close(); ?>
                    <?php elseif ($post->isAnswer($thread)): ?>
                        <?= form_open(route_to('thread-unset-answer', $thread->id), [
                            'hx-post' => route_to('thread-unset-answer', $thread->id),
                            'class' => 'mt-[-1px]',
                        ]); ?>
                        <?= form_hidden('post_id', $post->id); ?>
                        <button type="submit" class="btn btn-xs btn-error text-white">
                            Remove answer
                        </button>
                        <?= form_close(); ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (auth()->user()?->can('posts.create')): ?>
                    <a class="btn btn-xs btn-primary"
                       hx-get="<?= route_to('post-create-reply', $post->thread_id, $post->reply_to ?? $post->id); ?>"
                       hx-target="#post-reply-<?= $post->reply_to ?? $post->id; ?>"
                       hx-swap="innerHTML show:top"
                       hx-trigger="click throttle:1s"
                    >
                        Reply
                    </a>
                <?php endif; ?>
                <?php if (! $post->isMarkedAsDeleted() && ($post->author_id === user_id() || auth()->user()?->can('posts.edit'))): ?>
                    <a class="btn btn-xs btn-primary"
                       hx-get="<?= route_to('post-edit', $post->id); ?>"
                       hx-target="closest .post"
                       hx-swap="outerHTML show:top"
                       hx-trigger="click throttle:1s"
                    >
                        Edit
                    </a>
                <?php endif; ?>
                <?php if ($post->marked_as_deleted === null && $post->author_id !== user_id() && auth()->user()?->can('posts.report')): ?>
                    <a class="btn btn-xs" title="Report this post"
                       hx-get="<?= route_to('post-report', $post->id); ?>"
                       hx-target="#modal-container"
                       hx-trigger="click throttle:1s"
                    >
                        Report
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Post Content -->
    <div class="post-content prose lg:prose-lg max-w-none mt-6">
        <?php if ($post->isMarkedAsDeleted()): ?>
            <i>Message not available.</i>
        <?php else: ?>
            <?= $post->render() ?>
        <?php endif; ?>
    </div>
</div>
