# Napojení na Claude

Rozšíření **Napojení na Claude** zpřístupní web asistentovi Claude přes protokol MCP (Model Context Protocol). Claude pak umí na váš pokyn číst a psát články, zakládat rubriky, spravovat bloky a tvořit vlastní šablony webu – s právy vašeho účtu a jen v mezích popsaných níže.

Neplést s [AI asistentem](../psani/ai-asistent.md). Ten je součástí editoru článku a jen navrhuje texty. Napojení na Claude funguje opačně: Claude běží u vás (aplikace Claude, Claude Code) a web je pro něj nástroj.

## Co napojení umí a co ne

| Oblast | Nástroje | Kdo |
|---|---|---|
| Přehled | informace o webu, role a oprávnění přihlášeného | všichni |
| Články | seznam, načtení, založení, úprava | podle role, viz níže |
| Rubriky | strom rubrik; založení rubriky | čtení všichni; založení redaktor a administrátor |
| Média | seznam naposledy nahraných obrázků s adresami | všichni |
| Bloky | seznam bloků podle zón; založení a úprava bloku | administrátor |
| Šablony | seznam, zkopírování vestavěné šablony, čtení a uložení souboru vlastní šablony, přepnutí webu na šablonu | administrátor |

**Hranice je pevná: přes napojení se mění jen obsah a vlastní šablony.** Žádný nástroj neumí:

- měnit kód systému, soubory v `system/`, `admin.php`, `index.php` ani vestavěné šablony,
- zapsat soubor jinam než do složky vlastní šablony `layout/<název>/` – a tam jen soubory `.php` a `.css` do 300 kB,
- spustit kód, příkaz nebo databázový dotaz,
- spravovat uživatele, hesla, tokeny ani oprávnění,
- měnit Nastavení, rozšíření, poštu, zálohy nebo aktualizace,
- nahrávat soubory do Médií, mazat články, číst komentáře, údaje čtenářů a odběratelů.

Každý soubor PHP ukládaný do šablony projde kontrolou, která dovolí jen výpis předaných dat. Práci se soubory, sítí, procesy a databází odmítne a soubor se neuloží. Podrobnosti jsou na stránce [Vlastní šablona](../vzhled/vlastni-sablona.md).

Chybí-li vám v systému funkce, Claude ji přes napojení nedoplní. Je to záměr: systém má být pro všechny stejný a aktualizovatelný. Náměty patří autorům phpRS – viz [Jak přispět](../pro-vyvojare/jak-prispet.md).

## Oprávnění se řídí rolí

Claude jedná vaším jménem a s vašimi právy. Platí pro něj totéž co pro vás v administraci:

- **Autor** vidí a upravuje jen své články. Bez práva vydávat ukládá jen koncepty a vydaný článek nezmění.
- Uživatel omezený na vybrané rubriky pracuje jen s články z těchto rubrik a do jiné rubriky nic neuloží.
- **Redaktor** pracuje se všemi články a smí zakládat rubriky.
- **Administrátor** má navíc bloky a šablony.

Nový článek vzniká vždy jako koncept. Vydat ho Claude smí jen s účtem, který má právo vydávat, a jen na váš výslovný pokyn. Při úpravě článku se předchozí verze uloží do historie revizí. Po uložení vrací Claude adresu náhledu a odkaz na úpravu v administraci.

## Zapnutí a token

1. Administrátor otevře **Správa → Rozšíření**, zaškrtne **Napojení na Claude** a uloží. Ve výchozím stavu je rozšíření vypnuté.
2. Každý uživatel, který chce napojení používat, otevře **Můj účet**, oddíl **Napojení na Claude**.
3. Vyplní **Název nového tokenu** – například „Claude na notebooku“ – a klepne na **Vytvořit token**.
4. Token se zobrazí **jen jednou**. Zkopírujte si ho hned. V databázi je uložen jen jeho otisk, takže ho později nejde zobrazit – jen zrušit a vytvořit nový.

Pod tokenem je hotový příkaz pro Claude Code:

```
claude mcp add --transport http phprs https://www.example.cz/mcp --header "Authorization: Bearer phprs_…"
```

V aplikaci Claude přidejte vlastní konektor s adresou `https://www.example.cz/mcp` a stejnou hlavičkou `Authorization`.

Adresa napojení je vždy adresa webu zakončená `/mcp`. Funguje jen přes HTTPS (výjimkou je vývoj na `localhost`) a jen se zapnutým rozšířením. Přístupná zůstává i v režimu údržby.

### Správa tokenů

Seznam tokenů v **Můj účet** ukazuje u každého název, datum vytvoření a kdy byl naposledy použit. **Zrušit token** ho okamžitě zneplatní.

Při změně hesla je předem zaškrtnutá volba **zrušit i tokeny napojení (Claude, API)**. Měníte-li heslo kvůli podezření na zneužití, nechte ji zaškrtnutou a napojení pak vytvořte znovu. Token zablokovaného uživatele nefunguje.

## Záznam v Protokolu změn

Každý zásah, který něco mění – založení a úprava článku, rubriky nebo bloku, vytvoření šablony, uložení souboru šablony, přepnutí šablony – se zapíše do **Správa → Protokol změn** jako modul `claude` s názvem nástroje a titulkem, názvem nebo číslem dotčené položky. Zapíše se pod uživatelem, kterému token patří. Vytvoření tokenu se zapisuje také.

Čtení (seznamy, načtení článku nebo souboru šablony) se nezapisuje.

## Bezpečnostní doporučení

- **Token chraňte jako heslo.** Funguje bez hesla i bez dvoufázového přihlášení. Nevkládejte ho do sdílených dokumentů, repozitářů ani do chatu.
- **Pro každé zařízení a účel vlastní token.** Při ztrátě notebooku zrušíte jeden a ostatní fungují dál. Nepoužívané tokeny mažte.
- **Používejte účet s nejnižší potřebnou rolí.** Na psaní článků stačí účet autora nebo redaktora. Token administrátora vytvářejte jen pro práci na blocích a šablonách a potom ho zrušte.
- **Čtěte, co Claude udělal.** Nové články jsou koncepty – před vydáním je přečtěte. Šablonu si před přepnutím prohlédněte v náhledu `/?sablona=název`.
- **Průběžně kontrolujte Protokol změn**, hlavně po práci se šablonami.
- Po 20 neplatných pokusech o přihlášení tokenem z jedné adresy během 15 minut server další pokusy z této adresy dočasně odmítá.

Když napojení nepoužíváte, rozšíření vypněte. Adresa `/mcp` pak přestane odpovídat a tokeny zůstanou uložené pro příští zapnutí.

## Související

- [Vlastní šablona](../vzhled/vlastni-sablona.md)
- [AI asistent](../psani/ai-asistent.md)
- [Bezpečnost](../provoz/bezpecnost.md)
- [Role a oprávnění](../redakce/role-a-opravneni.md)
