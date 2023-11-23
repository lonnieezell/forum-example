<?php if (service('policy')->can('threads.moderate')): ?>
    <li>
        <a href="<?= route_to('moderate-threads'); ?>">
            Threads
            <span class="badge badge-sm">
                <?= $count['thread']; ?>
            </span>
        </a>
    </li>
<?php endif; ?>
<?php if (service('policy')->can('posts.moderate')): ?>
    <li>
        <a href="<?= route_to('moderate-posts'); ?>">
            Posts
            <span class="badge badge-sm">
                <?= $count['post']; ?>
            </span>
        </a>
    </li>
<?php endif; ?>
<li>
    <a href="<?= route_to('moderate-logs'); ?>">Logs</a>
</li>
