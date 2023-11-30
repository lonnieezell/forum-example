<?= form_open('', ['hx-post' => current_url()]); ?>
    <h6 class="border-solid border-b-2 mt-6">Threads</h6>
    <div class="form-control">
        <label class="label cursor-pointer flex">
            <input type="hidden" name="email_thread" value="0">
            <input type="checkbox" name="email_thread" value="1" <?= set_checkbox('email_thread', '1', $notification->email_thread) ?>
                   class="toggle toggle-primary">
            <span class="label-text ml-2 flex-auto">Send a notification for every new post in the thread I created</span>
        </label>
        <?php if ($validator->hasError('email_thread')): ?>
            <label class="label">
                <span class="label-text-alt text-red-600"><?= $validator->getError('email_thread'); ?></span>
            </label>
        <?php endif; ?>
    </div>

    <h6 class="border-solid border-b-2 mt-6">Posts</h6>
    <div x-data="{ checkboxEmailPost: <?= $notification->email_post ? 'true' : 'false' ?>, checkboxEmailPostReply: <?= $notification->email_post_reply ? 'true' : 'false' ?> }">
        <div class="form-control">
            <label class="label cursor-pointer flex">
                <input type="hidden" name="email_post" value="0">
                <input type="checkbox" name="email_post" value="1" <?= set_checkbox('email_post', '1', $notification->email_post) ?>
                       class="toggle toggle-primary"
                       x-model="checkboxEmailPost" @click="checkboxEmailPostReply = false">
                <span class="label-text ml-2 flex-auto">Send a notification for every new post in a thread in which I participated</span>
            </label>
            <?php if ($validator->hasError('email_post')): ?>
                <label class="label">
                    <span class="label-text-alt text-red-600"><?= $validator->getError('email_post'); ?></span>
                </label>
            <?php endif; ?>
        </div>
        <div class="form-control">
            <label class="label cursor-pointer flex">
                <input type="hidden" name="email_post_reply" value="0">
                <input type="checkbox" name="email_post_reply" value="1" <?= set_checkbox('email_post_reply', '1', $notification->email_post_reply) ?>
                       class="toggle toggle-primary"
                       x-model="checkboxEmailPostReply" @click="checkboxEmailPost = false">
                <span class="label-text ml-2 flex-auto">Send a notification only for posts that are replies to my post</span>
            </label>
            <?php if ($validator->hasError('email_post_reply')): ?>
                <label class="label">
                    <span class="label-text-alt text-red-600"><?= $validator->getError('email_post_reply'); ?></span>
                </label>
            <?php endif; ?>
        </div>
    </div>

    <?php if (service('policy')->can('moderation.logs')): ?>
        <h6 class="border-solid border-b-2 mt-6">Moderation</h6>
        <div class="form-control">
            <label class="label cursor-pointer flex">
                <input type="hidden" name="moderation_daily_summary" value="0">
                <input type="checkbox" name="moderation_daily_summary" value="1" <?= set_checkbox('moderation_daily_summary', '1', $notification->moderation_daily_summary ?? false) ?>
                       class="toggle toggle-primary">
                <span class="label-text ml-2 flex-auto">Send a daily summary with a moderation stats</span>
            </label>
            <?php if ($validator->hasError('moderation_daily_summary')): ?>
                <label class="label">
                    <span class="label-text-alt text-red-600"><?= $validator->getError('moderation_daily_summary'); ?></span>
                </label>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="flex justify-center mt-6">
        <div class="btn-group btn-group-horizontal w-full">
            <button type="submit" class="btn btn-primary w-full" data-loading-disable>
                Save
            </button>
        </div>
    </div>
<?= form_close(); ?>
