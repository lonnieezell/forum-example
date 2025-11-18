<div class="post my-6 p-6 rounded bg-base-100 shadow-xl cursor-pointer"
     hx-get="<?= $post->link(); ?>"
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
                <i class="fa-solid fa-reply"></i>
                <b><?= esc($user->username) ?></b>
                <?= \CodeIgniter\I18n\Time::parse($post->created_at)->humanize() ?>
            </div>
            <div class="flex-auto text-right">
                <small>
                    Thread: <strong><?= esc($post->thread_title); ?></strong> &bull;
                    Category: <strong><?= esc($post->category_title); ?></strong>
                </small>
            </div>
        </div>
    </div>

    <!-- Post Content -->
    <div class="post-content prose !max-w-full mt-6">
        <?= ellipsize(strip_tags($post->render()), 150) ?>
    </div>
</div>
