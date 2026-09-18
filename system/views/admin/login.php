<?php
/**
 * Přihlášení. Retro podoba vychází z admin.html phpRS 2, prostředí 2026 ji přestyluje na kartu.
 *
 * @var PhpRS\Core\App $app
 * @var string|null $chyba
 * @var string $login
 * @var string $prostredi  výchozí prostředí webu (uživatel ještě není známý)
 */
$css = $prostredi === '2026' ? 'image/admin-2026.css' : 'image/admin.css';
?>
<!doctype html>
<html lang="cs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>phpRS admin rozhraní</title>
<link rel="stylesheet" href="<?= e($app->url($css)) ?>?v=<?= e(PHPRS_VERSION) ?>">
</head>
<body class="login prostredi-<?= e($prostredi) ?>">
<div class="login-karta">
<h1>php<b>RS</b></h1>
<h3>Redakční systém<br>(Editorial system)</h3>
<p class="login-vyzva"><strong>Vlož heslo!</strong></p>
<?php if ($chyba !== null): ?>
<p class="hlaska hlaska-chyba" role="alert"><?= e($chyba) ?></p>
<?php endif ?>
<form method="post" action="<?= e($app->url('admin.php')) ?>">
<?= $app->session->csrfField() ?>
<div class="login-pole"><label for="user">Uživatel (User):</label> <input class="textpole" type="text" id="user" name="user" value="<?= e($login) ?>" size="20" maxlength="40" autocomplete="username" required autofocus></div>
<div class="login-pole"><label for="password">Heslo (Password):</label> <input class="textpole" type="password" id="password" name="password" size="20" autocomplete="current-password" required></div>
<p><input class="tl" type="submit" value=" OK "></p>
</form>
</div>
</body>
</html>
