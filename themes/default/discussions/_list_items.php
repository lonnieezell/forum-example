<div class="flex flex-col sm:flex-row mb-3 px-2 sm:px-4 gap-4 sm:justify-stretch">
    <div class="flex-1 w-auto mx-auto">
        <?= form_open($searchUrl, [
            'method' => 'get', 'id' => 'discussion-search',
            'hx-boost' => 'true', 'hx-select' => '#discussion-list',
            'hx-target' => '#discussion-list', 'hx-swap' => 'outerHTML show:window:top'
        ]); ?>
            <?= form_dropdown(
                'search[type]',
                $table['dropdowns']['type'],
                set_value('search[type]', $table['search']['type'] ?? ''),
                [
                    'id' => 'search-type',
                    'class' => 'select w-auto border-neutral-200',
                    'hx-on:change' => 'htmx.trigger("#discussion-search", "submit")'
                ]);
            ?>
        <?= form_close(); ?>
    </div>
</div>

<?php if ($threads): ?>

    <?php foreach($threads as $thread) : ?>
        <?= $this->view('discussions/_list_item', ['thread' => $thread]) ?>
    <?php endforeach ?>

    <div class="mt-6 text-center"
         hx-boost="true"
         hx-select="#discussion-list"
         hx-target="#discussion-list"
         hx-swap="outerHTML show:window:top"
    >
        <?= $table['pager']->links(); ?>
    </div>
<?php else: ?>
    <div class="mt-6 p-6 border rounded bg-base-200 border-base-300">
        <p>Sorry, there are no discussion to display.</p>
    </div>
<?php endif ?>
