<?php foreach ($posts as $post) : ?>
    <?= view('discussions/posts/_post_with_replies', ['post' => $post, 'loadedReplies' => $loadedReplies]) ?>
<?php endforeach ?>
