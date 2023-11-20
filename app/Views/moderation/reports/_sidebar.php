<ul class="menu bg-base-200 w-56 rounded-box" hx-boost="true">
    <li>
        <h2 class="menu-title">Moderation queue</h2>
        <ul>
            <?php if (service('policy')->can('threads.moderate')): ?>
                <li><a href="<?= route_to('moderate-threads'); ?>">Threads</a></li>
            <?php endif; ?>
            <?php if (service('policy')->can('posts.moderate')): ?>
                <li><a href="<?= route_to('moderate-posts'); ?>">Posts</a></li>
            <?php endif; ?>
            <li><a href="<?= route_to('moderate-logs'); ?>">Logs</a></li>
        </ul>
    </li>
</ul>
