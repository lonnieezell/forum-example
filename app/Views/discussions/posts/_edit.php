<?= form_open('', [
    'hx-confirm' => 'Are you sure you want to update a post?',
    'hx-put' => current_url(),
    'hx-target' => 'closest div',
    'hx-swap' => 'outerHTML show:top',
]); ?>
<div class="card bg-base-100 shadow-xl">
    <div class="card-body">
        <div class="card-title">
            Edit a post
        </div>
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text">Message</span>
            </label>
            <?= form_textarea('body', set_value('body', $post->body, false),  [
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
                <button class="btn w-1/2"
                        hx-confirm="unset"
                        hx-post="<?= route_to('post-preview'); ?>"
                        hx-target="#editor-preview"
                        hx-swap="innerHTML show:top"
                        data-loading-disable>
                    Preview
                </button>
                <button type="submit" class="btn btn-primary w-1/2" data-loading-disable>
                    Update
                </button>
            </div>
        </div>
    </div>
</div>
<?= form_close(); ?>

<div id="editor-preview"></div>
