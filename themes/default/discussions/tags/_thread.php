<div class="mt-1" hx-boost="true">
    <i class="fa-solid fa-tags opacity-40 m-1.5"></i>
    <?php foreach ($tags->items() as $tag): ?>
        <a class="btn btn-xs lowercase"
           href="<?= esc(route_to('tag', $tag->slug), 'attr'); ?>"
           hx-trigger="click consume"
        >
            <?= esc($tag->name); ?>
        </a>
    <?php endforeach; ?>
</div>
