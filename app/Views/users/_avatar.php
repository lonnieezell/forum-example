<span style="width: <?= $size ?>px; height: <?= $size ?>px; font-size: <?= $fontSize ?>px; background-color: <?= $background ?>"
      class="avatar overflow-hidden" title="<?= $user->username ?>"
>
    <?php if ($user->avatarLink() !== '') : ?>
        <img class="img-fluid" src="<?= $user->avatarLink($size) ?>" alt="<?= $user->username ?>">
    <?php else :?>
        <?= $idString ?>
    <?php endif ?>
</span>
