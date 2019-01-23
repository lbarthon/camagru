<form action="/user/login" method="post">
    <input type="text" name="username" placeholder="Nom d'utilisateur" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <input type="hidden" name="token" value="<?= $token ?>">
    <input type="submit" name="login" value="Se conneceter">
</form>
<?= $login_err ?>

<form action="/user/create" method="post">
    <input type="text" name="username" placeholder="Nom d'utilisateur" required>
    <input type="email" name="mail" placeholder="Mail" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <input type="hidden" name="token" value="<?= $token ?>">
    <input type="submit" name="create" value="Créer mon compte">
</form>
<?= $create_success ?>
<?= $create_err ?>