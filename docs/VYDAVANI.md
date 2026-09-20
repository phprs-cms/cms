# Vydávání phpRS a podpisové klíče

Instalace phpRS přijmou aktualizaci jen tehdy, když ji podepsal vydavatel. Bezpečnostní vydání se při výchozím nastavení
instalují **sama, bez kliknutí správce** – podpisový klíč je proto nejcitlivější věc v celém projektu. Tenhle dokument říká,
kde klíče leží, jak se vydává a co dělat, když se klíč ztratí nebo unikne.

## Dva klíče

| klíč | soubor (soukromý) | kde má ležet | k čemu |
| --- | --- | --- | --- |
| **provozní** | `tools/klice/vydavatel.key` | počítač vydavatele + šifrovaná záloha | podepisuje každé vydání |
| **záložní** | `tools/klice/zalozni.key` | **jen offline** – správce hesel a druhá kopie na papíře nebo USB mimo počítač | nepoužívá se; slouží k výměně provozního klíče |

Veřejné protějšky jsou v `system/aktualizace.pub` (na řádek jeden, za klíčem volitelný popis, řádky s `#` jsou poznámky).
Podpis platí, když sedí na **kterýkoli** z nich (`Core\Podpis`). Soubor je součást balíčku, takže ho každá aktualizace přepíše –
tím se nové klíče dostanou do instalací a odvolané z nich zmizí.

Podepisuje se řetězec `verze|sha256 balíčku|bezne nebo bezpecnostni` a zvlášť seznam souborů jádra (`system/soubory.json`).
Příznak bezpečnostního vydání je tedy krytý podpisem: kdo by ovládl jen web s manifestem, nemůže běžné vydání prohlásit
za bezpečnostní a vynutit jeho automatickou instalaci.

Soukromé klíče **nikdy** nepatří do gitu (hlídá `.gitignore`), do balíčku ani do cloudové synchronizace. Pozor: pokud složka
projektu leží v synchronizované složce (iCloud Drive, Dropbox), synchronizuje se i `tools/klice/` – přesuňte klíče jinam
a do `tools/klice/` dejte jen symbolický odkaz, nebo klíč předávejte proměnnou prostředí `PHPRS_KLIC`.

## Založení záložního klíče (jednou, před prvním veřejným vydáním)

```bash
php tools/vydani.php --novy-klic=zalozni
```

1. Soubor `tools/klice/zalozni.key` uložte do správce hesel a druhou kopii mimo počítač. Pak ho z disku smažte.
2. `system/aktualizace.pub` (přibyl řádek) commitněte. Instalace záložní klíč poznají od prvního vydání, které ho obsahuje –
   proto to udělejte **před** prvním veřejným vydáním, ať ho mají všechny.

## Běžné vydání

1. V `system/bootstrap.php` zvyšte `PHPRS_VERSION`, změnu commitněte a označte tagem `vX.Y.Z`.
2. `php tools/vydani.php X.Y.Z --url=<adresa ZIPu v GitHub Releases> --zmena="…" [--bezpecnostni]`
3. `dist/phprs-X.Y.Z.zip` nahrajte do GitHub Releases, `dist/aktualizace.json` na `https://phprs.dev/aktualizace.json`.
4. Na zkušební instalaci ověřte, že se aktualizace nabídne a nainstaluje.

`--bezpecnostni` používejte jen pro skutečné bezpečnostní opravy: taková vydání se instalují sama a správci dostanou e-mail.

Podepisujte **lokálně**, ne v GitHub Actions. V CI by klíčem mohl podepisovat každý, kdo smí měnit workflow, a bezpečnost
všech instalací by stála na zabezpečení jednoho účtu. CI sestavuje a testuje; podpis je jeden příkaz na počítači vydavatele.

## Plánovaná výměna provozního klíče

1. Starý `tools/klice/vydavatel.key` přesuňte do archivu (nemažte ho, dokud výměna neproběhne).
2. `php tools/vydani.php --novy-klic=provozni` – do `system/aktualizace.pub` přibude nový řádek. Starý řádek zatím ponechte.
3. Vydejte verzi podepsanou **starým** klíčem (dočasně ho vraťte na místo, nebo použijte `PHPRS_KLIC`). Přinese instalacím nový klíč.
4. V dalším vydání, už podepsaném novým klíčem, starý řádek z `system/aktualizace.pub` odstraňte.

## Ztráta provozního klíče

1. Vyzvedněte záložní klíč a uložte ho jako `tools/klice/zalozni.key`.
2. `php tools/vydani.php --novy-klic=provozni`, ztracený klíč z `system/aktualizace.pub` odstraňte.
3. `php tools/vydani.php X.Y.Z --klic=zalozni --url=…` – vydání podepsané záložním klíčem přinese nový provozní.
4. Záložní klíč vraťte offline. Další vydání už podepisuje nový provozní klíč.

## Únik provozního klíče (nebo jen podezření)

Postup je stejný jako při ztrátě, jen **hned** a vydání označte `--bezpecnostni`, aby se instalovalo samo. Dokud instalace
aktualizaci nepřijmou, kompromitovanému klíči věří – útočník ale k útoku potřebuje ještě ovládnout `phprs.dev/aktualizace.json`.
Proto zároveň změňte přístupy k hostingu webu a ke GitHubu a uživatele informujte.

Unikne-li **záložní** klíč, založte nový (`--novy-klic=zalozni` po přesunutí starého souboru), starý řádek odstraňte a vydejte
verzi podepsanou provozním klíčem.

## Když přijdete o oba klíče

Automatická cesta pak neexistuje. Instalace jde aktualizovat ručně (nahrát soubory přes FTP), a první ručně nahraná verze
přinese nový `system/aktualizace.pub`. Proto záložní klíč zálohujte na dvou nezávislých místech.
