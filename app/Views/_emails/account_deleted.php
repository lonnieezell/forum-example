Hi <?= esc($user->username) ?>,

<br><br>

We're reaching out to let you know that your account was permanently deleted.

<br><br>

Thank you for being a part of our community.

<small>
    —<br>
    <?= config(Config\App::class)->siteName; ?> Team<br>
    <?= site_url(); ?>
</small>
