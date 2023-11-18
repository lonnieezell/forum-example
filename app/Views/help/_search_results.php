<?php if ($search->getResults()->isEmpty()): ?>

    <h3>No results, sorry.</h3>

<?php else: ?>

    <h3>Search Results (<?= $search->getResults()->count(); ?>)</h3>

    <div hx-boost="true">
        <?php foreach ($search->getResults()->items() as $key => $result): ?>

            <div class="card card-compact bg-base-100 shadow-xl my-6">
                <div class="card-body">
                    <p>
                        <strong><?= ++$key; ?>.</strong>
                        <a href="<?= url_to('page', $result->getFile()->getPath()); ?>">
                            <?= $result->getFile()->getName(); ?>
                        </a>
                    </p>
                </div>
            </div>

        <?php endforeach; ?>
    </div>

<?php endif; ?>

