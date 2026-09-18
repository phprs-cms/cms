<?php
/**
 * @var string $nadpis
 * @var string $obsah  HTML
 * @var int $typ       vzhled 1-5 zvolený v administraci
 * @var string $sys    zkratka systémového bloku, u běžného prázdná
 */
?>
<section class="blok blok-typ<?= $typ ?><?= $sys !== '' ? ' blok-' . e($sys) : '' ?>">
	<h2 class="blok-nadpis"><?= e($nadpis) ?></h2>
	<div class="blok-obsah">
<?= $obsah ?>
	</div>
</section>
