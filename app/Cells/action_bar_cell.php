<?php

use App\Entities\Thread;

if (auth()->loggedIn()) : ?>
<div class="action-bar">
    <!-- My Actions -->
    <div class="flex-1 text-start">

        <!-- Accept Answer -->
        <?php if ($this->canManageAnswer()) : ?>
            <?php if ($thread->answer_post_id === null): ?>
                <?= form_open(route_to('thread-set-answer', $thread->id), [
                    'hx-post' => route_to('thread-set-answer', $thread->id),
                    'class'   => 'inline-block',
                ]); ?>
                    <?= form_hidden('post_id', $record->id); ?>

                    <button type="submit" class="action-btn" title="Accept this answer">
                        <?= view('icons/check-badge') ?>
                    </button>
                <?= form_close(); ?>
            <?php elseif ($record->isAnswer($thread)): ?>
                <?= form_open(route_to('thread-unset-answer', $thread->id), [
                    'hx-post' => route_to('thread-unset-answer', $thread->id),
                    'class'   => 'inline-block',
                ]); ?>
                <?= form_hidden('post_id', $record->id); ?>

                <button type="submit" class="action-btn text-green-600  opacity-100"
                    title="Remove Answer">
                    <?= view('icons/check-badge-solid') ?>
                </button>
                <?= form_close(); ?>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Edit Thread -->
        <?php if ($this->isThread() && $this->canEdit()) : ?>
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
        <?php if ($this->isThread() && $this->canDelete()) : ?>
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
        <?php if ($this->isPost() && $this->canEdit()) : ?>
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
        <?php if ($this->isPost() && $this->canDelete()) : ?>
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

        <!-- React -->
        <?php if (! $record->isOwner()): ?>
            <?= theme()->render('discussions/_reactions', [
                'record'       => $record,
                'resourceId'   => $record->id,
                'resourceType' => $record instanceof Thread ? 'thread' : 'post',
            ]); ?>
        <?php endif ?>

        <!-- Report Content -->
        <?php if ($this->canReport()): ?>
            <a class="action-btn" title="Report this content"
                title="Report this content"
                hx-get="<?= route_to($record instanceof Thread ? 'thread-report' : 'post-report', $record->id); ?>"
                hx-target="#modal-container"
                hx-trigger="click throttle:1s"
            >
                <?= view('icons/flag') ?>
            </a>
        <?php endif ?>

        <!-- Reply to a Thread -->
        <?php if ($this->isThread() && $this->canReply()): ?>
            <a class="action-btn" title="Post a reply"
                hx-get="<?= route_to('post-create', $thread->id); ?>"
                hx-target="#post-reply"
                hx-swap="innerHTML show:top"
            >
                <?= view('icons/reply') ?>
            </a>
        <?php endif; ?>

        <!-- Reply to a Post -->
        <?php if ($this->isPost() && $this->canReply()): ?>
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

    </div>
</div>
<?php endif ?>
