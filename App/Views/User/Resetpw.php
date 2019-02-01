<form action="/<?= $url ?>" method="post" id="resetpw">
    <input type="text" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Nouveau mot de passe" required><br>
    <input type="hidden" name="token" value="<?= $token ?>">
    <input type="submit" name="confirm" value="Changer mon mot de passe">
</form>