<?php if ($setting): ?>
    <div class="pb-4">
        <a hx-get="<?= signedurl()->urlTo(
            'action-thread-notifications',
            $this->userId,
            $this->threadId,
            $notificationStatus === null ? 'mute' : 'unmute'
        ); ?>"
           hx-target="#mute-thread-cell"
           class="btn w-full">
            <?= $notificationStatus === null ?
                                view('icons/bell') . 'Mute notifications' :
                                view('icons/bell-slash') . 'Unmute notifications'
    ?>
        </a>
    </div>
<?php endif; ?>
