<div class="post p-6 rounded bg-base-100 shadow-xl">
    <!-- Post Meta -->
    <div class="post-meta">
        <div class="flex gap-4">
            <div class="avatar">
                <div class="mask mask-squircle w-6 h-6">
                    <img src="<?= $post->author->avatarLink(25); ?>" alt="" />
                </div>
            </div>
            <div class="flex-1 opacity-50">
                <i class="fa-solid fa-reply"></i>
                <?php if ($post->author) : ?>
                    <a href="<?= $post->author->link() ?>">
                        <b><?= esc($post->author->username) ?></b>
                    </a>
                <?php endif ?>
                <?= \CodeIgniter\I18n\Time::parse($post->created_at)->humanize() ?>
            </div>
            <div class="flex-auto text-right">
                <?php if (auth()->user()?->can('posts.create')): ?>
                    <a class="btn btn-xs btn-primary"
                       hx-get="<?= route_to('post-create-reply', $post->thread_id, $post->reply_to ?? $post->id); ?>"
                       hx-target="#post-reply-<?= $post->reply_to ?? $post->id; ?>"
                       hx-swap="innerHTML show:top"
                    >
                        Reply
                    </a>
                <?php endif; ?>
                <?php if ($post->author_id === user_id() || auth()->user()?->can('posts.edit')): ?>
                    <a class="btn btn-xs btn-primary"
                       hx-get="<?= route_to('post-edit', $post->id); ?>"
                       hx-target="closest .post"
                       hx-swap="outerHTML show:top"
                    >
                        Edit
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Post Content -->
    <div class="post-content prose !max-w-full mt-6">
        <?= $post->render() ?>
    </div>
</div>
