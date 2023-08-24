<div class="discussion-list">
    <div class="mt-4 my-6 flex justify-between">
        <form hx-get="<?= current_url() ?>" hx-target="#discussion-list">
            <select name="type" class="select w-auto" onChange="this.form.submit()">
                <option value="recent-threads"
                    <?= $listType === 'recent-threads' ? 'selected' : '' ?>>
                    by Newest Threads
                </option>
                <option value="recent-posts"
                    <?= $listType === 'recent-posts' ? 'selected' : '' ?>>
                    by Newest Replies
                </option>
                <option value="unanswered"
                    <?= $listType === 'unanswered' ? 'selected' : '' ?>>
                    only Unanswered</option>
                <option value="my-threads"
                    <?= $listType === 'my-threads' ? 'selected' : '' ?>>
                    only My Threads
                </option>
            </select>
        </form>
        <?= $pager->links() ?>
    </div>

    <div class="mt-6 p-6 border rounded bg-base-200 border-base-300">
        <?php if (empty($threads)): ?>
            <p>Sorry, there are no discussion to display.</p>
        <?php else: ?>
            <?php foreach($threads as $thread) : ?>
                <?= view('discussions/_thread_summary', ['thread' => $thread]) ?>
            <?php endforeach ?>
        <?php endif ?>
    </div>

    <div class="mt-6 text-center">
        <?= $pager->links() ?>
    </div>
</div>
