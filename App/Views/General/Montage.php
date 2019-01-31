<h2>Montage</h2>
<form action="">
    <input type="radio" name="filter" id="Filtre">
    <label for="Filtre"><img src="/Pictures/Filters/Filtre.png" alt="Filtre"></label>
    <input type="radio" name="filter" id="Aucun">
    <label for="Aucun"><p>Aucun</p></label>
</form>
<video id="video" width="640" height="480" autoplay></video>
<button id="take">Prendre une photo</button>
<canvas id="canvas" width="640" height="480"></canvas>
<form action="add_pic" method="post" id="add_pic">
    <input type="hidden" name="picture" id="picture">
    <input type="hidden" name="token" value="<?= $token ?>">
    <input type="submit" name="add_pic" value="Poster ma photo">
</form>
<?php
    foreach ($matches as $picture) {
?>
<div class="montage_list">
    <img src="<?= $picture['img'] ?>">
    <form action="/delete/<?= $picture['id'] ?>" method="post">
        <input type="hidden" name="token" value="<?= $token ?>">
        <input type="submit" name="delete" value="Supprimer cette photo">
    </form>
</div>
<?php
    }
?>
<script>
    var canvas = document.getElementById('canvas');
    var context = canvas.getContext('2d');
    var video = document.getElementById('video');
    var filters = document.getElementsByName('filter');
    var took = false;

    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
            video.srcObject = stream;
            video.play();
        });
    }

    document.getElementById("take").addEventListener("click", function() {
        for (var i = 0; i < filters.length; i++) {
            if (filters[i].checked) {
                context.drawImage(video, 0, 0);
                if (filters[i].id != "none") {
                    base_image = new Image();
                    base_image.src = filters[i].nextElementSibling.childNodes[0].src;
                    base_image.onload = function(){
                        context.drawImage(base_image, 0, 0);
                    }
                }
                took = true;
                return;
            }
        }
        alert("Vous devez choisir un filtre!");
    });

    document.getElementById("add_pic").addEventListener("submit", function(event) {
        if (took == false) {
            alert("Il faut déjà prendre une photo!");
            event.preventDefault();
            return false;
        } else {
            document.getElementById('picture').value = canvas.toDataURL('image/png');
        }
    });

    var del_forms = document.querySelectorAll('.montage_list form');
    del_forms.forEach(function(element) {
        element.addEventListener("submit", function(event) {
            event.preventDefault();
            var req;
            if (window.XMLHttpRequest) {
                req = new XMLHttpRequest();
            } else if (window.ActiveXObject) {
                req = new ActiveXObject("Microsoft.XMLHTTP");
            }
            if (req != undefined) {
                req.open("POST", element.action, true);
                req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");   
                var inputs = element.getElementsByTagName("input");
                var sendstr = inputs[0].name + "=" + inputs[0].value + "&" + inputs[1].name + "=" + inputs[1].value;
                req.send(sendstr);
                req.onload = function (e) {
                    if (req.readyState === 4 && req.status === 200) {
                        if (req.responseText == "error") {
                            window.location.href = "/account";
                        } else {
                            location.reload(true);
                        }
                    }
                }
            }
        });
    });
</script>