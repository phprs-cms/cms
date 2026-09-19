<?php
/**
 * Řádek formuláře "Jazyková verze" - jen když má web další jazyky (rozšíření Jazykové verze).
 *
 * @var PhpRS\Core\App $app
 * @var string $hodnota  aktuální hodnota sloupce jazyk ('' = výchozí jazyk)
 * @var string $napoveda
 */
use PhpRS\Core\Jazyk;

$dalsi = Jazyk::dalsi($app->settings());
if ($dalsi === []) {
    return;
}
?>
<div class="radek">
	<label for="jazyk">Jazyková verze</label>
	<div><select id="jazyk" name="jazyk">
		<option value=""><?= e(Jazyk::DOSTUPNE[Jazyk::vychozi($app->settings())][0]) ?> (výchozí)</option>
<?php foreach ($dalsi as $kod): ?>
		<option value="<?= e($kod) ?>"<?= $hodnota === $kod ? ' selected' : '' ?>><?= e(Jazyk::DOSTUPNE[$kod][0]) ?> – /<?= e($kod) ?>/</option>
<?php endforeach ?>
	</select>
<?php if ($napoveda !== ''): ?>
	<span class="napoveda"><?= e($napoveda) ?></span>
<?php endif ?>
	</div>
</div>
