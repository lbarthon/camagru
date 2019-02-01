<div id="page">
<?php
foreach ($matches as $picture) {
?>
<div class="picture">
    <h3><?= htmlspecialchars($picture['username']) ?></h3>
    <a href="/picture/<?= $picture['id'] ?>">
        <img src="<?= $picture['img'] ?>">
    </a>
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
<p>
<?php
} if ($prevpage) {
?>
<a href="/page/<?= $page - 1 ?>">Page précédente</a>
<?php
}
?>
<b>Page <?= $page + 1 ?> </b>
<?php
if ($nextpage) {
?>
<a href="/page/<?= $page + 1 ?>">Page suivante</a>
<?php 
}
?>
</p>
</div>
<script src="/Js/Pictures.js"></script>