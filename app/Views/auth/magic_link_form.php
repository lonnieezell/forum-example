<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.useMagicLink') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

<div class="flex justify-center pt-12">
    <div class="card w-full md:w-96 bg-base-100 shadow-xl">
        <div class="card-body">
            <h5 class="card-title mb-5"><?= lang('Auth.useMagicLink') ?></h5>

            <p class="mb-4">Enter your email address and a login link will be emailed to you. No password required.</p>

                <?php if (session('error') !== null) : ?>
                    <div class="alert mb-4" role="alert"><?= session('error') ?></div>
                <?php endif ?>

            <form action="<?= url_to('magic-link') ?>" method="post">
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
                        value="<?= old('email') ?>"
                        required
                    >
                    <?= show_error('email') ?>
                </div>

                <div class="card-actions justify-center mt-12">
                    <button type="submit" class="btn btn-primary btn-block mb-4"><?= lang('Auth.send') ?></button>
                </div>

            </form>

            <p class="text-center">
                <a href="<?= url_to('login') ?>" class="link">
                    <?= lang('Auth.backToLogin') ?>
                </a>
            </p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
