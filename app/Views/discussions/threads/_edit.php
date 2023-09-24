<?= form_open('', [
    'hx-confirm' => 'Are you sure you want to update the thread?',
    'hx-put' => current_url(),
    'hx-target' => '#thread',
    'hx-swap' => 'innerHTML show:top',
]); ?>
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="card-title">
                Edit the thread
            </div>
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">Title</span>
                </label>
                <?= form_input('title', set_value('title', $thread->title), [
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
                <?= form_input('tags', set_value('tags', isset($thread->tags) ? implode(',', array_column($thread->tags, 'name')) : ''), [
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
                <?= form_textarea('body', set_value('body', $thread->body, false),  [
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
                    <button class="btn w-1/3"
                            hx-confirm="unset"
                            hx-get="<?= route_to('thread-show', $thread->id); ?>"
                            hx-target="#thread"
                            data-loading-disable>
                        Cancel
                    </button>
                    <button class="btn btn-neutral w-1/3"
                        hx-confirm="unset"
                        hx-post="<?= route_to('thread-preview'); ?>"
                        hx-target="#editor-preview"
                        hx-swap="innerHTML show:top"
                        data-loading-disable>
                        Preview
                    </button>
                    <button type="submit" class="btn btn-primary w-1/3" data-loading-disable>
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>
<?= form_close(); ?>

<div id="editor-preview"></div>


