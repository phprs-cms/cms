<?php
/**
 * Přepínač jazykových verzí. Layout ho dostává hotový v proměnné $jazyky_html (prázdná, má-li web jediný jazyk).
 *
 * @var array<string, array{nazev:string, url:string, aktivni:bool, preklad:bool}> $jazyky
 */
?>
<span class="rs-jazyky" role="navigation" aria-label="Language">
<?php foreach ($jazyky as $kod => $j): ?>
	<a href="<?= e($j['url']) ?>" hreflang="<?= e($kod) ?>" lang="<?= e($kod) ?>" title="<?= e($j['nazev']) ?>"<?= $j['aktivni'] ? ' aria-current="true"' : '' ?>><?= e(strtoupper($kod)) ?></a>
<?php endforeach ?>
</span>
