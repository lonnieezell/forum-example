<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <?= view('account/_header', ['user' => $user]) ?>
<?= $this->endSection() ?>


<?= $this->section('main')  ?>
    <?= view('account/_post-head', [
        'title' => 'Security',
        'subTitle' => 'Manage your account security settings.'
    ]) ?>

    <?= view('account/security/_logins', ['logins' => $logins, 'agent' => $agent]) ?>

    <!-- Warning Zone -->
    <div class="border border-warning rounded-lg p-4 mt-12">
        <!-- Change Password -->
        <div class="flex justify-between border-b pb-4 border-slate-700">
            <!-- Password reset button and description -->
            <div>
                <h3 class="font-bold">Reset Password</h3>
                <p class="text-sm opacity-50">
                    Reset your password to log out of all devices and force a password change.
                </p>
            </div>
            <div class="flex align-middle h-full">
                <a href="#" class="btn btn-outline btn-warning">Change Password</a>
            </div>
        </div>

        <!-- Logout of all devices -->
        <div class="flex justify-between border-b pb-4 border-slate-700 mt-4">
            <!-- Password reset button and description -->
            <div>
                <h3 class="font-bold">Log Out of All Devices</h3>
                <p class="text-sm opacity-50">
                    Log out of all devices, including this one.
                </p>
            </div>
            <div class="flex align-middle h-full">
                <button class="btn btn-outline btn-warning" <?= !$isRemembered ? 'disabled' : '' ?>
                    hx-post="<?= url_to('account-security-logout-all') ?>"
                    hx-confirm="Are you sure you want to log out of all devices?"
                >
                    Log Out All
                </button>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="border border-error rounded-lg p-4 mt-12">
        <?= view('account/security/_delete') ?>
    </div>
<?= $this->endSection() ?>


<?= $this->section('sidebar')  ?>
    <?= view('account/_sidebar') ?>
<?= $this->endSection() ?>
