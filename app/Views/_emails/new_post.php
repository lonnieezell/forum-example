Hi <?= esc($user->username) ?>,

<br><br>

There is a new reply in the thread: <strong><?= esc($thread->title) ?></strong>

<br><br>

<strong><?= esc($post->author->username) ?></strong> on <?= $post->created_at->format('F d, Y g:ia') ?> wrote:

<br><br>

<i><?= ellipsize(strip_tags($post->render()), 50) ?></i>

<br><br>

You can view this discussion in your browser by clicking <a href="<?= $post->link($category, $thread) ?>">here</a>.

<br><br>

<small>
    —<br>
    You are receiving this because you are subscribed to this type of notifications.<br>
    This behavior can be changed permanently in your <a href="<?= site_url(route_to('account-notifications')) ?>">account settings</a>.
</small>
