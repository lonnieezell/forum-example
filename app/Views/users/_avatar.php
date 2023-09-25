<span style="width: <?= $size ?>px; height: <?= $size ?>px; font-size: <?= $fontSize ?>px; background-color: <?= $background ?>"
      class="avatar overflow-hidden relative" title="<?= $user->username ?>"
>
    <?php if ($user->avatarLink() !== '') : ?>
        <img class="img-fluid relative z-10" src="<?= $user->avatarLink($size) ?>" alt="<?= $user->username ?>">
    <?php endif ?>

    <span class="absolute z-5 bottom-0 left-0 w-full h-full inline-block flex justify-center items-center text-sm mix-blend-screen">
        <?= $idString ?>
    </span>
</span>
