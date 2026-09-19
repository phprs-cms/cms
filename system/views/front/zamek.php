<?php
/**
 * Výzva pod ukázkou zamčeného článku.
 *
 * @var bool $predplatne  článek je jen pro předplatitele
 * @var bool $prihlasen   čtenář je přihlášený (ale nemá předplatné)
 * @var string $text      vlastní text výzvy z Nastavení
 * @var string $ucet      adresa přihlášení s návratem na článek
 * @var bool $registrace
 */
?>
<aside class="rs-zamek" aria-label="<?= e(t('Zamčený obsah')) ?>">
	<strong><?= e(t($predplatne ? 'Tento článek je pro předplatitele' : 'Pokračování je pro přihlášené čtenáře')) ?></strong>
	<p><?= e($text !== '' ? $text : t($predplatne ? 'Předplatné podporuje naši redakci. Děkujeme, že nás čtete.' : 'Registrace je zdarma a zabere minutu.')) ?></p>
<?php if (!$prihlasen): ?>
	<p><a class="rs-tl" href="<?= e($ucet) ?>"><?= e(t('Přihlásit se')) ?></a><?php if ($registrace): ?> <a class="rs-tl rs-tl-vedlejsi" href="<?= e($ucet) ?>"><?= e(t('Zaregistrovat se')) ?></a><?php endif ?></p>
<?php endif ?>
</aside>
