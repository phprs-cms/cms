<?php
/**
 * Šablona článku "Standardní" pro layout Classic Newspaper.
 * Režimy: nahled / kratky (výpisy) a cely. První článek titulní strany ($poradi 0) je otvírák.
 *
 * @var array<string, mixed> $clanek  sloupce rs_clanky + tema_jm, tema_seo, autor_jm
 * @var string $rezim
 * @var int $poradi
 * @var callable(string): string $url
 * @var list<array<string, mixed>> $souvisejici
 */
$adresa = $url('clanek/' . $clanek['seo_link']);
$rubrika = '<a class="clanek-rubrika" href="' . e($url('rubrika/' . $clanek['tema_seo'])) . '">' . e($clanek['tema_jm']) . '</a>';
$cas = '<time datetime="' . e(date('c', strtotime($clanek['datum']))) . '">' . e(datum($clanek['datum'])) . '</time>';
?>
<?php if ($rezim === 'cely'): ?>
<article class="clanek clanek-cely">
	<header class="clanek-hlavicka">
		<?= $rubrika ?>
		<h1><?= e($clanek['titulek']) ?></h1>
		<div class="perex"><?= $clanek['uvod'] ?></div>
		<p class="clanek-podpis"><?= $clanek['autor_jm'] !== null ? '<span class="autor">' . e($clanek['autor_jm']) . '</span>' : '' ?><?= $cas ?></p>
	</header>
<?php if ($clanek['obrazek'] !== ''): ?>
	<figure class="clanek-foto"><img src="<?= e($clanek['obrazek']) ?>" alt=""></figure>
<?php endif ?>
	<div class="clanek-text"><?= $clanek['text'] ?></div>
	<footer class="clanek-paticka">
<?php if ($clanek['zdroj'] !== ''): ?>
		<span>Zdroj: <?= e($clanek['zdroj']) ?></span>
<?php endif ?>
		<span>Přečteno <?= (int) $clanek['visit'] + 1 ?>&times;</span>
	</footer>
<?php if ($souvisejici !== []): ?>
	<aside class="souvisejici">
		<h2>Související články</h2>
		<ul>
<?php foreach ($souvisejici as $s): ?>
			<li><a href="<?= e($url('clanek/' . $s['seo_link'])) ?>"><?= e($s['titulek']) ?></a></li>
<?php endforeach ?>
		</ul>
	</aside>
<?php endif ?>
</article>
<?php else: ?>
<article class="clanek clanek-nahled<?= $poradi === 0 ? ' clanek-otvirak' : '' ?><?= $clanek['obrazek'] !== '' ? ' ma-foto' : '' ?>">
<?php if ($clanek['obrazek'] !== ''): ?>
	<<?= $rezim === 'nahled' ? 'a href="' . e($adresa) . '" tabindex="-1" aria-hidden="true"' : 'div' ?> class="clanek-foto"><img src="<?= e($clanek['obrazek']) ?>" alt="" loading="<?= $poradi === 0 ? 'eager' : 'lazy' ?>"></<?= $rezim === 'nahled' ? 'a' : 'div' ?>>
<?php endif ?>
	<div class="clanek-telo">
		<?= $rubrika ?>
		<h2><?= $rezim === 'nahled' ? '<a href="' . e($adresa) . '">' . e($clanek['titulek']) . '</a>' : e($clanek['titulek']) ?></h2>
		<div class="perex"><?= $clanek['uvod'] ?></div>
		<p class="clanek-podpis"><?= $clanek['autor_jm'] !== null ? '<span class="autor">' . e($clanek['autor_jm']) . '</span>' : '' ?><?= $cas ?></p>
	</div>
</article>
<?php endif ?>
