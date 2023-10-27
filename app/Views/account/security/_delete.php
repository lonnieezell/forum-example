<div x-data="{ open: <?= ! empty($open) && $open ? 'true' : 'false' ?> }" id="delete-form">
    <div class="flex justify-between">
        <div>
            <h3 class="font-bold">Delete Your Account</h3>
            <p class="text-sm opacity-50">
                Permanently delete your account. This action cannot be undone.
            </p>
        </div>
        <div class="flex align-middle h-full">
            <button class="btn btn-outline btn-error" x-on:click="open = !open" x-show="!open">
                Delete Your Account
            </button>
        </div>
    </div>

    <div x-show="open" class="mt-6">
        <p>To confirm you want to delete your account, enter your password.</p>

        <?php if(!empty($error)) : ?>
            <div class="alert alert-error mt-4">
                <?= $error ?>
            </div>
        <?php endif ?>

        <?= form_open(url_to('account-security-delete')) ?>
            <div class="flex flex-col mt-4">
                <?= view('components/password_input', ['name' => 'password', 'placeholder' => 'Current Password']) ?>
            </div>

            <div class="flex justify-end mt-4">
                <a class="btn btn-ghost mr-4" x-on:click="open = !open">Cancel</a>
                <button type="submit" class="btn btn-error"
                    hx-post="<?= url_to('account-security-delete') ?>"
                    hx-confirm="Are you sure you want to delete your account? This action cannot be undone."
                    hx-target="#delete-form"
                    hx-swap="outerHTML"
                >
                    Yes, Delete My Account
                </button>
            </div>
        <?= form_close() ?>
    </div>
</div>
