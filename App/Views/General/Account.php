<form action="/user/edit" method="post" id="account">
    Username*: <input type="text" name="username" value="<?= $username ?>" required><br>
    Email*: <input type="email" name="email" value="<?= $email ?>" required><br>
    Password: <input type="password" name="password" placeholder="Nouveau mot de passe"><br>
    Notifications: <input type="checkbox" name="notifs" value="Notifications par mail?" <?php if ($checked) echo "checked"; ?>><br>
    <input type="hidden" name="token" value="<?= $token ?>">
    <?= $edit_success ?>
    <?= $edit_err ?>
    <input type="submit" name="edit" value="Enregistrer les modifications">
    <p id="info">Values with a * are needed.</p>
</form>