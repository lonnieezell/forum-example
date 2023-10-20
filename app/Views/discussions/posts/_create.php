<div class="post-create">

    <?= form_open('', [
        'hx-confirm' => 'Are you sure you want to create a new post?',
        'hx-post' => current_url(),
        'hx-target' => empty($post_id) ? '#replies-content' : 'previous .post-replies',
        'hx-swap' => 'beforeend show:bottom',
    ]); ?>
    <div class="card rounded bg-base-100 shadow-xl">
        <div class="card-body p-6">
            <div class="card-title pb-3">
                Create a new <?= empty($post_id) ? 'post' : 'reply'; ?>
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
                        <?= form_hidden('thread_id', set_value('thread_id', $thread_id)); ?>
                        <?= form_hidden('reply_to', set_value('reply_to', $post_id)); ?>
                        <?= form_textarea('body', set_value('body', '', false),  [
                            'class' => 'input input-bordered', 'required' => '',
                            'id' => 'editor', 'data-type' => 'markdown',
                            'data-upload-enabled' => config('ImageUpload')->enabled,
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
            <div class="flex justify-center">
                <div class="btn-group btn-group-horizontal w-full">
                    <button class="btn w-1/2" type="button"
                            @click="$dispatch('removePostForm', { id: '<?= $post_id === '' ? 'post-reply' : 'post-reply-' . $post_id; ?>' })">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary w-1/2" data-loading-disable>
                        Publish
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?= form_close(); ?>
</div>
