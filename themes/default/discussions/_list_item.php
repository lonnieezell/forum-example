<div class="discussion-list-item flex justify-between px-4 py-6 border border-transparent hover:border-base-300 hover:cursor-pointer"
    hx-get="<?= esc($thread->link(), 'attr') ?>"
    hx-target="#main"
    hx-push-url="true"
    hx-select="#thread-wrap">
    <div class="flex-1 pr-8">
        <h3 class="text-lg leading-tight font-semibold cursor-pointer mb-2">
            <?= esc($thread->title) ?>
        </h3>

        <?php if (! $thread->tags->isEmpty()): ?>
            <?= $this->view('discussions/tags/_thread', ['tags' => $thread->tags]) ?>
        <?php endif; ?>

        <div class="discussion-meta mt-2 text-neutral-400 text-sm">
            <?php if (! empty($thread->last_post_author)) : ?>
                <i class="fa-solid fa-reply"></i>
                <strong>
                    <a href="<?= route_to('profile', esc($thread->last_post_author,'attr')); ?>"
                       class="hover:text-neutral"
                    >
                        <?= esc($thread->last_post_author) ?>
                    </a>
                </strong>
                replied <?= \CodeIgniter\I18n\Time::parse($thread->last_post_created_at)->humanize() ?>
            <?php else: ?>
                <i class="fa-solid fa-pen"></i>
                <strong>
                    <a href="<?= $thread->author->link() ?>"
                       class="hover:text-neutral"
                    >
                        <?= esc($thread->author->username) ?>
                    </a>
                </strong>
                created <?= \CodeIgniter\I18n\Time::parse($thread->updated_at)->humanize() ?>
            <?php endif ?>
            &nbsp;&nbsp;&bull;&nbsp;&nbsp;
            <a href="<?= route_to('category', esc($thread->category_slug,'attr')); ?>"
               class="hover:text-neutral"
            >
                <?= esc($thread->category_title) ?>
            </a>
        </div>
    </div>

    <div class="flex flex-row gap-6 mr-4">
        <!-- Thread Count -->
        <div class="flex flex-col aspect-square justify-center content-center text-center">
            <div class="w-full">
                <?= number_format($thread->views) ?>
            </div>
            <div class="w-full text-neutral-400 text-sm">
                Views
            </div>
        </div>

        <!-- Post Count -->
        <div class="flex flex-col aspect-square justify-center content-center text-center">
            <div class="w-full">
                <?= number_format($thread->post_count) ?>
            </div>
            <div class="w-full text-neutral-400 text-sm">
                Replies
            </div>
        </div>
    </div>
</div>
