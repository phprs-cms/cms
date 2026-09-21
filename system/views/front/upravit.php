<?php
/**
 * Úprava stránky nebo článku přímo na webu: stejný editor jako v administraci, vsazený do šablony webu.
 * Ukládá se běžným formulářem do administrace (akce uloz_text), takže platí stejná oprávnění, revize i zámek.
 *
 * @var PhpRS\Core\App $app
 * @var string $typ       clanek | stranka
 * @var array<string, mixed> $zaznam
 * @var string $akce      adresa pro uložení
 * @var string $zpet      adresa, na kterou se po uložení nebo zrušení vrací
 * @var string $zamceno   jméno kolegy, který má článek právě otevřený ('' = volno)
 * @var bool $chyba       uložení se nepovedlo (prázdný titulek)
 */
?>
<link rel="stylesheet" href="<?= e($app->url('image/editor.css')) ?>?v=<?= e(PHPRS_VERSION) ?>">
<link rel="stylesheet" href="<?= e($app->url('image/vizual.css')) ?>?v=<?= e(PHPRS_VERSION) ?>">
<article class="clanek clanek-cely rs-upravit-text rs-ui">
<?php if ($zamceno !== ''): ?>
	<p class="rs-upravit-hlaska"><?= e(t('Text má právě otevřený %s. Zkuste to za chvíli.', $zamceno)) ?></p>
	<p><a class="rs-tl" href="<?= e($zpet) ?>"><?= e(t('Zpět')) ?></a></p>
<?php else: ?>
	<form method="post" action="<?= e($akce) ?>">
		<input type="hidden" name="_csrf" value="<?= e($app->session->csrfToken()) ?>">
		<input type="hidden" name="id" value="<?= (int) ($zaznam['idc'] ?? $zaznam['ids']) ?>">
		<input type="hidden" name="zpet" value="<?= e($zpet) ?>">
<?php if ($chyba): ?>
		<p class="rs-upravit-hlaska"><?= e(t('Titulek nesmí zůstat prázdný.')) ?></p>
<?php endif ?>
		<p><label for="rs-titulek"><?= e(t('Titulek')) ?></label>
			<input class="rs-upravit-titulek" type="text" id="rs-titulek" name="titulek" value="<?= e($zaznam['titulek']) ?>" maxlength="200" required></p>
<?php if ($typ === 'clanek'): ?>
		<p><label for="rs-uvod"><?= e(t('Perex')) ?></label>
			<textarea id="rs-uvod" name="uvod" rows="4" data-editor="maly"><?= e($zaznam['uvod']) ?></textarea></p>
<?php endif ?>
		<p><label for="rs-text"><?= e(t('Text')) ?></label>
			<textarea id="rs-text" name="text" rows="18" data-editor><?= e($zaznam['text']) ?></textarea></p>
		<div class="rs-upravit-lista">
			<button class="rs-tl" type="submit"><?= e(t('Uložit')) ?></button>
			<a class="rs-tl rs-tl-vedlejsi" href="<?= e($zpet) ?>"><?= e(t('Zrušit')) ?></a>
			<a class="rs-upravit-vse" href="<?= e($app->url('admin.php?modul=' . ($typ === 'clanek' ? 'clanky' : 'stranky') . '&akce=edit&id=' . (int) ($zaznam['idc'] ?? $zaznam['ids']))) ?>"><?= e(t('Všechna nastavení v administraci')) ?></a>
		</div>
	</form>
<?php endif ?>
</article>
<script src="<?= e($app->url('image/editor.js')) ?>?v=<?= e(PHPRS_VERSION) ?>" data-admin-url="<?= e($app->url('admin.php')) ?>" defer></script>
