<?php if ($search->getResults()->isEmpty()): ?>

    <h1>No results, sorry.</h1>

<?php else: ?>

    <h1>Search Results (<?= $search->getResults()->count(); ?>)</h1>

    <div hx-boost="true">
        <?php foreach ($search->getResults()->items() as $key => $result): ?>

            <div class="card card-compact bg-base-100 shadow-xl my-6">
                <div class="card-body">
                    <p>
                        <strong><?= ++$key; ?>.</strong>
                        <a href="<?= url_to('page', $result->getFile()->urlPath()); ?>">
                            <?= $result->getFile()->getName(); ?>
                        </a>
                    </p>
                </div>
            </div>

        <?php endforeach; ?>
    </div>

<?php endif; ?>

