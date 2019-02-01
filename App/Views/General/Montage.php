<div id="montage_parent">
<div id="montage">
<h2>Montage</h2>
<form action="." id="form_filter">
    <input type="radio" name="filter" id="Filtre">
    <label for="Filtre"><img src="/Pictures/Filters/Filtre.png" alt="Filtre"></label>
    <input type="radio" name="filter" id="Blue">
    <label for="Blue"><img src="/Pictures/Filters/Blue.png" alt="Blue"></label>
    <input type="radio" name="filter" id="Aucun">
    <label for="Aucun">Aucun</label>
</form>
<video id="video" width="640" height="400" autoplay></video>
<br>
<button id="take">Prendre une photo</button>
<br>
<canvas id="canvas" width="640" height="400"></canvas>
<form action="add_pic" method="post" id="add_pic">
    <input type="hidden" name="picture" id="picture">
    <input type="hidden" name="token" value="<?= $token ?>">
    <input type="submit" name="add_pic" value="Poster ma photo">
</form>
<?= $add_success ?>
<?= $add_error ?>
</div>
<div id="userspic">
<?php
    foreach ($matches as $picture) {
?>
<div class="montage_list">
    <img src="<?= $picture['img'] ?>">
    <form class="deletepic" action="/delete/<?= $picture['id'] ?>" method="post">
        <input type="hidden" name="token" value="<?= $token ?>">
        <input type="submit" name="delete" value="Supprimer cette photo">
    </form>
</div>
<?php
    }
?>
</div>
</div>
<script src="/Js/Montage.js"></script>