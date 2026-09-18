<?php
/**
 * Přihlášení - podoba vychází z admin.html phpRS 2.
 *
 * @var PhpRS\Core\App $app
 * @var string|null $chyba
 * @var string $login
 */
?>
<!doctype html>
<html lang="cs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>phpRS admin rozhraní</title>
<link rel="stylesheet" href="<?= e($app->url('image/admin.css')) ?>?v=<?= e(PHPRS_VERSION) ?>">
</head>
<body class="login">
<h1>phpRS</h1>
<h3>Redakční systém<br>(Editorial system)</h3>
<p><strong>Vlož heslo!</strong></p>
<?php if ($chyba !== null): ?>
<p class="hlaska hlaska-chyba" role="alert"><?= e($chyba) ?></p>
<?php endif ?>
<form method="post" action="<?= e($app->url('admin.php')) ?>">
<?= $app->session->csrfField() ?>
<table>
<tr><td><label for="user">Uživatel (User):</label></td><td><input class="textpole" type="text" id="user" name="user" value="<?= e($login) ?>" size="20" maxlength="40" autocomplete="username" required autofocus></td></tr>
<tr><td><label for="password">Heslo (Password):</label></td><td><input class="textpole" type="password" id="password" name="password" size="20" autocomplete="current-password" required></td></tr>
</table>
<p><input class="tl" type="submit" value=" OK "></p>
</form>
</body>
</html>
