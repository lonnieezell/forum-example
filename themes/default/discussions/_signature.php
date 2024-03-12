<?php if ($user && $user->signature): ?>
    <div class="signature text-sm border-t mt-4 py-4">
        <?= $user->renderSignature() ?>
    </div>
<?php endif ?>
