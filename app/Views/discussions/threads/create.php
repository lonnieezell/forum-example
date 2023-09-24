<?= $this->extend('master')  ?>

<?= $this->section('main')  ?>
    <div class="thread-create">

        <?= form_open('', ['hx-boost' => 'true', 'hx-confirm' => 'Are you sure you want to create a new thread?']); ?>
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="card-title">
                        Create a new thread
                    </div>
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">Title</span>
                        </label>
                        <?= form_input('title', set_value('title'), [
                            'class' => 'input input-bordered', 'placeholder' => 'Type here...',
                            'required' => ''
                        ]); ?>
                        <?php if ($validator->hasError('title')): ?>
                            <label class="label">
                                <span class="label-text-alt text-red-600"><?= $validator->getError('title'); ?></span>
                            </label>
                        <?php endif; ?>
                    </div>
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">Category</span>
                        </label>
                        <?= form_dropdown('category_id', $categoryDropdown, set_value('category_id', ''), [
                            'class' => 'select select-bordered', 'required' => ''
                        ]); ?>
                        <?php if ($validator->hasError('category_id')): ?>
                            <label class="label">
                                <span class="label-text-alt text-red-600"><?= $validator->getError('category_id'); ?></span>
                            </label>
                        <?php endif; ?>
                    </div>
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">Tags (optional)</span>
                        </label>
                        <?= form_input('tags', set_value('tags'), [
                            'class' => 'input input-bordered input-tags', 'id' => 'tags'
                        ]); ?>
                        <?php if ($validator->hasError('tags')): ?>
                            <label class="label">
                                <span class="label-text-alt text-red-600"><?= $validator->getError('tags'); ?></span>
                            </label>
                        <?php endif; ?>
                    </div>
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">Message</span>
                        </label>
                        <?= form_textarea('body', set_value('body', '', false),  [
                            'class' => 'input input-bordered', 'required' => '',
                            'id' => 'editor', 'data-type' => 'markdown',
                            'data-upload-enabled' => config('ImageUpload')->enabled,
                            'data-upload-size' => config('ImageUpload')->fileSize,
                            'data-upload-mime' => config('ImageUpload')->getMime(),
                            'data-upload-url' => config('ImageUpload')->uploadUrl,
                        ]); ?>
                        <?php if ($validator->hasError('body')): ?>
                            <label class="label">
                                <span class="label-text-alt text-red-600"><?= $validator->getError('body'); ?></span>
                            </label>
                        <?php endif; ?>
                    </div>
                    <div class="flex justify-center">
                        <div class="btn-group btn-group-horizontal w-full">
                            <button class="btn w-1/2"
                                hx-confirm="unset"
                                hx-post="<?= route_to('thread-preview'); ?>"
                                hx-target="#editor-preview"
                                hx-swap="innerHTML show:top"
                                data-loading-disable>
                                Preview
                            </button>
                            <button type="submit" class="btn btn-primary w-1/2" data-loading-disable>
                                Publish
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?= form_close(); ?>

        <div id="editor-preview"></div>

    </div>

<?= $this->endSection() ?>

<?= $this->section('sidebar')  ?>

    <h2>Sidebar</h2>

<?= $this->endSection() ?>
