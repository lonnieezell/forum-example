Hi <?= esc($user->username) ?>,

<br><br>

We're reaching out to let you know that your account is scheduled to be permanently removed on <strong><?= $userDelete->scheduled_at->toDateString(); ?></strong>.

<br><br>

If you haven't changed your mind on this, then you don't have to make any actions.

<br><br>

But if you have reconsidered your decision, you can <a href="<?= $userDelete->restore() ?>">click here</a> to restore your account.

<br><br>

<small>
    —<br>
    <?= config('App')->siteName; ?> Team<br>
    <?= site_url(); ?>
</small>
