<?php
    foreach ($matches as $picture) {
?>
<div class="picture">
    <h3><?= $picture['username'] ?></h3>
    <img src="<?= $picture['img'] ?>">
    <p class="likes"><?= $picture['likes'] ?></p>
</div>
<?php
    }
?>