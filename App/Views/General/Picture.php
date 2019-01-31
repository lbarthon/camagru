<div id="picture">
    <h3><?= $picture['username'] ?></h3>
    <img src="<?= $picture['img'] ?>">
    <p class="likes"><?= $picture['likes'] ?> likes</p>
    <form class="like" action="/like/<?= $picture['id'] ?>" method="post">
        <input type="hidden" name="token" value="<?= $token ?>">
        <input type="submit" name="like" value="Like">
    </form>
    <form class="like" action="/dislike/<?= $picture['id'] ?>" method="post">
        <input type="hidden" name="token" value="<?= $token ?>">
        <input type="submit" name="dislike" value="Unlike">
    </form>
<?php
    foreach ($picture['comments'] as $comment) {
        echo "<p><b>" . $comment['username'] . "</b> " . $comment['comment'] . "</p><br>";
    }
?>
    <form id="comment" class="comment" action="/comment/<?= $picture['id'] ?>" method="post">
        <input type="text" name="comment" placeholder="Votre commentaire...">
        <input type="hidden" name="token" value="<?= $token ?>">
        <input type="submit" name="submit" value="Comment">
    </form>
</div>