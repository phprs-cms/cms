<?php
/**
 * Průběh rozesílky. Dokud zbývají příjemci, formulář se sám odesílá po dávkách.
 *
 * @var PhpRS\Admin\Moduly\NewsletterAdmin $modul
 * @var string $csrf
 * @var array<string, mixed> $vydani
 * @var int $zbyva
 */
?>
<?php if ($vydani['odeslano'] !== null): ?>
<p class="hlaska hlaska-ok">Hotovo. Newsletter „<?= e($vydani['predmet']) ?>“ odešel <?= (int) $vydani['pocet'] ?> odběratelům.</p>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url()) ?>">Zpět na newsletter</a></p>
<?php else: ?>
<p class="hlaska">Rozesílám „<?= e($vydani['predmet']) ?>“: odesláno <?= (int) $vydani['pocet'] ?>, zbývá <?= $zbyva ?>. Nechte stránku otevřenou.</p>
<form method="post" action="<?= e($modul->url('rozeslat', ['id' => $vydani['idn']])) ?>" id="davka">
	<?= $csrf ?>
	<p><button class="tl" type="submit">Pokračovat</button></p>
</form>
<script>setTimeout(function () { document.getElementById('davka').submit(); }, 1200);</script>
<?php endif ?>
