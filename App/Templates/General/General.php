<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/Css/General.css">
    <title>Camagru</title>
    <?= $js ?>
</head>
<body>
    <div id="header">
        <a href="index">Accueil</a>
        <a href="account">Mon compte</a>
        <a href="montage">Montage</a>
        <?php if ($_logged) { ?>
        <form action="/user/logout" method="post">
            <input type="hidden" name="token" value="<?= $token ?>">
            <input type="submit" name="logout" value="Se déconnecter">
        </form>
        <?php } ?>
    </div>
    <div id="center">
        <?= $content ?>
    </div>
    <footer>
        <div class="social-networks">
            <div class="social">
                <a href="https://www.instagram.com/?hl=fr">
                    <img src="/Pictures/Instagram.png" alt="Instagram Logo">
                </a>
            </div>
            <div class="social">
                <a href="https://www.facebook.fr/">
                    <img src="/Pictures/Facebook.png" alt="Facebook Logo">
                </a>
            </div>
            <div class="social">
                <a href="https://www.twitter.com/">
                    <img src="/Pictures/Twitter.png" alt="Twitter Logo">
                </a>
            </div>
        </div>
        <h3>© lbarthon 2019</h3>
    </footer>
</body>
</html>