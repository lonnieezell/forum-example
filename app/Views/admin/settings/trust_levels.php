<?= $this->extend('master') ?>

<?= $this->section('main'); ?>
    <h1 class="text-lg font-semibold mb-4">Trust Levels Settings</h1>

    <div id="form-wrap">
        <form action="<?= current_url() ?>" method="post">
            <?= csrf_field() ?>

            <!-- Loop over each trust level as a field group -->
            <?php foreach ($trustLevels as $level => $trustLevel): ?>
                <fieldset>
                    <legend>Level <?= $level ?></legend>

                    <div class="flex flex-row gap-6">
                        <div class="w-1/2">
                        <?php if (!empty($trustRequirements[$level])) : ?>
                            <h3 class="font-semibold mb-2">Requirements:</h3>

                            <?php foreach($trustRequirements[$level] ?? [] as $requirement => $value): ?>
                                <div class="ml-4 mb-1">
                                    <label for="trust-<?= $level ?>-<?= $requirement ?>">
                                        <input type="number"
                                            class="border border-gray-300 rounded py-1 px-2 mr-2"
                                            id="trust-<?= $level ?>-<?= $requirement ?>"
                                            name="requirements[<?= $level ?>][<?= $requirement ?>]"
                                            value="<?= $value ?>"
                                        >
                                        <?= ucwords(str_replace('-', ' ', $requirement)) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="w-1/2">
                        <h3 class="font-semibold mb-2">Allowed Actions:</h3>

                        <?php foreach ($trustActions as $action => $label): ?>
                            <div class="ml-4">
                                <label for="trust-<?= $level ?>-<?= $action ?>">
                                    <input type="checkbox"
                                        class="mr-2"
                                        id="trust-<?= $level ?>-<?= $action ?>"
                                        name="trust[<?= $level ?>][<?= $action ?>]"
                                        value="1"
                                        <?= in_array($action, $trustAllowed[$level] ?? []) ? 'checked' : '' ?>
                                    >
                                    <?= $label ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
            <?php endforeach; ?>

            <div class="flex <?= (auth()->user()?->handed ?? 'right') === 'right' ? 'justify-end' : 'justify-start' ?>">
                <button type="submit" class="btn btn-primary" data-loading-disable>
                    Save Trust Levels
                </button>
            </div>
        </form>
    </div>

<?= $this->endSection(); ?>
