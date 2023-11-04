<?= form_open('', ['hx-post' => current_url()]); ?>
    <fieldset>
        <legend>Personal</legend>

        <!-- Username -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Username</span>
            </label>
            <?= form_input([
                'name' => 'username',
                'value' => $user->username,
                'class' => 'input input-bordered w-full',
                'disabled' => 'disabled',
            ]) ?>
        </div>

        <!-- Email -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Email Address</span>
            </label>
            <?= form_input([
                'name' => 'email',
                'value' => $user->email,
                'class' => 'input input-bordered w-full',
                'disabled' => 'disabled',
            ]) ?>
        </div>

        <!-- Name -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Name</span>
            </label>
            <?= form_input([
                'name' => 'name',
                'value' => $user->name,
                'class' => 'input input-bordered w-full',
            ]) ?>
            <?= form_error($validator, 'name') ?>
        </div>

        <!-- Handed -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Right/Left Handed</span>
            </label>
            <p class="w-full text-sm mb-2 opacity-60">We use this adjust some interface elements to be easier to use.</p>
            <?= form_dropdown([
                'name' => 'handed',
                'value' => $user->handed,
                'class' => 'select select-bordered w-full',
                'style' => 'max-width: 10rem',
                'options' => [
                    'right' => 'Right',
                    'left' => 'Left',
                ],
            ]) ?>
            <?= form_error($validator, 'handed') ?>
        </div>

        <!-- Country -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Country</span>
            </label>
            <?= form_dropdown([
                'name' => 'country',
                'value' => $user->country,
                'class' => 'select select-bordered w-full',
                'style' => 'width: auto;',
                'selected' => $user->country,
                'options' => App\Libraries\CountryHelper::options(),
            ]) ?>
            <?= form_error($validator, 'country') ?>
        </div>

    </fieldset>

    <fieldset>
        <legend>Profile</legend>

        <!-- Website URL -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Website URL</span>
            </label>
            <?= form_input([
                'type' => 'url',
                'name' => 'website',
                'value' => $user->website,
                'class' => 'input input-bordered w-full',
            ]) ?>
            <?= form_error($validator, 'website') ?>
        </div>

        <!-- Location -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Location</span>
            </label>
            <?= form_input([
                'name' => 'location',
                'value' => $user->location,
                'class' => 'input input-bordered w-full',
            ]) ?>
            <?= form_error($validator, 'location') ?>
        </div>

        <!-- Company -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Company</span>
            </label>
            <?= form_input([
                'name' => 'company',
                'value' => $user->company,
                'class' => 'input input-bordered w-full',
            ]) ?>
            <?= form_error($validator, 'company') ?>
        </div>

        <!-- Signature -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Signature</span>
            </label>
            <p class="w-full text-sm mb-2 opacity-60">This is displayed at the bottom of all discussions you start and any posted replies. You may use Markdown formatting. Max. 255 characters.</p>
            <?= form_textarea([
                'name' => 'signature',
                'value' => $user->signature,
                'class' => 'textarea textarea-bordered w-full',
                'rows' => 3,
                'maxlength' => 255,
            ]) ?>
            <?= form_error($validator, 'signature') ?>
        </div>
    </fieldset>

    <div class="mt-12 <?= (auth()->user()?->handed ?: 'right') === 'right' ? 'text-end' : 'text-start' ?>">
        <button type="submit" class="btn btn-primary">Save Profile Settings</button>
    </div>

<?= form_close() ?>
