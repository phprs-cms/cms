<?php
/**
 * Výpis článků: hlavní stránka, rubrika i výsledky hledání.
 *
 * @var list<string> $nahledy  hotové HTML náhledů (šablony cla_*)
 * @var int $celkem
 * @var int $strana
 * @var int $stran
 * @var callable(int): string $strankaUrl
 * @var array<string, mixed>|null $rubrika
 * @var string|null $hledano
 * @var callable(string): string $url
 */
?>
<?php if ($rubrika !== null): ?>
<header class="vypis-hlavicka">
	<h1><?= e($rubrika['nazev']) ?></h1>
<?php if ($rubrika['popis'] !== ''): ?>
	<div class="perex"><?= $rubrika['popis'] ?></div>
<?php endif ?>
</header>
<?php elseif ($hledano !== null): ?>
<header class="vypis-hlavicka">
	<h1>Vyhledávání</h1>
	<form class="hledani" method="get" action="<?= e($url('hledani')) ?>" role="search">
		<input type="search" name="q" value="<?= e($hledano) ?>" minlength="3" maxlength="100" aria-label="Hledaný text" required>
		<button type="submit">Hledat</button>
	</form>
<?php if ($hledano !== ''): ?>
	<p><?= mb_strlen($hledano) < 3 ? 'Zadejte alespoň 3 znaky.' : 'Nalezeno článků: ' . $celkem ?></p>
<?php endif ?>
</header>
<?php endif ?>

<?php if ($nahledy === [] && $hledano === null): ?>
<p>Zatím zde nejsou žádné články.</p>
<?php endif ?>
<?= implode("\n", $nahledy) ?>

<?php if ($stran > 1): ?>
<nav class="strankovani" aria-label="Stránkování">
<?php if ($strana > 1): ?>
	<a href="<?= e($strankaUrl($strana - 1)) ?>" rel="prev">&laquo; novější</a>
<?php endif ?>
	<span>strana <?= $strana ?> z <?= $stran ?></span>
<?php if ($strana < $stran): ?>
	<a href="<?= e($strankaUrl($strana + 1)) ?>" rel="next">starší &raquo;</a>
<?php endif ?>
</nav>
<?php endif ?>
