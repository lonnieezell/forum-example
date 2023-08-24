<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<nav aria-label="<?= lang('Pager.pageNavigation') ?>">
	<div class="btn-group">
		<?php if ($pager->hasPrevious()) : ?>
            <a href="<?= $pager->getFirst() ?>" class="btn" aria-label="<?= lang('Pager.first') ?>">
                <span aria-hidden="true"><?= lang('Pager.first') ?></span>
            </a>
            <a href="<?= $pager->getPrevious() ?>" class="btn" aria-label="<?= lang('Pager.previous') ?>">
                <span aria-hidden="true"><?= lang('Pager.previous') ?></span>
            </a>
		<?php endif ?>

		<?php foreach ($pager->links() as $link) : ?>
            <a href="<?= $link['uri'] ?>" class="btn <?= $link['active'] ? 'btn-active' : '' ?>">
                <?= $link['title'] ?>
            </a>
		<?php endforeach ?>

		<?php if ($pager->hasNext()) : ?>
            <a href="<?= $pager->getNext() ?>" class="btn" aria-label="<?= lang('Pager.next') ?>">
                <span aria-hidden="true"><?= lang('Pager.next') ?></span>
            </a>
            <a href="<?= $pager->getLast() ?>" class="btn" aria-label="<?= lang('Pager.last') ?>">
                <span aria-hidden="true"><?= lang('Pager.last') ?></span>
            </a>
		<?php endif ?>
	</div>
</nav>
