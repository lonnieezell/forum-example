<nav class="side-nav">
    <!-- Settings -->
    <div x-data="{ open: true }">
        <p class="side-nav-title" x-on:click="open = ! open">
            Settings
            <span>&#709;</span>
        </p>
        <menu x-show="open" x-transition>
            <li>
                <a href="<?= url_to('settings-users') ?>">
                    Users
                </a>
            </li>
            <li>
                <a href="<?= url_to('settings-trust') ?>">
                    Trust Levels
                </a>
            </li>
        </menu>
    </div>
</nav>
