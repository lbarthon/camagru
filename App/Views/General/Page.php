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
<?php
foreach ($picture['comments'] as $comment) {
    echo "<p class='comment'><b>" . htmlspecialchars($comment['username']) . "</b> " . $comment['comment'] . "</p><br>";
}
?>
    <form class="comment_form" action="/comment/<?= $picture['id'] ?>" method="post">
        <input type="text" name="comment" placeholder="Votre commentaire...">
        <input type="hidden" name="token" value="<?= $token ?>">
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
<script>
    var like_forms = document.querySelectorAll('.picture form.like');
    like_forms.forEach(function(element) {
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
    var comment_forms = document.querySelectorAll('.picture form.comment_form');
    comment_forms.forEach(function(element) {
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