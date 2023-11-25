Hi <?= esc($user->username) ?>,

<br><br>

This is a daily moderation summary.

<br><br>

<table>
    <tr>
        <th scope="row" style="text-align: right;">Threads in the moderation queue:</th>
        <td><?= $summary['waiting']['thread'] ?? 0; ?></td>
    </tr>
    <tr>
        <th scope="row" style="text-align: right;">Posts in the moderation queue:</th>
        <td><?= $summary['waiting']['post'] ?? 0; ?></td>
    </tr>

    <tr>
        <th scope="row" style="text-align: right;">Threads approved today:</th>
        <td><?= $summary['thread']['approved'] ?? 0; ?></td>
    </tr>
    <tr>
        <th scope="row" style="text-align: right;">Threads denied today:</th>
        <td><?= $summary['thread']['denied'] ?? 0; ?></td>
    </tr>

    <tr>
        <th scope="row" style="text-align: right;">Posts approved today:</th>
        <td><?= $summary['post']['approved'] ?? 0; ?></td>
    </tr>
    <tr>
        <th scope="row" style="text-align: right;">Posts denied today:</th>
        <td><?= $summary['post']['denied'] ?? 0; ?></td>
    </tr>
</table>

<br><br>

Thank you for making our community better and safer place!

<br><br>

<small>
    —<br>
    You are receiving this because you are subscribed to this type of notification.<br>
    This behavior can be changed permanently in your <a href="<?= url_to('account-notifications') ?>">account settings</a>
</small>
