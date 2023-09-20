<div class="post-create">

    <?= form_open('', [
        'hx-confirm' => 'Are you sure you want to create a new post?',
        'hx-post' => current_url(),
        'hx-target' => empty($post_id) ? '#replies-content' : 'previous .post-replies',
        'hx-swap' => 'beforeend show:bottom',
    ]); ?>
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="card-title">
                Create a new <?= empty($post_id) ? 'post' : 'reply'; ?>
            </div>
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text">Message</span>
                </label>
                <?= form_hidden('thread_id', set_value('thread_id', $thread_id)); ?>
                <?= form_hidden('reply_to', set_value('reply_to', $post_id)); ?>
                <?= form_textarea('body', set_value('body', '', false),  [
                    'class' => 'input input-bordered', 'required' => '',
                    'id' => 'editor', 'data-type' => 'markdown'
                ]); ?>
                <?php if ($validator->hasError('body')): ?>
                    <label class="label">
                        <span class="label-text-alt text-red-600"><?= $validator->getError('body'); ?></span>
                    </label>
                <?php endif; ?>
            </div>
            <div class="flex justify-center">
                <div class="btn-group btn-group-horizontal w-full">
                    <button class="btn btn-neutral w-1/3" type="button"
                            @click="$dispatch('removePostForm', { id: '<?= $post_id === '' ? 'post-reply' : 'post-reply-' . $post_id; ?>' })">
                        Cancel
                    </button>
                    <button class="btn w-1/3"
                            hx-confirm="unset"
                            hx-post="<?= route_to('post-preview'); ?>"
                            hx-target="#editor-preview"
                            hx-swap="innerHTML show:top"
                            data-loading-disable>
                        Preview
                    </button>
                    <button type="submit" class="btn btn-primary w-1/3" data-loading-disable>
                        Publish
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?= form_close(); ?>

    <div id="editor-preview"></div>

</div>
