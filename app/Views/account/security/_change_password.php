<div x-data="{ open: <?= ! empty($open) && $open ? 'true' : 'false' ?> }" id="password-form" class="border-b border-slate-700">
    <div class="flex justify-between pb-4">
        <div>
            <h3 class="font-bold">Reset Password</h3>
            <p class="text-sm opacity-50">
                Reset your password to log out of all devices and force a password change.
            </p>
        </div>
        <div class="flex align-middle h-full">
            <button class="btn btn-outline btn-warning" x-on:click="open = !open" x-show="!open">
                Change Password
            </button>
        </div>
    </div>

    <div x-show="open" class="mt-6 mb-6">
        <p>To update your password, enter your current password below. Then choose a new one.</p>
        <p class="text-sm opacity-50">Minimum length: <?= setting('Auth.minimumPasswordLength') ?> characters. Longer is better. Consider using a phrase.</p>

        <?php if(!empty($message)) : ?>
            <div class="alert alert-success mt-4">
                <?= $message ?>
            </div>
        <?php endif ?>

        <?php if(!empty($error)) : ?>
            <div class="alert alert-error mt-4">
                <?= $error ?>
            </div>
        <?php endif ?>

        <?= form_open(url_to('account-change-password')) ?>
            <div class="flex flex-col mt-4">
                <?= view('components/password_input', ['name' => 'current_password', 'placeholder' => 'Current Password']) ?>
                <?= $validator->showError('current_password') ?>
            </div>

            <div class="flex flex-col mt-4">
                <?= view('components/password_input', ['name' => 'password', 'placeholder' => 'New Password']) ?>
                <?= $validator->showError('password') ?>
            </div>

            <div class="flex flex-col mt-4">
                <?= view('components/password_input', ['name' => 'confirm_password', 'placeholder' => 'New Password (again)']) ?>
                <?= $validator->showError('confirm_password') ?>
            </div>

            <div class="flex justify-end mt-4">
                <a class="btn btn-ghost mr-4" x-on:click="open = !open">Cancel</a>
                <button type="submit" class="btn btn-warning"
                    hx-post="<?= url_to('account-change-password') ?>"
                    hx-target="#password-form"
                    hx-swap="outerHTML"
                >
                    Update Password
                </button>
            </div>
        <?= form_close() ?>
    </div>
</div>
