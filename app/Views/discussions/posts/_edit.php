<div class="post-edit">
    <?= form_open('', [
        'hx-confirm' => 'Are you sure you want to update a post?',
        'hx-put' => current_url(),
        'hx-target' => 'closest div',
        'hx-swap' => 'outerHTML show:top',
    ]); ?>
    <div class="card rounded bg-base-100 shadow-xl">
        <div class="card-body p-6">
            <div class="card-title pb-3">
                Edit a post
            </div>
            <div x-data="{ tab: 'message' }">
                <div class="tabs mb-1">
                    <a class="tab tab-lifted" :class="{ 'tab-active': tab === 'message' }" @click.prevent="tab = 'message'">Message</a>
                    <button class="tab tab-lifted"
                            :class="{ 'tab-active': tab === 'preview' }"
                            x-on:preview-show="tab = 'preview'"
                            hx-confirm="unset"
                            hx-post="<?= route_to('post-preview'); ?>"
                            hx-target="#editor-preview"
                            hx-swap="innerHTML"
                            data-loading-disable
                    >
                        Preview
                    </button>
                    <div class="tab tab-lifted flex-1 cursor-default"></div>
                </div>
                <div class="form-control w-full" x-show="tab === 'message'">
                    <?= form_textarea('body', set_value('body', $post->body, false),  [
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
                <div id="editor-preview" x-show="tab === 'preview'"></div>
            </div>
            <div class="flex justify-center">
                <div class="btn-group btn-group-horizontal w-full">
                    <button class="btn w-1/2"
                            hx-confirm="unset"
                            hx-get="<?= route_to('post-show', $post->id); ?>"
                            hx-target="closest .post-edit"
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
</div>
