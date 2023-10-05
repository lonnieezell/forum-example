<?php if (isset($pages) && count($pages)) :?>
    <div>
    <?php foreach ($pages as $page) : ?>
        <li>
            <a href="<?= $page['url'] ?>"
                class="<?= url_is($page['url']) ? 'active' : '' ?>"
            >
                <?= $page['icon'] ?>
                <span class="block text-centered sm:inline sm:text-left"><?= $page['title'] ?></span>
            </a>
        </li>
    <?php endforeach ?>
    </div>
<?php endif ?>
