<form action="/user/edit" method="post">
    <input type="text" name="username" value="<?= $username ?>" required>
    <input type="email" name="email" value="<?= $email ?>" required>
    <input type="password" name="password" placeholder="Nouveau mot de passe">
    <?php if ($checked) { ?>
        <input type="checkbox" name="notifs" value="Notifications par mail?" checked>
    <?php } else { ?>
        <input type="checkbox" name="notifs" value="Notifications par mail?">
    <?php } ?>
    <input type="hidden" name="token" value="<?= $token ?>">
    <input type="submit" name="edit" value="Enregisterer les modifications">
</form>
<?= $edit_success ?>
<?= $edit_err ?>