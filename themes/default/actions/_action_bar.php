<?php if (auth()->loggedIn()) : ?>
<div class="action-bar">
    <!-- My Actions -->
    <div class="flex-1 text-start">

        <!-- Accept Answer -->
        <?php if ($record instanceof App\Entities\Post &&
            service('policy')->can('threads.manageAnswer', $thread)
            ) : ?>
            <?php if ($thread->answer_post_id === null): ?>
                <?= form_open(route_to('thread-set-answer', $thread->id), [
                    'hx-post' => route_to('thread-set-answer', $thread->id),
                    'class' => 'inline-block'
                ]); ?>
                    <?= form_hidden('post_id', $post->id); ?>

                    <button type="submit" class="action-btn" title="Accept this answer">
                        <?= view('icons/check-badge') ?>
                    </button>
                <?= form_close(); ?>
            <?php elseif ($post->isAnswer($thread)): ?>
                <?= form_open(route_to('thread-unset-answer', $thread->id), [
                    'hx-post' => route_to('thread-unset-answer', $thread->id),
                    'class' => 'inline-block',
                ]); ?>
                <?= form_hidden('post_id', $post->id); ?>

                <button type="submit" class="action-btn text-green-600  opacity-100"
                    title="Remove Answer">
                    <?= view('icons/check-badge-solid') ?>
                </button>
                <?= form_close(); ?>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Edit Thread -->
        <?php if ($record instanceof App\Entities\Thread &&
            auth()->user()?->can('threads.edit')
        ) : ?>
            <a class="action-btn"
                title="Edit this thread"
                hx-get="<?= route_to('thread-edit', $record->id); ?>"
                hx-target="#thread"
                hx-swap="innerHTML show:top"
                hx-trigger="click throttle:1s"
            >
                <?= view('icons/pencil') ?>
            </a>
        <?php endif ?>

        <!-- Delete Thread -->
        <?php if ($record instanceof App\Entities\Thread &&
            auth()->user()?->can('threads.delete')
        ) : ?>
            <a class="action-btn"
                title="Delete this thread"
                hx-confirm='Are you sure you want to delete this thread?'
                hx-get="<?= route_to('thread-delete', $record->id); ?>"
                hx-trigger="click throttle:1s"
            >
                <?= view('icons/trash') ?>
            </a>
        <?php endif ?>

        <!-- Edit Post -->
        <?php if ($record instanceof App\Entities\Post &&
            !$record->isMarkedAsDeleted()  &&
            auth()->user()?->can('posts.edit')
        ) : ?>
            <a class="action-btn"
                title="Edit this post"
                hx-get="<?= route_to('post-edit', $record->id); ?>"
                hx-target="closest .post"
                hx-swap="outerHTML show:top"
                hx-trigger="click throttle:1s"
            >
                <?= view('icons/pencil') ?>
            </a>
        <?php endif ?>

        <!-- Delete Post -->
        <?php if ($record instanceof App\Entities\Post &&
            !$record->isMarkedAsDeleted()  &&
            auth()->user()?->can('posts.delete')
        ) : ?>
            <a class="action-btn"
                title="Delete this post"
                hx-confirm='Are you sure you want to delete this post?'
                hx-get="<?= route_to('post-delete', $record->id); ?>"
                hx-trigger="click throttle:1s"
            >
                <?= view('icons/trash') ?>
            </a>
        <?php endif ?>

    </div>

    <!-- Community Actions -->
    <div class="flex-1 text-end">

        <!-- Reply to a Thread -->
        <?php if ($record instanceof App\Entities\Thread && auth()->user()?->can('posts.create')): ?>
            <a class="action-btn" title="Post a reply"
                hx-get="<?= route_to('post-create', $thread->id); ?>"
                hx-target="#post-reply"
                hx-swap="innerHTML show:top"
            >
                <?= view('icons/reply') ?>
            </a>
        <?php endif; ?>

        <!-- Replay to a Post -->
        <?php if ($record instanceof App\Entities\Post && auth()->user()?->can('posts.create')): ?>
            <a class="action-btn"
                title="Reply to this post"
                hx-get="<?= route_to('post-create-reply', $record->thread_id, $record->reply_to ?? $record->id); ?>"
                hx-target="#post-reply-<?= $record->reply_to ?? $record->id; ?>"
                hx-swap="innerHTML show:top"
                hx-trigger="click throttle:1s"
            >
                <?= view('icons/reply') ?>
            </a>
        <?php endif; ?>

        <!-- Report Content -->
        <?php if ($record->author_id == user_id() || auth()->user()?->can('content.report')): ?>
            <a class="action-btn" title="Report this content"
                title="Report this content"
                hx-get="<?= route_to($record instanceof App\Entities\Thread ? 'thread-report' : 'post-report', $record->id); ?>"
                hx-target="#modal-container"
                hx-trigger="click throttle:1s"
            >
                <?= view('icons/flag') ?>
            </a>
        <?php endif ?>

    </div>
</div>
<?php endif ?>
