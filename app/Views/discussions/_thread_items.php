<?php foreach ($posts as $post) : ?>
    <?= view('discussions/posts/_post', ['post' => $post]) ?>
<?php endforeach ?>
