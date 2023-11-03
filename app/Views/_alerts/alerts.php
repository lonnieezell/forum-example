<?php foreach ($alerts as $type => $messages): ?>
    <?php foreach ($messages as $message): ?>
        <div class="alert alert-<?= esc($type, 'attr'); ?>"
             x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, <?= $message['seconds'] * 1000 ?>)"
        >
            <span><?= esc($message['message']); ?></span>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>
