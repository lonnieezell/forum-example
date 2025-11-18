<div class="my-2" hx-boost="true">
    <i class="fa-solid fa-tags"></i>
    <?php foreach ($tags as $tag): ?>
        <a class="btn btn-xs lowercase"
           href="<?= $tag->link(); ?>">
            <?= esc($tag->name); ?>
        </a>
    <?php endforeach; ?>
</div>
