Hi <?= esc($user->username) ?>,

<br><br>

There is a new reply in the thread: <strong><?= esc($thread->title) ?></strong>

<br><br>

<strong><?= esc($post->author->username) ?></strong> on <?= $post->created_at->format('F d, Y g:ia') ?> wrote:

<br><br>

<i><?= ellipsize($post->render(), 50) ?></i>
