<div class="post p-6 rounded bg-base-100 <?= $post->isAnswer($thread) ? 'post-answer' : ''; ?> <?= isset($standaloneAnswer) ? 'pt-0 pb-10 shadow-lg mt-6 mb-12' : ''; ?>"
    id="post-<?= $post->id ?>"
>

    <!-- Answer Bar -->
    <?php if (isset($standaloneAnswer)) : ?>
        <div class="answer-bar">
            <?= view('icons/check-badge') ?> &nbsp;
            Accepted Answer
        </div>
    <?php endif ?>

    <!-- Post Meta -->
    <div class="post-meta">
        <div class="flex gap-4">
            <div class="avatar">
                <div class="mask mask-squircle">
                    <?php if (! $post->isMarkedAsDeleted()): ?>
                        <?= $post->author->renderAvatar(48); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex-1 opacity-50 dark:opacity-100">
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
                <p><?= \CodeIgniter\I18n\Time::parse($post->created_at)->humanize() ?></p>
            </div>
        </div>
    </div>

    <!-- Post Content -->
    <div class="post-content prose lg:prose-lg max-w-none mt-6">
        <?php if ($post->isMarkedAsDeleted()): ?>
            <i>Message not available.</i>
        <?php else: ?>
            <?= $post->render() ?>

            <?= $this->view('discussions/_signature', ['user' => $post->author]) ?>
        <?php endif; ?>
    </div>

    <?php if (! isset($standaloneAnswer)) : ?>
        <?= view_cell('ActionBarCell', ['record' => $post]) ?>
    <?php endif ?>
</div>
