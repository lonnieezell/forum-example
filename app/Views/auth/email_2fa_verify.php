<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.email2FATitle') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

<div class="flex justify-center pt-12">
    <div class="card w-full md:w-96 bg-base-100 shadow-xl">
        <div class="card-body">
            <h5 class="card-title mb-5"><?= lang('Auth.emailEnterCode') ?></h5>

            <p><?= lang('Auth.emailConfirmCode') ?></p>

            <?php if (session('error') !== null) : ?>
                <div class="alert mb-4"><?= session('error') ?></div>
            <?php endif ?>

            <form action="<?= url_to('auth-action-verify') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Code -->
                <div class="mb-2">
                    <input
                        type="number"
                        class="input input-bordered"
                        name="token"
                        placeholder="000000"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        autocomplete="one-time-code"
                        required
                    >
                    <?= show_error('token') ?>
                </div>

                <div class="card-actions justify-center mt-12">
                    <button type="submit" class="btn btn-primary btn-block"><?= lang('Auth.confirm') ?></button>
                </div>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
