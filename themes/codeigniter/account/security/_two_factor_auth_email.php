<div x-data="{ open: <?= ! empty($open) && $open ? 'true' : 'false' ?> }" id="two-factor-auth-email-form">
    <div class="flex justify-between">
        <div>
            <h3 class="font-bold">2FA <small>(Two-Factor Authentication)</small></h3>
            <p class="text-sm opacity-50">
                Enabled 2FA increases the security of your account.
            </p>
        </div>
        <div class="flex align-middle h-full">
            <?php if (setting('Auth.actions')['login'] === 'App\Libraries\Authentication\Actions\TwoFactorAuthEmail'): ?>
                <button class="btn btn-outline btn-<?= $user->two_factor_auth_email ? 'error': 'warning' ?>" x-on:click="open = !open" x-show="!open">
                    <?= $user->two_factor_auth_email ? 'Disable 2FA': 'Enable 2FA' ?>
                </button>
            <?php else: ?>
                <button class="btn btn-outline" disabled>
                    Enabled for everyone
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div x-show="open" class="mt-6">
        <p>With 2FA enabled, you will receive an email with <strong>special code</strong> after every successful login, which you will be required to enter to confirm the login to your account.</p>
        <br>
        <p>To confirm you want to <strong><?= $user->two_factor_auth_email ? 'disable': 'enable' ?></strong> 2FA for this account, enter your password.</p>

        <?php if(!empty($error)) : ?>
            <div class="alert alert-warning mt-4">
                <?= $error ?>
            </div>
        <?php endif ?>

        <?= form_open(url_to('account-two-factor-auth-email')) ?>
            <div class="flex flex-col mt-4">
                <?= view('components/password_input', ['name' => 'password', 'placeholder' => 'Current Password']) ?>
            </div>

            <div class="flex justify-end mt-4">
                <a class="btn btn-ghost mr-4" x-on:click="open = !open">Cancel</a>
                <button type="submit" class="btn btn-<?= $user->two_factor_auth_email ? 'error': 'warning' ?>"
                    hx-post="<?= url_to('account-two-factor-auth-email') ?>"
                    hx-confirm="Are you sure you want to <?= $user->two_factor_auth_email ? 'disable': 'enable' ?> 2FA?"
                    hx-target="#two-factor-auth-email-form"
                    hx-swap="outerHTML"
                >
                    Yes, <?= $user->two_factor_auth_email ? 'Disable': 'Enable' ?> 2FA
                </button>
            </div>
        <?= form_close() ?>
    </div>
</div>
