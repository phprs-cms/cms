# Úprava přímo na webu

Překlep ve vydaném článku nebo zastaralou větu na stránce O nás opravíte rovnou tam, kde jste si jich všimli. Tlačítko **Upravit zde** otevře text v šabloně webu ve stejném editoru, jaký znáte z administrace. Nemusíte článek hledat v seznamu.

## Kdo tlačítko vidí

Tlačítko **Upravit zde** je vpravo dole na stránce článku a na samostatné stránce (O nás, Kontakt…). Vidí ho jen člověk přihlášený do administrace, který smí daný text upravit:

| Obsah | Kdo smí |
|---|---|
| Stránka | kdo má přístup do sekce **Stránky** (redaktor, administrátor) |
| Vydaný článek | kdo má přístup do sekce **Články**, smí vydávat a článek spadá mezi ty, které spravuje |

U článků platí stejná pravidla jako v administraci. Autor bez práva vydávat vydaný článek měnit nesmí, takže u něj tlačítko nevidí. Uživatel omezený na vybrané rubriky ho vidí jen u článků ze svých rubrik. Podrobnosti jsou na stránce [Role a oprávnění](../redakce/role-a-opravneni.md).

Čtenáři tlačítko nevidí nikdy. Na výpisech článků, stránce rubriky ani na hlavní stránce tlačítko není – jen u jednoho článku nebo stránky.

Úprava na webu je určená hlavně pro vydaný obsah. Funguje ale i v náhledu konceptu (tlačítko **Náhled** v editoru článku): **Upravit zde** i návrat po uložení zůstávají v náhledu.

## Postup

1. Přihlaste se do administrace a otevřete na webu článek nebo stránku.
2. Klepněte na **Upravit zde**. Místo textu se zobrazí formulář s editorem.
3. Upravte text.
4. Klepněte na **Uložit**. Vrátíte se na stejnou stránku a vidíte výsledek. **Zrušit** se vrátí beze změny.

## Co jde změnit

| Obsah | Pole |
|---|---|
| Článek | **Titulek**, **Perex**, **Text** |
| Stránka | **Titulek**, **Text** |

Editor má stejné nástroje jako v administraci včetně vkládání obrázků z Médií. Titulek nesmí zůstat prázdný – jinak se formulář vrátí s hlášením **Titulek nesmí zůstat prázdný.**

Všechno ostatní – rubriku, štítky, hlavní obrázek, datum, stav, nastavení pro vyhledávače, adresu stránky – změníte odkazem **Všechna nastavení v administraci**, který otevře úplný formulář. Neuložené změny z editoru na webu se při tom nepřenesou; nejdřív uložte.

Úprava na webu není stavitel stránek. Nejde jí měnit rozložení, sloupce ani bloky kolem obsahu. Bloky mají vlastní editor – viz [Bloky a rozvržení](bloky-a-rozvrzeni.md).

## Zámek článku

Dva lidé nemohou přepisovat tentýž článek zároveň. Zámek je společný s administrací:

- otevřením editoru si článek zamknete; dokud ho máte otevřený, zámek se sám prodlužuje,
- kolega, který se pokusí článek otevřít (na webu i v administraci), uvidí místo editoru hlášení **Text má právě otevřený …. Zkuste to za chvíli.**,
- uložením se zámek uvolní; po zavření okna bez uložení vyprší sám do tří minut.

Stránky zámek nemají.

## Revize a záznam změn

Uložení článku, při kterém se změnil titulek, perex nebo text, založí revizi stejně jako uložení v administraci. K předchozímu znění se vrátíte v úplném formuláři článku – viz [Plánování a revize](../psani/planovani-a-revize.md). Stránky revize nemají.

Každé uložení se zapíše do **Protokolu změn** jako „úprava přímo na webu“ s titulkem článku nebo stránky. Protokol vidí administrátor ve **Správa → Protokol změn**.

Po uložení vydaného článku se také:

- aktualizuje vyhledávání na webu,
- smaže cache stránek, takže čtenáři vidí opravu hned,
- odešle oznámení vyhledávačům přes IndexNow, pokud ho máte zapnuté (viz [SEO](../seo-a-ai/seo.md)).

Datum vydání se nemění a oznámení čtenářům (Web Push, webhook) se znovu neposílá.

## Související

- [Editor článku](../psani/editor.md)
- [Plánování a revize](../psani/planovani-a-revize.md)
- [Bloky a rozvržení](bloky-a-rozvrzeni.md)
- [Role a oprávnění](../redakce/role-a-opravneni.md)
