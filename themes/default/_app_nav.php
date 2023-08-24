<div class="navbar bg-base-300">
    <div class="container flex mx-auto">
        <div class="navbar-start flex-1">
            <div class="dropdown">
                <label tabindex="0" class="btn btn-ghost lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
                </label>
                <!-- Mobile Menu -->
                <ul tabindex="0" class="menu menu-compact dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a>Item 1111</a></li>
                    <li tabindex="0">
                    <a class="justify-between">
                        Parent
                        <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/></svg>
                    </a>
                    <ul class="p-2">
                        <li><a>Submenu 1</a></li>
                        <li><a>Submenu 2</a></li>
                    </ul>
                    </li>
                    <li><a>Item 3</a></li>
                </ul>
            </div>
            <a href="<?= site_url() ?>" class="btn btn-ghost normal-case text-xl">
                <?= config('App')->siteName ?>
            </a>
        </div>
        <!-- Desktop Menu -->
        <div class=" flex-0 navbar-center invisible md:visible text-end">
            <ul class="menu menu-horizontal px-1">
                <li><a href="<?= site_url('login') ?>">Sign In</a></li>
            </ul>
            <!-- <div class="flex-none gap-2">
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn">
                        Account
                    </label>
                    <ul tabindex="0" class="menu menu-compact dropdown-content">
                        <li>
                            <a class="justify-between">
                                Profile
                            </a>
                        </li>
                        <li><a>Settings</a></li>
                        <li><a>Logout</a></li>
                    </ul>
                </div>
            </div> -->
        </div>
    </div>
</div>
