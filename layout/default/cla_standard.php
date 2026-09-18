<?php
/**
 * Šablona článku "Standardní".
 *
 * Stejně jako v phpRS 2 má tři režimy:
 *   nahled - úvod s odkazem na celý článek (hlavní stránka, rubrika, hledání)
 *   kratky - krátký článek: jen úvod, bez samostatné stránky
 *   cely   - celý článek
 *
 * @var array<string, mixed> $clanek  sloupce rs_clanky + tema_jm, tema_seo, autor_jm
 * @var string $rezim
 * @var int $poradi  pořadí ve výpisu od nuly (0 = první článek první stránky)
 * @var callable(string): string $url
 * @var list<array<string, mixed>> $souvisejici
 */
$adresa = $url('clanek/' . $clanek['seo_link']);
$info = function () use ($clanek, $url): string {
    return '<p class="clanek-info">'
        . '<a class="clanek-rubrika" href="' . e($url('rubrika/' . $clanek['tema_seo'])) . '">' . e($clanek['tema_jm']) . '</a> '
        . '<time datetime="' . e(date('c', strtotime($clanek['datum']))) . '">' . e(datum($clanek['datum'])) . '</time>'
        . ($clanek['autor_jm'] !== null ? ' &middot; ' . e($clanek['autor_jm']) : '')
        . '</p>';
};
?>
<?php if ($rezim === 'cely'): ?>
<article class="clanek clanek-cely">
	<header>
		<?= $info() ?>
		<h1><?= e($clanek['titulek']) ?></h1>
	</header>
<?php if ($clanek['obrazek'] !== ''): ?>
	<img class="clanek-obrazek" src="<?= e($clanek['obrazek']) ?>" alt="">
<?php endif ?>
	<div class="perex"><?= $clanek['uvod'] ?></div>
	<div class="clanek-text"><?= $clanek['text'] ?></div>
	<footer class="clanek-paticka">
<?php if ($clanek['zdroj'] !== ''): ?>
		<p>Zdroj: <?= e($clanek['zdroj']) ?></p>
<?php endif ?>
		<p>Přečteno: <?= (int) $clanek['visit'] + 1 ?>x</p>
	</footer>
<?php if ($souvisejici !== []): ?>
	<aside class="souvisejici">
		<h2>Související články</h2>
		<ul>
<?php foreach ($souvisejici as $s): ?>
			<li><a href="<?= e($url('clanek/' . $s['seo_link'])) ?>"><?= e($s['titulek']) ?></a> <small><?= e(datum($s['datum'])) ?></small></li>
<?php endforeach ?>
		</ul>
	</aside>
<?php endif ?>
</article>
<?php else: ?>
<article class="clanek clanek-nahled<?= $clanek['priority'] > 0 ? ' clanek-dulezity' : '' ?>">
	<?= $info() ?>
<?php if ($rezim === 'kratky'): ?>
	<h2><?= e($clanek['titulek']) ?></h2>
<?php else: ?>
	<h2><a href="<?= e($adresa) ?>"><?= e($clanek['titulek']) ?></a></h2>
<?php endif ?>
<?php if ($clanek['obrazek'] !== ''): ?>
	<img class="clanek-obrazek" src="<?= e($clanek['obrazek']) ?>" alt="" loading="lazy">
<?php endif ?>
	<div class="perex"><?= $clanek['uvod'] ?></div>
<?php if ($rezim === 'nahled'): ?>
	<p class="clanek-vice"><a href="<?= e($adresa) ?>" aria-label="Celý článek: <?= e($clanek['titulek']) ?>">Celý článek &raquo;</a></p>
<?php endif ?>
</article>
<?php endif ?>
