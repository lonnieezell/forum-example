
    <a class="action-btn"
        id="reaction-<?= $record->id ?>"
        title="Like this content"
        hx-get="<?= route_to('react-to', $record->id, $record instanceof \App\Entities\Thread ? 'thread' : 'post', \App\Models\ReactionModel::REACTION_LIKE); ?>"
        hx-target="#reaction-<?= $record->id ?>"
        hx-swap="outerHTML"
        hx-trigger="click throttle:1s"
    >
        <?php if ($record->reaction_count > 0) : ?>
            <span><?= number_format((int)$record->reaction_count) ?></span>
        <?php endif ?>
        <?php if ($record->has_reacted > 0) : ?>
            <?= $this->view('icons/heart_solid') ?>
        <?php else : ?>
            <?= $this->view('icons/heart') ?>
        <?php endif ?>
    </a>
