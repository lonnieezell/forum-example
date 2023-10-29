Hi <?= esc($user->username) ?>,

<br><br>

It looks like you just decided to delete your account.

<br><br>

We will remove your account permanently on: <strong><?= $userDelete->scheduled_at->toDateString(); ?></strong>, giving you some time to change your mind.

<br><br>

You can <a href="<?= $userDelete->restoreLink() ?>">click here</a> to restore your account.

<br><br>

<small>
    —<br>
    <?= config('App')->siteName; ?> Team<br>
    <?= site_url(); ?>
</small>
