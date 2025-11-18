<div class="admin-sidebar" hx-boost="true">
    <ul class="menu">
        <!-- Settings -->
        <li>
            <details open>
                <summary>Settings</summary>
                <ul>
                    <li>
                        <a href="<?= url_to('settings-users') ?>"
                           hx-get="<?= url_to('settings-users') ?>"
                           hx-target="#main"
                           hx-swap="innerHTML">
                            Users
                        </a>
                    </li>
                    <li>
                        <a href="<?= url_to('settings-trust') ?>"
                           hx-get="<?= url_to('settings-trust') ?>"
                           hx-target="#main"
                           hx-swap="innerHTML">
                            Trust Levels
                        </a>
                    </li>
                </ul>
            </details>
        </li>
    </ul>
</div>
