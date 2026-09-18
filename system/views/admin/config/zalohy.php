<?php
/** Záložka Zálohy a aktualizace. */
?>
<fieldset>
<legend>Aktualizace systému</legend>
<p>Nainstalovaná verze: <strong><?= e($aktualizace['aktualni']) ?></strong></p>
<?php if (!$aktualizace['nastaveno']): ?>
<p class="hlaska">Zdroj aktualizací zatím není nastaven. Novou verzi nahrajete přes FTP (přepište všechny soubory kromě config.php, media/ a storage/); databáze se upraví sama.</p>
<?php elseif ($aktualizace['chyba'] !== null): ?>
<p class="hlaska hlaska-chyba"><?= e($aktualizace['chyba']) ?></p>
<?php elseif ($aktualizace['nova'] !== null): ?>
<div class="hlaska hlaska-ok">
	<p><strong>K dispozici je verze <?= e($aktualizace['nova']['verze']) ?></strong><?= !empty($aktualizace['nova']['vydano']) ? ' (' . e(datum((string) $aktualizace['nova']['vydano'])) . ')' : '' ?></p>
<?php if ($aktualizace['nova']['zmeny'] !== []): ?>
	<ul><?php foreach ($aktualizace['nova']['zmeny'] as $zmena): ?><li><?= e($zmena) ?></li><?php endforeach ?></ul>
<?php endif ?>
	<p><button class="tl" type="submit" formaction="<?= e($modul->url('aktualizuj')) ?>" onclick="return confirm('Aktualizovat systém? Nejprve se vytvoří záloha databáze. Web bude několik vteřin nedostupný.');">Aktualizovat na <?= e($aktualizace['nova']['verze']) ?></button></p>
</div>
<p class="napoveda">Před aktualizací se zazálohuje databáze. Balíček se přijme jen s platným podpisem vydavatele. Nepřepisuje se config.php, nahraná média ani vlastní šablony webu.</p>
<?php else: ?>
<p>Máte aktuální verzi.<?= $aktualizace['overeno'] ? ' <small>Ověřeno ' . e(date('j. n. Y H:i', $aktualizace['overeno'])) . '.</small>' : '' ?></p>
<?php endif ?>
<?php if ($aktualizace['nastaveno']): ?>
<p><button class="navigace" type="submit" formaction="<?= e($modul->url('zkontroluj')) ?>">Zkontrolovat teď</button></p>
<?php endif ?>
<?php $pole('aktualizace_url', 'Vlastní zdroj aktualizací', 'url', 'Nechte prázdné. Jinou adresu souboru aktualizace.json vyplňte jen tehdy, když si verze spravujete sami.', 'placeholder="https://"'); ?>
</fieldset>

<fieldset>
<legend>Zálohy databáze</legend>
<?php $pole('zalohy_auto', 'Automatická záloha jednou týdně', 'ano', 'Vytvoří se při přihlášení administrátora, když je poslední záloha starší než týden. Uchovává se posledních 10 záloh.'); ?>
<p><button class="tl" type="submit" formaction="<?= e($modul->url('zalohuj')) ?>">Vytvořit zálohu teď</button></p>
<?php if ($zalohy !== []): ?>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th>Soubor</th><th>Vytvořena</th><th>Velikost</th><th>Akce</th></tr></thead>
<tbody>
<?php foreach ($zalohy as $z): ?>
<tr>
	<td><?= e($z['soubor']) ?></td>
	<td class="cislo"><?= e(date('j. n. Y H:i', $z['cas'])) ?></td>
	<td class="cislo"><?= number_format($z['velikost'] / 1024, 0, ',', ' ') ?> kB</td>
	<td class="akce"><a href="<?= e($modul->url('stahni_zalohu', ['soubor' => $z['soubor']])) ?>">Stáhnout</a> · <button class="navigace" type="submit" formaction="<?= e($modul->url('smaz_zalohu')) ?>" name="soubor" value="<?= e($z['soubor']) ?>" onclick="return confirm('Smazat zálohu?');">Smaž</button></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<?php endif ?>
<p class="napoveda">Záloha obsahuje celou databázi (články, nastavení, uživatele). Nahrané obrázky ze složky media/ zálohujte přes FTP. Zálohy leží ve složce storage/zalohy/, která není z webu přístupná – stahujte si je i mimo server.</p>
</fieldset>
