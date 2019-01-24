<form action="/setup" method="post">
    <input type="hidden" name="token" value="<?= $token ?>">
    <input type="submit" name="submit" value="Setup camagru ?">
</form>
<p style='color: red;'><?= $error ?></p>