<?php if (service('policy')->can('moderation.threads')): ?>
    <li>
        <a href="<?= route_to('moderate-threads'); ?>">
            Threads
            <span class="badge badge-sm">
                <?= $count['thread'] ?? 0; ?>
            </span>
        </a>
    </li>
<?php endif; ?>
<?php if (service('policy')->can('moderation.posts')): ?>
    <li>
        <a href="<?= route_to('moderate-posts'); ?>">
            Posts
            <span class="badge badge-sm">
                <?= $count['post'] ?? 0; ?>
            </span>
        </a>
    </li>
<?php endif; ?>
<?php if (service('policy')->can('moderation.logs')): ?>
    <li>
        <a href="<?= route_to('moderate-logs'); ?>">Logs</a>
    </li>
<?php endif; ?>
