<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.login') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

    <div class="flex justify-center pt-12">
        <div class="card w-full md:w-96 bg-base-100 shadow-xl">
            <div class="card-body">
                <h5 class="card-title mb-5 mx-auto"><?= lang('Auth.login') ?></h5>

                <?php if (session('error') !== null) : ?>
                    <div class="alert mb-4" role="alert"><?= session('error') ?></div>
                <?php endif ?>

                <form action="<?= url_to('login') ?>" method="post">
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

                    <!-- Password -->
                    <div class="mb-3">
                        <?= view('components/password_input', [
                            'name' => 'password',
                            'id' => 'password-input',
                        ]) ?>
                        <?= show_error('password') ?>
                    </div>

                    <!-- Remember me -->
                    <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                        <label class="label cursor-pointer justify-start">
                            <input
                                type="checkbox"
                                checked="checked"
                                class="checkbox checkbox-xs mr-2"
                                <?php if (old('remember')): ?> checked<?php endif ?>
                            />
                            Remember me
                        </label>
                    <?php endif; ?>

                    <div class="card-actions justify-center mt-12">
                        <button type="submit" class="btn btn-primary btn-block mb-6">
                            <?= lang('Auth.login') ?>
                        </button>

                        <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
                            <p class="text-center text-sm">
                                <?= lang('Auth.forgotPassword') ?>
                                <a href="<?= url_to('magic-link') ?>" class="link">
                                    <?= lang('Auth.useMagicLink') ?>
                                </a>
                            </p>
                        <?php endif ?>

                        <?php if (setting('Auth.allowRegistration')) : ?>
                            <p class="text-center text-sm">
                                <?= lang('Auth.needAccount') ?>
                                <a href="<?= url_to('register') ?>" class="link">
                                    <?= lang('Auth.register') ?>
                                </a>
                            </p>
                        <?php endif ?>
                    </div>

                </form>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>
