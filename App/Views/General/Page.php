<?php
    foreach ($matches as $picture) {
?>
<div class="picture">
    <h3><?= $picture['username'] ?></h3>
    <img src="<?= $picture['img'] ?>">
    <p class="likes"><?= $picture['likes'] ?> likes</p>
    <form action="like/<?= $picture['id'] ?>" method="post">
        <input type="hidden" name="token" value="<?= $token ?>">
        <input type="submit" name="like" value="Like">
    </form>
    <form action="dislike/<?= $picture['id'] ?>" method="post">
        <input type="hidden" name="token" value="<?= $token ?>">
        <input type="submit" name="dislike" value="Disike">
    </form>
</div>
<?php
    }
?>
<script>
    var forms = document.querySelectorAll('.picture form');
    forms.forEach(function(element) {
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