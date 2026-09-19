<?php /** @var callable(string): string $url */ ?>
<header class="vypis-hlavicka"><h1><?= e(t('Stránka nenalezena')) ?></h1></header>
<p><?= e(t('Požadovaná stránka na webu není. Možná byl článek stažen nebo má jinou adresu.')) ?></p>
<p><a href="<?= e($url('')) ?>"><?= e(t('Přejít na hlavní stránku')) ?></a></p>
