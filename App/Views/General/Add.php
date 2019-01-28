<h1>Ajouter une photo</h1>
<video id="video" width="640" height="480" autoplay></video>
<button id="take">Prendre une photo</button>
<canvas id="canvas" width="640" height="480"></canvas>
<form action="add_pic" method="post" id="add_pic">
    <input type="hidden" name="picture" id="picture">
    <input type="hidden" name="token" value="<?= $token ?>">
    <input type="submit" name="add_pic" value="Poster ma photo">
</form>
<?= $add_success ?>
<?= $add_error ?>

<script>
    var canvas = document.getElementById('canvas');
    var context = canvas.getContext('2d');
    var video = document.getElementById('video');
    var took = false;

    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
            video.srcObject = stream;
            video.play();
        });
    }

    document.getElementById("take").addEventListener("click", function() {
        context.drawImage(video, 0, 0, 640, 480);
        took = true;
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

</script>