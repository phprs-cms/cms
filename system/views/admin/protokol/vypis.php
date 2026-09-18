<?php
/**
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\ProtokolZmen $modul
 * @var list<array<string, mixed>> $zaznamy
 * @var array<int, string> $uzivatele
 * @var int $kdo
 */
$nazvy = ['clanky' => 'Články', 'intergal' => 'Média', 'topic' => 'Rubriky', 'stranky' => 'Stránky', 'news' => 'Novinky', 'comment' => 'Komentáře', 'ankety' => 'Ankety',
    'reklama' => 'Reklama', 'bloky' => 'Bloky', 'users' => 'Uživatelé', 'presmerovani' => 'Přesměrování', 'config' => 'Nastavení', 'prihlaseni' => 'Přihlášení', 'ucet' => 'Můj účet'];
$akce = ['uloz' => 'uložení', 'smaz' => 'smazání', 'vydat' => 'vydání', 'hromadne' => 'hromadná akce', 'nahraj' => 'nahrání', 'login' => 'přihlášení', 'neuspech' => 'neúspěšný pokus',
    'rozvrzeni' => 'změna rozvržení', 'zalohuj' => 'záloha', 'aktualizuj' => 'aktualizace systému', 'slozka' => 'složka', 'ads_txt' => 'ads.txt'];
?>
<form method="get" action="<?= e($app->url('admin.php')) ?>" class="stred smltxt">
	<input type="hidden" name="modul" value="protokol">
	<label>Uživatel: <select name="kdo" onchange="this.form.submit()"><option value="0">všichni</option>
<?php foreach ($uzivatele as $idu => $jmeno): ?>
		<option value="<?= (int) $idu ?>"<?= $kdo === (int) $idu ? ' selected' : '' ?>><?= e($jmeno) ?></option>
<?php endforeach ?>
	</select></label>
</form>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th>Kdy</th><th>Kdo</th><th>Kde</th><th>Co</th><th>Podrobnost</th></tr></thead>
<tbody>
<?php foreach ($zaznamy as $z): ?>
<tr<?= $z['akce'] === 'neuspech' ? ' class="nevydany"' : '' ?>>
	<td class="cislo"><?= e(datum($z['cas'], true)) ?></td>
	<td><?= e($z['jmeno'] !== '' ? $z['jmeno'] : '–') ?></td>
	<td><?= e($nazvy[$z['modul']] ?? $z['modul']) ?></td>
	<td><?= e($akce[$z['akce']] ?? $z['akce']) ?></td>
	<td><?= e($z['popis']) ?></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<p class="smltxt">Posledních 300 záznamů. Protokol se uchovává půl roku.</p>
