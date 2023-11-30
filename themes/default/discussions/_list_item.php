<div class="discussion-list-item flex flex-col sm:flex-row gap-4 justify-between px-2 sm:px-4 py-6 border border-transparent hover:border-base-300 hover:cursor-pointer"
    hx-get="<?= esc($thread->link(), 'attr') ?>"
    hx-target="#main"
    hx-push-url="true"
    hx-select="#thread-wrap">

    <div class="flex-1">
        <h3 class="text-lg leading-tight font-semibold cursor-pointer mb-1">
            <?= esc($thread->title) ?>
        </h3>

        <div class="discussion-meta">
            <a href="<?= route_to('category', esc($thread->category_slug,'attr')); ?>"
                class="text-sm text-neutral-500 hover:text-neutral mr-4"
                >
                    <?= esc($thread->category_title) ?>
            </a>
        </div>

        <div class="discussion-meta mt-1 text-neutral-400 text-sm">
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
        </div>
    </div>

    <div class="flex-0 flex flex-row gap-4 w-full sm:w-44 ">
        <!-- Thread Count -->
        <div class="flex flex-row sm:flex-col sm:aspect-square justify-center content-center text-center">
            <div class="discussion-stat">
                <?= number_format($thread->views) ?>
            </div>
            <div class="discussion-stat-label">
                Views
            </div>
        </div>

        <!-- Post Count -->
        <div class="flex flex-row sm:flex-col sm:aspect-square justify-center content-center text-center">
            <div class="discussion-stat">
                <?= number_format($thread->post_count) ?>
            </div>
            <div class="discussion-stat-label">
                Replies
            </div>
        </div>
    </div>
</div>
