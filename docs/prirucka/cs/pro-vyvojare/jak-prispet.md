# Jak přispět

phpRS je svobodný software a vyvíjí se veřejně na GitHubu v repozitáři `phprs-cms/cms`. Přispět můžete hlášením chyby, překladem, opravou nebo námětem. Tato stránka říká, jak na to, aby se váš příspěvek dal snadno přijmout.

## Hlášení chyb

Běžnou chybu nahlaste v Issues repozitáře. Dobré hlášení obsahuje:

- verzi phpRS – je v administraci dole v patičce a v **Nastavení → Zálohy a aktualizace**,
- verzi PHP a databáze – ukazuje je **Nastavení → Stav systému**,
- přesný postup, jak chybu vyvolat, co jste čekali a co se stalo,
- znění chybové zprávy; podrobnosti bývají v souboru `storage/log/chyby.log`,
- u chyb vzhledu použitou šablonu, prohlížeč a šířku okna, případně snímek obrazovky.

Než chybu nahlásíte, ověřte, že máte poslední verzi, a projděte stránku [Řešení potíží](../provoz/reseni-potizi.md). Z logů a snímků odstraňte hesla, tokeny a osobní údaje čtenářů.

## Bezpečnostní chyby

Bezpečnostní chybu **nehlaste veřejně** v Issues. Použijte soukromé hlášení na GitHubu – v repozitáři záložka **Security → Report a vulnerability** – nebo e-mail uvedený na webu projektu. Popište verzi, postup a dopad.

Co se děje potom:

1. Ozveme se do 3 pracovních dnů.
2. Oprava vzniká neveřejně. Běžně vychází do 14 dnů, u kritických chyb co nejdříve.
3. Vyjde jako verze označená za bezpečnostní. Instalace se po novinkách dívají dvakrát denně a takovou verzi si – pokud to správce nevypnul – nainstalují samy; správce dostane e-mail.
4. Po vydání opravy zveřejníme bezpečnostní oznámení s popisem, zasaženými verzemi a poděkováním nálezci.

Podporovaná je vždy poslední vydaná verze. Pravidla jsou i v souboru `SECURITY.md`.

## Překlady

Systém je přeložen do češtiny, slovenštiny, angličtiny a němčiny. Zdrojem je čeština; klíčem každého překladu je český text.

| Co | Kde |
|---|---|
| Texty webu a e-maily čtenářům | `system/jazyky/en.php`, `sk.php`, `de.php` |
| Administrace | `system/jazyky/admin-en.php`, `admin-sk.php`, `admin-de.php` |
| Vizuální editor bloků a editor článku | `image/jazyky/admin-<kód>.js` |
| Instalátor | `system/jazyky/install-<kód>.php` |
| Ukázkový obsah | `system/demo/` |
| Příručka | `docs/prirucka/<jazyk>/` – soubory se ve všech jazycích jmenují stejně, česky |

Opravu překladu pošlete jako pull request, nebo ji popište v Issues: původní znění, navržené znění a místo, kde jste text viděli. Chybějící překlady doplňujte nástrojem `tools/slovnik.py`, ne ruční úpravou slovníku – viz [Zásady projektu](zasady.md). Formát data pro jazyk určují ve slovníku webu klíče `datum_format` a `datum_slovy`.

Přidání dalšího jazyka je větší práce: tři slovníky, slovník pro JavaScript, instalátor, ukázkový obsah a zápis do `Core\Jazyk`. Domluvte se předem v Issues.

## Pull requesty

1. **U větší změny se nejdřív domluvte.** Založte Issue a popište záměr. Projekt drží úzký záběr – redakční systém pro magazíny – a jednoduchost má přednost před počtem funkcí. Ušetříte si práci na něčem, co by se nepřijalo.
2. Přečtěte si [Zásady projektu](zasady.md) a [Strukturu projektu](struktura-projektu.md).
3. Pracujte ve vlastní větvi. Jeden pull request řeší jednu věc.
4. Držte se stylu okolního kódu: `declare(strict_types=1)`, identifikátory domény česky bez diakritiky, komentáře česky. Komentář vysvětluje, proč kód něco dělá – ne co dělá.
5. Před odesláním spusťte `tools/test.sh`, nebo aspoň `php tools/testy.php`. K opravě chyby přidejte test, který by ji příště zachytil, pokud jde o logiku bez databáze.
6. Změna databáze potřebuje migraci, úpravu `schema.sql` a zvýšení `PHPRS_VERZE_DB`.
7. Nový text pro uživatele potřebuje překlad ve všech slovnících.
8. Mění-li se chování nebo popisek v administraci, upravte i českou příručku v `docs/prirucka/cs/`.
9. V popisu pull requestu uveďte, co a proč měníte a jak jste to vyzkoušeli. U změn vzhledu přiložte snímky ve světlém i tmavém režimu a v šířce telefonu.

Každý pull request projde kontrolou na GitHubu: kouřový test na podporovaných verzích PHP, Semgrep a Gitleaks. Podrobnosti jsou na stránce [Testy a vydávání](testy-a-vydavani.md).

### Co se nepřijímá

- nové závislosti, frameworky, kroky sestavení, externí písma a CDN,
- zásuvné moduly třetích stran a nahrávání kódu z administrace – rozšíření jsou uzavřená sada,
- nástroje napojení na Claude, které by sahaly mimo obsah a vlastní šablony,
- funkce pro jediný web. Vlastní vzhled patří do [vlastní šablony](../vzhled/vlastni-sablona.md), ne do jádra.

## Licence

phpRS je vydán pod licencí **GNU General Public License verze 2** nebo novější. Text je v souboru `LICENSE`. Odesláním příspěvku souhlasíte s jeho zveřejněním pod stejnou licencí. Nepřidávejte kód ani obrázky, jejichž licence s ní není slučitelná. Písmo administrace Noto Sans má vlastní licenci OFL, přiloženou v `image/pisma/OFL.txt`.

## Podpora projektu

Vývoj můžete podpořit i finančně přes GitHub Sponsors. Odkaz **Podpořit phpRS** je v patičce administrace; správce ho může vypnout v **Nastavení → Zálohy a aktualizace**.

## Související

- [Zásady projektu](zasady.md)
- [Testy a vydávání](testy-a-vydavani.md)
- [Bezpečnost](../provoz/bezpecnost.md)
- [Řešení potíží](../provoz/reseni-potizi.md)
