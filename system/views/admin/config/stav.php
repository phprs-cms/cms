<?php
/** Záložka Stav systému. */
$ikony = ['ok' => '✓', 'varovani' => '!', 'chyba' => '✕'];
$souhrn = PhpRS\Core\Stav::souhrn($kontroly);
$skupina = '';
?>
<p class="hlaska hlaska-<?= $souhrn === 'ok' ? 'ok' : 'chyba' ?>"><?= ['ok' => 'Vše v pořádku.', 'varovani' => 'Systém běží, některé položky si zaslouží pozornost.', 'chyba' => 'Nalezeny chyby, které brání správnému provozu.'][$souhrn] ?></p>
<div class="tab-obal">
<table class="vypis">
<tbody>
<?php foreach ($kontroly as $k): ?>
<?php if ($k['skupina'] !== $skupina): $skupina = $k['skupina']; ?>
<tr><th colspan="3"><?= e($skupina) ?></th></tr>
<?php endif ?>
<tr>
	<td class="stred"><span class="stitek stitek-<?= ['ok' => 'vydano', 'varovani' => 'koncept', 'chyba' => 'chyba'][$k['stav']] ?>" title="<?= e($k['stav']) ?>"><?= $ikony[$k['stav']] ?></span></td>
	<td><strong><?= e($k['nazev']) ?></strong></td>
	<td><?= e($k['info']) ?></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<fieldset>
<legend>Monitoring</legend>
<?php if ($hodnoty['stav_token'] !== ''): ?>
<p>Stav ve formátu JSON pro dohledové nástroje (UptimeRobot, Zabbix…):<br><code><?= e($adresaWebu) ?>stav.json?token=<?= e($hodnoty['stav_token']) ?></code></p>
<?php else: ?>
<p>Dohledový nástroj může stav číst jako JSON. Nejprve vytvořte přístupový token.</p>
<?php endif ?>
<input type="hidden" name="stav_token" value="<?= e($hodnoty['stav_token']) ?>">
<p><button class="navigace" type="submit" name="novy_token" value="1"><?= $hodnoty['stav_token'] !== '' ? 'Vytvořit nový token (starý přestane platit)' : 'Vytvořit token' ?></button></p>
</fieldset>
