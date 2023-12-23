<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.email2FATitle') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

<div class="flex justify-center pt-12">
    <div class="card w-full md:w-96 bg-base-100 shadow-xl">
        <div class="card-body">
            <h5 class="card-title mb-5"><?= lang('Auth.email2FATitle') ?></h5>

            <p><?= lang('Auth.confirmEmailAddress') ?></p>

            <?php if (session('error')) : ?>
                <div class="alert mb-4"><?= session('error') ?></div>
            <?php endif ?>

            <form action="<?= url_to('auth-action-handle') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Email -->
                <div class="mb-3">
                    <input
                        type="email"
                        class="input input-bordered w-full"
                        id="email-input"
                        name="email"
                        inputmode="email"
                        autocomplete="email"
                        placeholder="Email"
                        value="<?= old('email', $user->email) ?>"
                        required
                    >
                    <?= show_error('email') ?>
                </div>

                <div class="card-actions justify-center mt-12">
                    <button type="submit" class="btn btn-primary btn-block"><?= lang('Auth.send') ?></button>
                </div>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
