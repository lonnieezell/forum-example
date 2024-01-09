<?= form_open('', [
        'hx-post' => current_url(),
        'hx-target' => '#form-wrap',
        'hx-swap' => 'innerHTML',
    ]); ?>

    <fieldset>
        <legend>Registration</legend>

        <!-- Allow Registration -->
        <div class="columns-2">
            <div class="form-control">
                <label class="label cursor-pointer justify-normal" for="allow-registration">
                    <input
                        type="checkbox"
                        name="allowRegistration"
                        value="1"
                        id="allow-registration"
                        <?php if (setting('Auth.allowRegistration')) : ?> checked <?php endif ?>
                    >
                    <span class="label-text ml-2">Allow users to register themselves on the site</span>
                </label>
                <?= form_error($validator, 'allowRegistration') ?>
            </div>
            <p class="text-sm opacity-50 pt-2">If unchecked, an admin will need to create users.</p>
        </div>

        <!-- Email Verification -->
        <div class="columns-2">
            <div class="form-control">
                <label class="label cursor-pointer justify-normal" for="email-activation">
                    <input
                        type="checkbox"
                        name="emailActivation"
                        value="1"
                        id="email-activation"
                        <?php if (setting('Auth.actions')['register'] === 'CodeIgniter\Shield\Authentication\Actions\EmailActivator') : ?> checked <?php endif ?>
                    >
                    <span class="label-text ml-2">Force email verification after registration?</span>
                </label>
                <?= form_error($validator, 'emailActivation') ?>
            </div>
            <p class="text-sm opacity-50 pt-2">If checked, will send a code via email for them to confirm.</p>
        </div>
    </fieldset>

    <fieldset x-data="{remember: <?= setting('Auth.sessionConfig')['allowRemembering'] ? 1 : 0 ?>}">
        <legend>Login</legend>

        <!-- Allow Remmeber Me -->
        <div class="columns-2">
            <div class="form-control">
                <label class="label cursor-pointer justify-normal" for="allow-remember">
                    <input
                        type="checkbox"
                        name="allowRemember"
                        value="1"
                        id="allow-remember"
                        <?php if (setting('Auth.sessionConfig')['allowRemembering']) : ?> checked <?php endif ?>
                        x-on:click="remember = ! remember"
                    >
                    <span class="label-text ml-2">Allow users to be remembered.</span>
                </label>
                <?= form_error($validator, 'allowRemember') ?>
            </div>
            <p class="text-sm opacity-50 pt-2">This makes it so users do not have to login every visit.</p>
        </div>

        <!-- Remember Length -->
        <div class="" x-show="remember">
            <div class="form-control">
                <label for="rememberLength" class="label">Remember Users for how long?</label>
                <select name="rememberLength" class="select select-sm select-bordered w-full max-w-xs">
                    <option value="0">How long to remember...</option>
                    <?php if (isset($rememberOptions) && count($rememberOptions)) : ?>
                        <?php foreach ($rememberOptions as $title => $seconds) : ?>
                            <option value="<?= $seconds ?>"
                                <?php if (setting('Auth.sessionConfig')['rememberLength'] === (string) $seconds) : ?> selected <?php endif?>
                            >
                                <?= $title ?>
                            </option>
                        <?php endforeach ?>
                    <?php endif ?>
                </select>
            </div>
        </div>

        <!-- Force 2FA -->
        <div class="columns-2 mt-3">
            <div class="form-control">
                <label class="label cursor-pointer justify-normal" for="email-2fa">
                    <input
                        type="checkbox"
                        name="email2FA"
                        value="1"
                        id="email-2fa"
                        <?php if (setting('Auth.actions')['login'] === 'App\Libraries\Authentication\Actions\Email2FA') : ?> checked <?php endif ?>
                    >
                    <span class="label-text ml-2">Force 2FA check after login?</span>
                </label>
                <?= form_error($validator, 'email2FA') ?>
            </div>
            <p class="text-sm opacity-50 pt-2">If checked, will send a code via email for them to confirm.</p>
        </div>
    </fieldset>

    <fieldset>
        <legend>Passwords</legend>

        <!-- Minimum Password Length -->
        <div>
            <label class="label justify-normal" for="minimumPasswordLength">Minimum Password Length</label>
            <div class="columns-2">
                <div class="form-control">
                    <input
                        type="text"
                        name="minimumPasswordLength"
                        value="<?= setting('Auth.minimumPasswordLength') ?>"
                        id="minimumPasswordLength"
                        class="input input-sm input-bordered w-full max-w-24"
                    >
                    <?= form_error($validator, 'alloowRemember') ?>
                </div>
                <p class="text-sm opacity-50 pt-2">A minimum value of 8 is suggested. 10 is recommended.</p>
            </div>
        </div>

        <label for="passwordValidators" class="label mt-6">Password Validators</label>
        <p class="text-muted">These rules determine how secure a password must be.</p>

        <!-- Composition Validator -->
        <div class="columns-2 mt-3">
            <div class="form-control">
                <label class="label cursor-pointer justify-normal">
                    <input
                        type="checkbox"
                        name="validators[]"
                        value="CodeIgniter\Shield\Authentication\Passwords\CompositionValidator"
                        <?php if (is_array(setting('Auth.passwordValidators')) && in_array(
                            'CodeIgniter\Shield\Authentication\Passwords\CompositionValidator',
                            setting('Auth.passwordValidators'),
                            true
                        )) : ?>
                            checked
                        <?php endif ?>
                    >
                    <span class="label-text ml-2">Composition Validator</span>
                </label>
            </div>
            <p class="text-sm opacity-50 pt-2">Composition Validator primarily checks the password length requirements.</p>
        </div>

        <!-- Nothing Personal Validator -->
        <div class="columns-2">
            <div class="form-control">
                <label class="label cursor-pointer justify-normal">
                    <input
                        type="checkbox"
                        name="validators[]"
                        value="CodeIgniter\Shield\Authentication\Passwords\NothingPersonalValidator"
                        <?php if (is_array(setting('Auth.passwordValidators')) && in_array(
                            'CodeIgniter\Shield\Authentication\Passwords\NothingPersonalValidator',
                            setting('Auth.passwordValidators'),
                            true
                        )) : ?>
                            checked
                        <?php endif ?>
                    >
                    <span class="label-text ml-2">Nothing Personal Validator</span>
                </label>
            </div>
            <p class="text-sm opacity-50 pt-2">Nothing Personal Validator checks the password for similarities between the password, the email, username, and other personal fields to ensure they are not too similar and easy to guess.</p>
        </div>

        <!-- Dictionary Validator -->
        <div class="columns-2">
            <div class="form-control">
                <label class="label cursor-pointer justify-normal">
                    <input
                        type="checkbox"
                        name="validators[]"
                        value="CodeIgniter\Shield\Authentication\Passwords\DictionaryValidator"
                        <?php if (is_array(setting('Auth.passwordValidators')) && in_array(
                            'CodeIgniter\Shield\Authentication\Passwords\DictionaryValidator',
                            setting('Auth.passwordValidators'),
                            true
                        )) : ?>
                            checked
                        <?php endif ?>
                    >
                    <span class="label-text ml-2">Dictionary Validator</span>
                </label>
            </div>
            <p class="text-sm opacity-50 pt-2">Dictionary Validator checks the password against nearly 600,000 leaked passwords.</p>
        </div>

        <!-- Pwned Validator -->
        <div class="columns-2">
            <div class="form-control">
                <label class="label cursor-pointer justify-normal">
                    <input
                        type="checkbox"
                        name="validators[]"
                        value="CodeIgniter\Shield\Authentication\Passwords\PwnedValidator"
                        <?php if (is_array(setting('Auth.passwordValidators')) && in_array(
                            'CodeIgniter\Shield\Authentication\Passwords\PwnedValidator',
                            setting('Auth.passwordValidators'),
                            true
                        )) : ?>
                            checked
                        <?php endif ?>
                    >
                    <span class="label-text ml-2">Pwned Validator</span>
                </label>
            </div>
            <p class="text-sm opacity-50 pt-2">Pwned Validator uses a <a href="https://haveibeenpwned.com/Passwords" target="_blank">third-party site</a> to check the password against millions of leaked passwords.</p>
        </div>

        <p class="text-muted text-sm mt-6">Note: Unchecking these will reduce the password security requirements.</p>
    </fieldset>


    <div class="flex <?= (auth()->user()?->handed ?? 'right') === 'right' ? 'justify-end' : 'justify-start' ?>">
        <button type="submit" class="btn btn-primary" data-loading-disable>
            Save Settings
        </button>
    </div>

</form>
