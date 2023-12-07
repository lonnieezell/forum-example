<?= $this->extend('discussion_layout')  ?>

<?= $this->section('header') ?>
    <div class="header-row">
        <div>
            <h2>
                <a href="<?= url_to('discussions') ?>">
                    Discussions
                </a>
            </h2>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('main')  ?>
    <div id="thread-wrap">
        <?= $this->view('discussions/threads/_thread', ['thread' => $thread]) ?>

        <?php if ($answer !== null): ?>
            <?= $this->view('discussions/posts/_post', [
                'post' => $answer,
                'thread' => $thread,
                'standaloneAnswer' => true,
            ], ['saveData' => false]) ?>
        <?php endif; ?>

        <!-- Replies -->
        <?php if (! empty($posts)) : ?>
            <div id="replies" class="mt-6">

                <div id="replies-content">
                    <?= $this->view('discussions/_thread_items', ['posts' => $posts, 'thread' => $thread, 'loadedReplies' => $loadedReplies]) ?>
                </div>

                <div id="replies-footer">
                    <?php if (auth()->user()?->can('posts.create')): ?>
                        <div class="<?= (auth()->user()?->handed ?? 'right') === 'right' ? 'text-right' : 'text-left' ?>">
                            <a class="btn btn-primary btn-block sm:btn-wide"
                               hx-get="<?= route_to('post-create', $thread->id); ?>"
                               hx-target="#post-reply"
                               hx-swap="innerHTML show:top"
                            >
                                Post reply
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-6" hx-boost="true" hx-target="#replies" hx-select="#replies">
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
<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>

    <?php if (auth()->loggedIn()): ?>
        <div id="mute-thread-cell" <?= request()->is('htmx') ? 'hx-swap-oob="true"' : '' ?>>
            <?= view_cell('MuteThreadCell', ['userId' => user_id(), 'threadId' => $thread->id]) ?>
        </div>
    <?php endif; ?>

<?= $this->endSection() ?>
