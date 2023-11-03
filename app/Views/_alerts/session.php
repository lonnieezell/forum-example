<?php if ($alerts = session()->getFlashdata('alerts')): ?>
    <?= view('_alerts/alerts', ['alerts' => $alerts]); ?>
<?php endif; ?>
