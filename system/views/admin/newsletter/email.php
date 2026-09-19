<?php
/**
 * HTML podoba newsletteru - tabulkové rozvržení a vložené styly kvůli poštovním programům.
 *
 * @var PhpRS\Core\Settings $web
 * @var array<string, mixed> $vydani
 * @var list<array<string, mixed>> $clanky
 * @var string $koren     adresa webu s lomítkem na konci
 * @var string $odhlasit  adresa pro odhlášení
 */
$abs = fn (string $u): string => preg_match('#^https?://#i', $u) ? $u : rtrim($koren, '/') . '/' . ltrim($u, '/');
?>
<!doctype html>
<html lang="cs"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= e($vydani['predmet']) ?></title></head>
<body style="margin:0;padding:0;background:#f2f4f7;font-family:Georgia,'Times New Roman',serif;color:#14171f">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f2f4f7"><tr><td align="center" style="padding:24px 12px">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff">
	<tr><td style="padding:28px 32px 12px;border-bottom:3px solid #14171f"><a href="<?= e($koren) ?>" style="font-size:28px;font-weight:bold;color:#14171f;text-decoration:none"><?= e($web->get('nazev_webu')) ?></a></td></tr>
<?php if (trim($vydani['uvod']) !== ''): ?>
	<tr><td style="padding:24px 32px 0;font-size:17px;line-height:1.55"><?= nl2br(e($vydani['uvod'])) ?></td></tr>
<?php endif ?>
<?php foreach ($clanky as $c): ?>
	<tr><td style="padding:24px 32px 0">
<?php if ($c['obrazek'] !== ''): ?>
		<a href="<?= e($koren . 'clanek/' . $c['seo_link']) ?>"><img src="<?= e($abs($c['obrazek'])) ?>" alt="" width="536" style="display:block;width:100%;max-width:536px;height:auto;border:0;margin-bottom:12px"></a>
<?php endif ?>
		<a href="<?= e($koren . 'clanek/' . $c['seo_link']) ?>" style="font-size:22px;line-height:1.25;font-weight:bold;color:#14171f;text-decoration:none"><?= e($c['titulek']) ?></a>
		<p style="margin:8px 0 0;font-size:16px;line-height:1.5;color:#3a3d45"><?= e(mb_strimwidth(trim(strip_tags($c['uvod'])), 0, 260, '…')) ?></p>
		<p style="margin:8px 0 0;font-family:Arial,sans-serif;font-size:14px"><a href="<?= e($koren . 'clanek/' . $c['seo_link']) ?>" style="color:#1f4fe0;font-weight:bold"><?= e(t('Číst článek →')) ?></a></p>
	</td></tr>
<?php endforeach ?>
	<tr><td style="padding:32px;font-family:Arial,sans-serif;font-size:12px;line-height:1.5;color:#667085">Tento e-mail dostáváte, protože jste se přihlásili k odběru novinek z webu <?= e($web->get('nazev_webu')) ?>.<br><a href="<?= e($odhlasit) ?>" style="color:#667085"><?= e(t('Odhlásit odběr')) ?></a></td></tr>
</table>
</td></tr></table>
</body></html>
