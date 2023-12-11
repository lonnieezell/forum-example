<?php if (auth()->user()?->can('threads.create')): ?>
    <div class="w-full" hx-boost="true">
        <a class="btn btn-primary mt-4 mb-8 w-full" href="<?= url_to('thread-create'); ?>">
            Start a Discussion
        </a>
    </div>
<?php endif; ?>

<?php if (auth()->loggedIn()): ?>
    <div id="mute-thread-cell"></div>
<?php endif; ?>

<?= view_cell('CategoryListCell', ['activeCategory' => $activeCategory ?? null]) ?>
