<?php
use Config\ImageUpload;
use Config\Security;
?>
<?= form_open('', [
    'hx-confirm' => 'Are you sure you want to update the thread?',
    'hx-put' => current_url(),
    'hx-target' => '#thread',
    'hx-swap' => 'innerHTML show:top',
]); ?>
    <div class="card rounded bg-base-100 shadow-xl">
        <div class="card-body p-6">
            <div class="card-title pb-3">
                Edit the thread
            </div>
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">Title</span>
                </label>
                <?= form_input('title', set_value('title', $thread->title, false), [
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
                <?= form_dropdown('category_id', $category_dropdown, set_value('forum_id', $thread->category_id), [
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
                <?= form_input('tags', set_value('tags', $thread->tags->column('name')->join(',')), [
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
                    <?= form_textarea('body', set_value('body', $thread->body, false),  [
                        'class' => 'input input-bordered', 'required' => '',
                        'id' => 'editor', 'data-type' => 'markdown',
                        'data-upload-enabled' => config(ImageUpload::class)->enabled,
                        'data-upload-size' => config(ImageUpload::class)->fileSize,
                        'data-upload-mime' => config(ImageUpload::class)->getMime(),
                        'data-upload-url' => config(ImageUpload::class)->uploadUrl,
                        'data-csrf-name' => config(Security::class)->tokenName,
                        'data-csrf-header' => config(Security::class)->headerName,
                    ]); ?>
                    <?php if ($validator->hasError('body')): ?>
                        <label class="label">
                            <span class="label-text-alt text-red-600"><?= $validator->getError('body'); ?></span>
                        </label>
                    <?php endif; ?>
                </div>
                <div id="editor-preview" x-show="tab === 'preview'"></div>
            </div>
            <div class="flex justify-center">
                <div class="btn-group btn-group-horizontal w-full">
                    <button class="btn w-1/2"
                            hx-confirm="unset"
                            hx-get="<?= route_to('thread-show', $thread->id); ?>"
                            hx-target="#thread"
                            data-loading-disable>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary w-1/2" data-loading-disable>
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>
<?= form_close(); ?>
