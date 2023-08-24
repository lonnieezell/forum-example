<div class="flex justify-between mt-4 p-4 border rounded bg-base-200 border-base-300 cursor-pointer hover:bg-base-300"
    hx-get="<?= esc($child->link(), 'attr') ?>"
    hx-target="#main"
>
    <div class="flex-1 pr-8">
        <h3 class="text-lg leading-tight font-semibold">
            <a>
                <?= esc($child->title) ?>
            </a>
        </h3>

        <?php if($child->description) : ?>
            <p class="mt-2 text-base leading-base opacity-50">
                <?= esc($child->description) ?>
            </p>
        <?php endif ?>

        <?php if (! empty($child->last_thread_title)) : ?>
            <div class="mt-4 text-neutral-content text-sm opacity-80">
                Most Recent:
                    <em><?= esc($child->last_thread_title) ?></em>
                    by
                    <b><?= esc($child->last_thread_author) ?></b>
                    - <?= \CodeIgniter\I18n\Time::parse($child->last_thread_updated_at)->humanize() ?>
            </div>
        <?php endif ?>
    </div>

    <div class="flex-none gap-4 flex flex-row">
        <!-- Thread Count -->
        <div class="flex flex-col py-2 px-6 aspect-square rounded bg-base-100 justify-center content-center text-center">
            <div class="w-full font-bold">
                <?= number_format($child->thread_count) ?>
            </div>
            <div class="w-full">
                Threads
            </div>
        </div>

        <!-- Post Count -->
        <div class="flex flex-col py-2 px-6 aspect-square rounded bg-base-100 justify-center content-center text-center">
            <div class="w-full font-bold">
                <?= number_format($child->post_count) ?>
            </div>
            <div class="w-full">
                Replies
            </div>
        </div>
    </div>
</div>
