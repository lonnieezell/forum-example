<?php foreach ($alerts as $type => $messages): ?>
    <?php foreach ($messages as $message): ?>
        <div class="alert-component" x-data="alerts(<?= $message['seconds']; ?>)">
            <progress class="progress progress-<?= esc($type, 'attr'); ?>"
                      x-model="progress" max="100"
            ></progress>
            <div class="alert alert-<?= esc($type, 'attr'); ?>"
                 @mouseover="pauseCountdown"
                 @mouseleave="resumeCountdown"
            >
                <span><?= esc($message['message']); ?></span>
            </div>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>
