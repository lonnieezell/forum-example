<div class="navbar bg-base-100 border-b border-base-300">
    <div class="container mx-auto">
        <div class="flex-1">
            <ul class="menu menu-horizontal px-1">
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
            </ul>
        </div>
        <div class="form-control">
            <input type="text" placeholder="Search" class="input input-bordered" />
        </div>
    </div>
</div>
