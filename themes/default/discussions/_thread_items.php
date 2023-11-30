<?php foreach ($posts as $post) : ?>
    <?= $this->view('discussions/posts/_post_with_replies', ['post' => $post, 'thread' => $thread, 'loadedReplies' => $loadedReplies]) ?>
<?php endforeach ?>
