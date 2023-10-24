<?= $this->extend('master')  ?>

<?= $this->section('main')  ?>
    <div id="thread-wrap">
        <div id="thread">
            <?= view('discussions/threads/_thread', ['thread' => $thread]) ?>
        </div>

        <!-- Replies -->
        <?php if (! empty($posts)) : ?>
            <div id="replies" class="mt-6">
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
                    <div hx-boost="true" hx-target="#replies" hx-select="#replies">
                        <?= $pager->links() ?>
                    </div>
                </div>

                <div id="replies-content">
                    <?= view('discussions/_thread_items', ['posts' => $posts, 'loadedReplies' => $loadedReplies]) ?>
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
                    <div hx-boost="true" hx-target="#replies" hx-select="#replies">
                        <?= $pager->links() ?>
                    </div>
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
<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>

<?php if (auth()->user()?->can('threads.create')): ?>
    <div class="pt-4" hx-boost="true">
        <a class="btn btn-primary mt-4 w-full" href="<?= url_to('thread-create'); ?>">
            Start a Discussion
        </a>
    </div>
<?php endif; ?>

<?php if (auth()->loggedIn()): ?>
    <div id="mute-thread-cell" <?= request()->is('htmx') ? 'hx-swap-oob="true"' : '' ?>>
        <?= view_cell('MuteThreadCell', ['userId' => user_id(), 'threadId' => $thread->id]) ?>
    </div>
<?php endif; ?>

<?= view_cell('CategoryListCell', ['activeCategory' => $activeCategory ?? null]) ?>

<?= $this->endSection() ?>
