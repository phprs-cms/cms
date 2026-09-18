<?php
/**
 * Systémový blok: vyhledávání.
 *
 * @var callable(string): string $url
 * @var string $q
 */
?>
<form class="hledani" method="get" action="<?= e($url('hledani')) ?>" role="search">
	<input type="search" name="q" value="<?= e($q) ?>" minlength="3" maxlength="100" aria-label="Hledaný text" required>
	<button type="submit">Hledat</button>
</form>
