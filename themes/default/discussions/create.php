<?= $this->extend('discussion_layout')  ?>

<?= $this->section('main')  ?>
    <div class="thread-create">

        <?= form_open('', ['hx-boost' => 'true', 'hx-confirm' => 'Are you sure you want to create a new thread?']); ?>
            <div class="">
                <div class="card-body p-6">
                    <div class="card-title pb-3">
                        Start a new Discussion
                    </div>
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">Title</span>
                        </label>
                        <?= form_input('title', set_value('title', '', false), [
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
                    <div x-data="{ tab: 'message' }">
                        <div class="tabs mb-1">
                            <a class="tab tab-lifted" :class="{ 'tab-active': tab === 'message' }" @click.prevent="tab = 'message'">Message</a>
                            <button class="tab tab-lifted"
                                    :class="{ 'tab-active': tab === 'preview' }"
                                    x-on:preview-show="tab = 'preview'"
                                    hx-confirm="unset"
                                    hx-post="<?= route_to('thread-preview'); ?>"
                                    hx-target="#editor-preview"
                                    hx-swap="innerHTML"
                                    data-loading-disable
                            >
                                Preview
                            </button>
                            <div class="tab tab-lifted flex-1 cursor-default"></div>
                        </div>
                        <div class="form-control w-full" x-show="tab === 'message'">
                            <?= form_textarea('body', set_value('body', '', false), [
                                'class' => 'input input-bordered', 'required' => '',
                                'id' => 'editor', 'data-type' => 'markdown',
                                'data-upload-enabled' => (int)policy('threads.uploadImage'),
                                'data-upload-size' => config('ImageUpload')->fileSize,
                                'data-upload-mime' => config('ImageUpload')->getMime(),
                                'data-upload-url' => config('ImageUpload')->uploadUrl,
                                'data-csrf-name' => config('Security')->tokenName,
                                'data-csrf-header' => config('Security')->headerName,
                            ]); ?>
                            <?php if ($validator->hasError('body')): ?>
                                <label class="label">
                                    <span class="label-text-alt text-red-600"><?= $validator->getError('body'); ?></span>
                                </label>
                            <?php endif; ?>
                        </div>
                        <div id="editor-preview" x-show="tab === 'preview'"></div>
                    </div>
                    <div class="mt-4">
                        <div class="btn-group btn-group-horizontal w-full flex justify-end <?= (auth()->user()?->handed ?? 'right') === 'left' ? 'sm:justify-start' : '' ?>">
                            <button type="submit" class="btn btn-primary btn-block sm:btn-wide" data-loading-disable>
                                Publish
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?= form_close(); ?>
    </div>

<?= $this->endSection() ?>
