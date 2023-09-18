<div class="flex justify-between p-2 mt-2 border rounded bg-base-200 border-base-300 cursor-pointer hover:bg-base-300"
     hx-get="<?= esc($thread->link(), 'attr') ?>"
     hx-target="#main"
     hx-push-url="true"
     hx-select="#thread-wrap"
>
    <div class="flex-1 pr-8">
        <h3 class="text-lg leading-tight font-semibold">
            <?= esc($thread->title) ?>
        </h3>
        <?php if ($thread->tags): ?>
        <div>
            <?php foreach ($thread->tags as $tag): ?>
                <a class="btn btn-xs"
                   href="<?= route_to('tag', esc($tag, 'attr')); ?>">
                    <?= esc($tag); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (! empty($thread->last_post_author)) : ?>
            <div class="mt-4 text-neutral-content text-sm opacity-80">
                <i class="fa-solid fa-reply"></i>
                <b><?= esc($thread->last_post_author) ?></b>
                replied <?= \CodeIgniter\I18n\Time::parse($thread->last_post_created_at)->humanize() ?>
                &nbsp;&nbsp;&bull;&nbsp;&nbsp;
                <?= esc($thread->category_title) ?>
            </div>
        <?php else: ?>
            <div class="mt-4 text-neutral-content text-sm opacity-80">
                <i class="fa-solid fa-pen"></i>
                created <?= \CodeIgniter\I18n\Time::parse($thread->updated_at)->humanize() ?>
            </div>
        <?php endif ?>
    </div>

    <div class="flex-none gap-4 flex flex-row">
        <!-- Thread Count -->
        <div class="flex flex-col p-2 aspect-square rounded bg-base-100 justify-center content-center text-center">
            <div class="w-full font-bold">
                <?= number_format($thread->views) ?>
            </div>
            <div class="w-full">
                Views
            </div>
        </div>

        <!-- Post Count -->
        <div class="flex flex-col p-2 aspect-square rounded bg-base-100 justify-center content-center text-center">
            <div class="w-full font-bold">
                <?= number_format($thread->post_count) ?>
            </div>
            <div class="w-full">
                Replies
            </div>
        </div>
    </div>
</div>
