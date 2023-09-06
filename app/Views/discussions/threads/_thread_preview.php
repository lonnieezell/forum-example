<div class="card bg-base-100 shadow-xl mt-6">
    <div class="card-body">
        <div class="card-title">
            Preview
        </div>

        <div class="thread mt-6 p-6 border rounded bg-base-200 border-base-300">
            <h3 class="text-xl leading-loose font-bold">
                <?= esc($thread->title) ?>
            </h3>
            <div class="thread-content prose">
                <?= $thread->render() ?>
            </div>
        </div>

    </div>
</div>
