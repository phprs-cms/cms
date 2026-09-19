<?php
/**
 * Nastavení nového hesla čtenáře (odkaz z e-mailu).
 *
 * @var string $akce
 * @var bool $chyba
 */
?>
<article class="clanek clanek-cely rs-ucet">
	<header class="clanek-hlavicka obal-uzky"><h1>Nové heslo</h1></header>
	<div class="obal-uzky">
<?php if ($chyba): ?>
	<p class="rs-zprava rs-zprava-chyba" role="alert">Heslo musí mít aspoň 8 znaků.</p>
<?php endif ?>
	<form class="rs-formular" method="post" action="<?= e($akce) ?>">
		<label>Nové heslo <small>(aspoň 8 znaků)</small> <input type="password" name="heslo" required minlength="8" autocomplete="new-password"></label>
		<button type="submit">Nastavit heslo a přihlásit</button>
	</form>
	</div>
</article>
