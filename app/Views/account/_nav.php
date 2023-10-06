<?php
    $pages = [
        [
            'title' => 'Posts',
            'url'   => route_to('account-posts'),
            'icon'  => view('icons/chat-bubbles'),
        ],
        [
            'title' => 'Discussions',
            'url'   => '',
            'icon'  => view('icons/chat-bubble'),
        ],
        [
            'title' => 'Notifications',
            'url'   => '',
            'icon'  => view('icons/bell'),
        ],
        [
            'title' => 'Settings',
            'url'   => '',
            'icon'  => view('icons/gear'),
        ],
        [
            'title' => 'Security',
            'url'   => '',
            'icon'  => view('icons/shield'),
        ],
    ];
?>

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
