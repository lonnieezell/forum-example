<div class="post my-6 p-6 rounded bg-base-100 shadow-xl cursor-pointer"
     hx-get="<?= $thread->link(); ?>"
     hx-push-url="true"
     hx-target="body">
    <!-- Post Meta -->
    <div class="post-meta">
        <div class="flex gap-4">
            <div class="avatar">
                <div class="mask mask-squircle">
                    <?= $user->renderAvatar(25); ?>
                </div>
            </div>
            <div class="flex-1 opacity-50">
                <b><?= esc($user->username) ?></b>
                <?= $thread->created_at->humanize() ?>
            </div>
            <div class="flex-auto text-right">
                <small>
                    Thread: <strong><?= esc($thread->thread_title); ?></strong> &bull;
                    Category: <strong><?= esc($thread->category_title); ?></strong>
                </small>
            </div>
        </div>
        <?php if (! $thread->tags->isEmpty()): ?>
            <?= $this->view('discussions/tags/_thread', ['tags' => $thread->tags]) ?>
        <?php endif; ?>
    </div>

    <!-- Post Content -->
    <div class="post-content prose !max-w-full mt-6">
        <?= ellipsize(strip_tags($thread->render()), 150) ?>
    </div>
</div>
