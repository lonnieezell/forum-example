<div class="flex justify-center p-6 bg-base-200 border border-l-0 border-r-0 border-base-300">
    <div class="container flex flex-col sm:flex-row gap-8 w-auto">
        <?= $user->renderAvatar(80) ?>

        <div class="flex-1">
            <h1 class="text-xl font-bold">
                <?= esc($user->username) ?>

                <?php if ($user->name): ?>
                    <span class="opacity-50 ml-4 font-normal"><?= esc($user->name) ?></span>
                <?php endif ?>
            </h1>

            <div>
                <div>
                    <?php if ($user->inGroup('admin', 'superadmin')): ?>
                        <span class="badge badge-primary">Administrator</span>
                    <?php endif ?>
                    <?php if ($user->inGroup('moderator')): ?>
                        <span class="badge badge-accent">Moderator</span>
                    <?php endif ?>
                </div>

                <ul class="text-sm">
                    <li><span class="opacity-50">Posts:</span>
                        <?= number_format($user->post_count + $user->thread_count) ?>
                    </li>
                    <li><span class="opacity-50">Email:</span>
                        <?php if($user->email): ?>
                            <a href="mailto:<?= esc($user->email, 'attr') ?>">
                                <?= esc($user->email) ?>
                            </a>
                        <?php endif ?>
                    </li>
                    <li><span class="opacity-50">Last Login:</span>
                        <?= $user->lastLogin()?->date?->humanize() ?? 'n/a' ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
