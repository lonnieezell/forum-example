<div class="navbar bg-primary" hx-boost="true">
    <div class="container mx-auto flex justify-spread align-middle">
        <!-- Mobile Menu -->
        <div class="navbar-end flex-1">
            <div class="dropdown">
                <label tabindex="0" class="btn btn-ghost lg:hidden text-primary-content">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
                </label>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-primary rounded-box w-52">
                    <li>
                        <a href="<?= site_url('discussions') ?>"
                            <?= url_is('discussions') ? 'class="active"' : '' ?>>
                            Discussions
                        </a>
                    </li>
                    <li>
                        <a href="<?= site_url('members') ?>"
                            <?= url_is('members') ? 'class="active"' : '' ?>>
                            Members
                        </a>
                    </li>
                    <li>
                        <a href="<?= url_to('pages') ?>"
                            <?= url_is('help') ? 'class="active"' : '' ?>>
                            Help
                        </a>
                    </li>
                    <?php if (! auth()->loggedIn()): ?>
                        <?php if (setting('Auth.allowRegistration')) : ?>
                            <li><a href="<?= route_to('register') ?>" hx-boost="false">Sign Up</a></li>
                        <?php endif ?>
                        <li><a href="<?= route_to('login') ?>" hx-boost="false">Sign In</a></li>
                    <?php endif ?>

                    <?php if (auth()->loggedIn()): ?>
                        <?php if (auth()->user()->inGroup('admin', 'superadmin')) : ?>
                            <li>
                                <a href="<?= url_to('admin-dashboard') ?>">
                                    Administer
                                </a>
                            </li>
                        <?php endif ?>
                        <?php if ($modThread = service('policy')->can('moderation.threads') || service('policy')->can('moderation.posts')): ?>
                            <li>
                                <a href="<?= $modThread ? url_to('moderate-threads') : url_to('moderate-posts') ?>"
                                    <?= url_is('moderation') ? 'class="active"' : '' ?>>
                                    Moderate
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="border-t border-base-content pt-2 mt-2">
                            <a class="w-full" href="<?= route_to('logout') ?>" hx-boost="false">
                                Logout
                            </a>
                        </li>
                    <?php endif ?>

                </ul>
            </div>

            <!-- Site Name -->
            <a class="btn btn-ghost normal-case text-xl text-primary-content">
                <?= config('App')->siteName ?>
            </a>
        </div>

        <!-- Desktop Menu -->
        <div class="navbar-center hidden lg:flex">
            <div class="flex-none">
                <ul class="menu menu-horizontal px-1 gap-4">
                    <li>
                        <a href="<?= site_url('discussions') ?>"
                            <?= url_is('discussions') ? 'class="active"' : '' ?>>
                            Discussions
                        </a>
                    </li>
                    <li>
                        <a href="<?= site_url('members') ?>"
                            <?= url_is('members') ? 'class="active"' : '' ?>>
                            Members
                        </a>
                    </li>

                    <li>
                        <a href="<?= url_to('pages') ?>"
                            <?= url_is('help') ? 'class="active"' : '' ?>>
                            Help
                        </a>
                    </li>
                    <!-- Auth -->
                    <?php if (! auth()->loggedIn()): ?>
                        <?php if (setting('Auth.allowRegistration')) : ?>
                            <li><a href="<?= route_to('register') ?>" hx-boost="false">Sign Up</a></li>
                        <?php endif ?>
                        <li><a href="<?= route_to('login') ?>" hx-boost="false">Sign In</a></li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
        <!-- Theme Toggle -->
        <label class="swap swap-rotate ml-2">
            <input type="checkbox" class="theme-controller hover:text-white" value="light"
                <?= get_cookie('theme') === 'light' ? 'checked' : '' ?> />

            <!-- sun icon -->
            <svg class="swap-on fill-current w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"/></svg>

            <!-- moon icon -->
            <svg class="swap-off fill-current w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/></svg>

        </label>
        <?php if (auth()->loggedIn()): ?>
            <div class="dropdown dropdown-end ml-4">
                <label tabindex="0" class="btn btn-ghost btn-circle">
                    <?= auth()->user()->renderAvatar(40) ?>
                </label>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 bg-primary shadow rounded-box rounded-lg w-36">
                    <?php if (auth()->user()->inGroup('admin', 'superadmin')) : ?>
                        <li>
                            <a href="<?= url_to('admin-dashboard') ?>">
                                Administer
                            </a>
                        </li>
                    <?php endif ?>
                    <?php if ($modThread = service('policy')->can('moderation.threads') || service('policy')->can('moderation.posts')): ?>
                        <li>
                            <a href="<?= $modThread ? url_to('moderate-threads') : url_to('moderate-posts') ?>">
                                Moderate
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="<?= url_to('account') ?>">
                            Account
                        </a>
                    </li>
                    <li class="separator"></li>
                    <li>
                        <a href="<?= url_to('logout') ?>" hx-boost="false">
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        <?php endif ?>
    </div>
</div>
