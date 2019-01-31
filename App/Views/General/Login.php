<div id="login">
    <form action="/user/login" method="post">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <input type="hidden" name="token" value="<?= $token ?>">
        <?= $login_err ?>
        <input type="submit" name="login" value="Se connecter">
    </form>

    <form action="/user/create" method="post">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
        <input type="email" name="mail" placeholder="Mail" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <input type="hidden" name="token" value="<?= $token ?>">
        <?= $create_success ?>
        <?= $create_err ?>
        <input type="submit" name="create" value="Créer mon compte">
    </form>

    <form action="/user/resetpw_ask" method="post">
        <input type="email" name="mail" placeholder="Mail" required><br>
        <input type="hidden" name="token" value="<?= $token ?>">
        <input type="submit" name="reset" value="Réinitialiser mon mot de passe">
    </form>
</div>