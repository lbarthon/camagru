<form action="<?= $url ?>" method="post">
    <input type="text" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Nouveau mot de passe" required>
    <input type="hidden" name="token" value="<?= $token ?>">
    <input type="submit" name="login" value="Changer mon mot de passe">
</form>