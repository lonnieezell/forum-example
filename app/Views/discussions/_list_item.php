<div class="flex justify-between p-4 mt-2 border rounded bg-base-200 border-base-300 hover:bg-base-100">
    <div class="flex-1 pr-8">
        <h3 class="text-lg leading-tight font-semibold cursor-pointer mb-2"
            hx-get="<?= esc($thread->link(), 'attr') ?>"
            hx-target="#main"
            hx-push-url="true"
            hx-select="#thread-wrap">
            <?= esc($thread->title) ?>
        </h3>

        <?php if (! $thread->tags->isEmpty()): ?>
            <?= view('discussions/tags/_thread', ['tags' => $thread->tags]) ?>
        <?php endif; ?>

        <?php if (! empty($thread->last_post_author)) : ?>
            <div class="mt-2 text-neutral-400 text-sm">
                <i class="fa-solid fa-reply"></i>
                <strong>
                    <a href="<?= route_to('profile', esc($thread->last_post_author,'attr')); ?>"
                       class="hover:text-neutral"
                    >
                        <?= esc($thread->last_post_author) ?>
                    </a>
                </strong>
                replied <?= \CodeIgniter\I18n\Time::parse($thread->last_post_created_at)->humanize() ?>
                &nbsp;&nbsp;&bull;&nbsp;&nbsp;
                <a href="<?= route_to('category', esc($thread->category_slug,'attr')); ?>"
                    class="hover:text-neutral"
                >
                    <?= esc($thread->category_title) ?>
                </a>
            </div>
        <?php else: ?>
            <div class="mt-2 text-neutral-400 text-sm">
                <i class="fa-solid fa-pen"></i>
                created <?= \CodeIgniter\I18n\Time::parse($thread->updated_at)->humanize() ?>
            </div>
        <?php endif ?>
    </div>

    <div class="flex flex-row gap-4">
        <!-- Thread Count -->
        <div class="flex flex-col p-4 aspect-square rounded bg-base-100 border-2 border-base-200 justify-center content-center text-center">
            <div class="w-full font-bold">
                <?= number_format($thread->views) ?>
            </div>
            <div class="w-full">
                Views
            </div>
        </div>

        <!-- Post Count -->
        <div class="flex flex-col p-4 aspect-square rounded bg-base-100 border-2 border-base-200 justify-center content-center text-center">
            <div class="w-full font-bold">
                <?= number_format($thread->post_count) ?>
            </div>
            <div class="w-full">
                Replies
            </div>
        </div>
    </div>
</div>
