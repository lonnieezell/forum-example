<div class="navbar bg-primary shadow-lg" hx-boost="true">
    <div class="container mx-auto flex justify-spread align-middle">
        <!-- Mobile Menu -->
        <div class="navbar-end flex-1">
            <div class="dropdown">
                <label tabindex="0" class="btn btn-ghost lg:hidden text-primary-content">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
                </label>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-200 rounded-box w-52">
                    <li>
                        <a href="#" data-theme-toggle>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            </svg>

                            <span class="block text-centered sm:inline sm:text-left">Dark Theme</span>
                        </a>
                    </li>
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
                    <?php if ($modThread = service('policy')->can('moderation.threads') || service('policy')->can('moderation.posts')): ?>
                        <li>
                            <a href="<?= $modThread ? url_to('moderate-threads') : url_to('moderate-posts') ?>"
                                <?= url_is('moderation') ? 'class="active"' : '' ?>>
                                Moderate
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (! auth()->loggedIn()): ?>
                        <li><a href="<?= route_to('register') ?>" hx-boost="false">Sign Up</a></li>
                        <li><a href="<?= route_to('login') ?>" hx-boost="false">Sign In</a></li>
                    <?php endif ?>

                    <?php if (auth()->loggedIn()): ?>
                        <li>
                            <a class="w-full" href="<?= site_url('account') ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Account
                            </a>
                            <ul class="menu">
                                <?= view('account/_nav') ?>
                            </ul>
                        </li>
                        <li>
                            <a class="w-full" href="<?= route_to('logout') ?>" hx-boost="false">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
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
                    <?php if ($modThread = service('policy')->can('moderation.threads') || service('policy')->can('moderation.posts')): ?>
                        <li>
                            <a href="<?= $modThread ? url_to('moderate-threads') : url_to('moderate-posts') ?>"
                                <?= url_is('moderation') ? 'class="active"' : '' ?>>
                                Moderate
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="flex-none px-4">
                <div class="form-control">
                    <input type="text" placeholder="Search..." class="input input-bordered w-24 md:w-auto" />
                </div>
            </div>
            <div class="flex-none">
                <ul class="menu menu-horizontal px-1 gap-4">
                    <?php if (! auth()->loggedIn()): ?>
                        <li><a href="<?= route_to('register') ?>" hx-boost="false">Sign Up</a></li>
                        <li><a href="<?= route_to('login') ?>" hx-boost="false">Sign In</a></li>
                    <?php endif ?>
                </ul>
            </div>
            <?php if (auth()->loggedIn()): ?>
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                            <?= auth()->user()->renderAvatar(40) ?>
                    </label>
                    <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-200 rounded-box w-52">
                        <li>
                            <a href="#" data-theme-toggle>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>

                                <span class="block text-centered sm:inline sm:text-left">Dark Theme</span>
                            </a>
                        </li>
                        <li>
                            <a class="w-full" href="<?= site_url('account') ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Account
                            </a>
                        </li>
                        <li>
                            <a class="w-full" href="<?= route_to('logout') ?>" hx-boost="false">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
