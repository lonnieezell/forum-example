<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

    <div class="flex justify-center pt-12">
        <div class="card w-full md:w-96 bg-base-100 shadow-xl">
            <div class="card-body">
                <h5 class="card-title mb-5 mx-auto"><?= lang('Auth.register') ?></h5>

                <?php if (session('error') !== null) : ?>
                    <div class="alert mb-4" role="alert"><?= session('error') ?></div>
                <?php endif ?>

                <form action="<?= url_to('register') ?>" method="post">
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

                    <!-- Username -->
                    <div class="mb-6">
                        <input
                            type="text"
                            class="input input-bordered w-full"
                            id="username-input"
                            name="username"
                            autocomplete="new-username"
                            placeholder="Username"
                            value="<?= old('username') ?>"
                            required
                        >
                        <?= show_error('username') ?>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <?= view('components/password_input', [
                            'name' => 'password',
                            'id' => 'password-input',
                            'autocomplete' => 'new-password',
                            'placeholder' => 'Password',
                        ]) ?>
                        <?= show_error('password') ?>
                    </div>

                    <!-- Password (Again) -->
                    <div class="mb-3">
                        <?= view('components/password_input', [
                            'name' => 'password_confirm',
                            'id' => 'password-confirm-input',
                            'autocomplete' => 'new-password-confirm',
                            'placeholder' => 'Confirm Password',
                        ]) ?>
                        <?= show_error('password_confirm') ?>
                    </div>

                    <div class="card-actions justify-center mt-12">
                        <button type="submit" class="btn btn-primary btn-block"><?= lang('Auth.register') ?></button>
                    </div>

                    <p class="text-center text-sm mt-8">
                        <?= lang('Auth.haveAccount') ?>
                        <a href="<?= url_to('login') ?>" class="link">
                            <?= lang('Auth.login') ?>
                        </a>
                    </p>

                </form>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>
