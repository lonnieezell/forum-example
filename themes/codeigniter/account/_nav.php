<?php
    $pages = [
        [
            'title' => 'Posts',
            'url'   => route_to('account-posts'),
            'icon'  => view('icons/chat-bubbles'),
        ],
        [
            'title' => 'Discussions',
            'url'   => route_to('account-threads'),
            'icon'  => view('icons/chat-bubble'),
        ],
        [
            'title' => 'Notifications',
            'url'   => route_to('account-notifications'),
            'icon'  => view('icons/bell'),
        ],
        [
            'title' => 'Profile',
            'url'   => route_to('account-profile'),
            'icon'  => view('icons/gear'),
        ],
        [
            'title' => 'Security',
            'url'   => route_to('account-security'),
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
