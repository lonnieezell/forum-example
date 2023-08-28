<div class="navbar bg-base-100" hx-boost="true">
    <div class="container mx-auto flex justify-spread">
        <!-- Mobile Menu -->
        <div class="navbar-end flex-1">
            <div class="dropdown">
                <label tabindex="0" class="btn btn-ghost lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
                </label>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li>
                        <a href="<?= site_url('discussions') ?>"
                            <?= url_is('discussions') ? 'class="active"' : '' ?>>
                            Discussions
                        </a>
                    </li>
                    <?php if(!auth()->loggedIn()) : ?>
                        <li><a href="<?= route_to('register') ?>">Sign Up</a></li>
                        <li><a href="<?= route_to('login') ?>">Sign In</a></li>
                    <?php endif ?>

                    <?php if(auth()->loggedIn()) : ?>
                        <li><a class="w-full">Account</a></li>
                        <li><a class="w-full" href="<?= route_to('logout') ?>">Logout</a></li>
                    <?php endif ?>

                </ul>
            </div>

            <!-- Site Name -->
            <a class="btn btn-ghost normal-case text-xl">
                <?= config('App')->siteName ?>
            </a>
        </div>

        <!-- Desktop Menu -->
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-4 flex">
                <li>
                    <a href="<?= site_url('/categories') ?>"
                        <?= url_is('categories') ? 'class="active"' : '' ?>>
                        Categories
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

                <div class="form-control">
                    <input type="text" placeholder="Search..." class="input input-bordered w-24 md:w-auto" />
                </div>

                <?php if(!auth()->loggedIn()) : ?>
                    <li><a href="<?= route_to('register') ?>">Sign Up</a></li>
                    <li><a href="<?= route_to('login') ?>">Sign In</a></li>
                <?php endif ?>

                <?php if(auth()->loggedIn()) : ?>
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                            <div class="w-10 rounded-full">
                                <?= auth()->user()->renderAvatar() ?>
                            </div>
                        </label>
                        <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                            <li><a class="w-full">Account</a></li>
                            <li><a class="w-full" href="<?= route_to('logout') ?>">Logout</a></li>
                        </ul>
                    </div>
                <?php endif ?>
            </ul>
        </div>
    </div>
</div>
