<?= $this->extend('master')  ?>

<?= $this->section('header') ?>
    <?= $this->view('account/_header', ['user' => $user]) ?>
<?= $this->endSection() ?>


<?= $this->section('main')  ?>
    <?= $this->view('account/_post-head', [
        'title' => 'Security',
        'subTitle' => 'Manage your account security settings.'
    ]) ?>

    <?= $this->view('account/security/_logins', ['logins' => $logins, 'agent' => $agent]) ?>

    <!-- Warning Zone -->
    <div class="border border-warning rounded-lg p-4 mt-12">
        <?php if (policy('users.changePassword', $user)): ?>
            <?= $this->view('account/security/_change_password', ['validator' => $validator]) ?>
        <?php endif; ?>

        <!-- Logout of all devices -->
        <div class="flex justify-between border-b pb-4 border-slate-700 my-4">
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

        <?php if (policy('users.twoFactorAuthEmail', $user)): ?>
            <?= $this->view('account/security/_two_factor_auth_email', ['user' => $user, 'validator' => $validator]) ?>
        <?php endif; ?>
    </div>

    <?php if (policy('users.delete', $user)): ?>
        <!-- Danger Zone -->
        <div class="border border-error rounded-lg p-4 mt-12">
            <?= $this->view('account/security/_delete') ?>
        </div>
    <?php endif; ?>
<?= $this->endSection() ?>


<?= $this->section('sidebar')  ?>
    <?= $this->view('account/_sidebar') ?>
<?= $this->endSection() ?>
