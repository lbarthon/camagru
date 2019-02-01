<div id="single_picture">
    <h3><?= htmlspecialchars($picture['username']) ?></h3>
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
    <div class="pic_comments">
<?php
    foreach ($picture['comments'] as $comment) {
        echo "<p class='comment'><b>" . htmlspecialchars($comment['username']) . "</b> " . $comment['comment'] . "</p><br>";
    }
?>
    </div>
    <form class="comment_form" action="/comment/<?= $picture['id'] ?>" method="post">
        <input type="text" name="comment" placeholder="Votre commentaire...">
        <input type="hidden" name="token" value="<?= $token ?>">
        <input type="hidden" name="username" value="<?php if (isset($_SESSION['user'])) { echo $_SESSION['user']; }?>">
        <input type="submit" name="submit" value="Comment">
    </form>
</div>
<script src="/Js/Pictures.js"></script>