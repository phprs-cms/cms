# AI vyhledávače

Část čtenářů dnes nehledá ve vyhledávači, ale ptá se asistenta – ChatGPT, Claude, Perplexity, Gemini. Tito asistenti procházejí web podobně jako vyhledávače a v odpovědích na zdroje odkazují. Optimalizaci pro ně se říká GEO; proto se záložka v nastavení jmenuje **SEO a GEO**.

phpRS dává vydavateli dvě věci: rozhodnutí, zda AI roboty na web pustit, a – pokud ano – podklady, ze kterých web přečtou snadno a správně. Všechny volby jsou v **Nastavení → SEO a GEO**.

## Povolit, nebo zakázat

Pole **AI vyhledávače a asistenti** v oddílu **Viditelnost webu**:

| Volba | Co udělá |
|---|---|
| **povolit – obsah se může objevit v odpovědích AI s odkazem na web** | Výchozí. Roboti AI služeb mají stejná pravidla jako ostatní. |
| **zakázat – ChatGPT, Claude, Perplexity, Gemini a další** | Do `robots.txt` se přidá zákaz celého webu pro známé AI roboty. |

Zákaz se týká těchto robotů: GPTBot, OAI-SearchBot, ChatGPT-User, ClaudeBot, Claude-User, anthropic-ai, PerplexityBot, Perplexity-User, Google-Extended, Applebot-Extended, CCBot, Bytespider, Amazonbot, meta-externalagent a cohere-ai. Seznam je součástí systému; jiného robota doplníte vlastním pravidlem (viz níže).

Tři věci, které je dobré vědět:

- **Nejde o technickou ochranu.** `robots.txt` je žádost. Slušní roboti pravidlo respektují; robot, který ho ignoruje, se na web dostane dál.
- **Běžné vyhledávání se nemění.** Google-Extended a Applebot-Extended řídí jen využití obsahu pro AI. Roboti běžného vyhledávání, Googlebot a Bingbot, v seznamu nejsou.
- **Zákaz neplatí zpětně.** Co si služby přečetly dřív, tím se z nich neodstraní.

Jak se rozhodnout, je redakční a obchodní otázka. Povolení přináší zmínky a odkazy v odpovědích AI. Zákaz dává smysl vydavateli, který nechce, aby se jeho texty používaly k trénování modelů nebo aby AI odpověď nahradila návštěvu webu. Placený obsah je chráněn nezávisle na této volbě – ze zamčeného článku robot dostane jen ukázku, viz [Zamčený obsah](../ctenari-a-prijmy/zamceny-obsah.md).

Vlastní pravidla, například zákaz jediného robota, zapište do pole **Vlastní pravidla robots.txt** v oddílu **Pro pokročilé**:

```
User-agent: Bytespider
Disallow: /
```

## Soubor llms.txt

Volba **Soubor llms.txt** (výchozí: zapnuto) vystaví na adrese `/llms.txt` průvodce webem pro jazykové modely podle návrhu llmstxt.org. Je to prostý text ve formátu Markdown, který systém skládá sám:

- název webu a **Popis webu** z **Nastavení → Základní**,
- seznam zobrazených rubrik s odkazy a popisy,
- 30 nejnovějších článků s odkazem a začátkem perexu (bez článků s volbou **Skrýt před vyhledávači (noindex)**).

Nic v něm neudržujete ručně. Vyplatí se ale mít výstižný **Popis webu** a popisy rubrik – jsou to věty, podle kterých si model udělá o webu první představu.

Na vícejazyčném webu má každá verze vlastní soubor (`/en/llms.txt`) se svými rubrikami a články. Je-li zapnutá i čistá verze článků, vedou odkazy v souboru rovnou na ni.

Po vypnutí volby adresa `/llms.txt` přestane existovat.

## Čistá verze článků (.md)

Volba **Čistá verze článků (.md)** (výchozí: zapnuto) zpřístupní každý vydaný článek i jako prostý text ve formátu Markdown. Adresa vznikne přidáním `.md` za adresu článku:

```
https://www.example.cz/clanek/muj-clanek
https://www.example.cz/clanek/muj-clanek.md
```

Čistá verze obsahuje:

- titulek,
- autora, datum vydání a aktualizace, rubriku a odkaz na původní článek jako zdroj,
- body **Ve zkratce**, pokud je článek má,
- perex a text převedené na Markdown.

Neobsahuje navigaci, bloky, reklamy, komentáře ani skripty. Model tak dostane samotný text s uvedením zdroje a nemusí ho vybírat z HTML stránky.

Další vlastnosti:

- Stránka článku na čistou verzi odkazuje v hlavičce (`rel="alternate"`, typ `text/markdown`), takže ji nástroje najdou samy.
- Čistá verze se posílá s hlavičkou `X-Robots-Tag: noindex`. Ve výsledcích běžného vyhledávání se neobjeví a nevzniká duplicitní obsah.
- Zamčený článek má v čisté verzi jen perex a ukázku, stejně jako na webu.
- Krátké zprávy čistou verzi nemají.

Čistá verze se hodí i lidem: pro archivaci, převzetí textu partnerským webem nebo čtení v terminálu.

## Co pomáhá při psaní

Přehledně členěný text s uvedeným autorem a datem se strojově čte snáze. Editor k tomu nabízí tři nástroje; všechny popisuje kapitola Psaní:

- **Ve zkratce** – tři až pět bodů s hlavními fakty. Zobrazí se nad článkem a jdou i do čisté verze.
- **Otázky a odpovědi** – vypíšou se pod článkem a do strukturovaných dat jdou jako `FAQPage`.
- **Označit jako aktualizovaný** – čtenář uvidí u článku „Aktualizováno“ s datem; datum změny se přenáší i do strukturovaných dat.

Strukturovaná data (autor, vydavatel, data, drobečková navigace) doplňuje systém sám – viz [SEO](seo.md).

## Veřejné API

Pro strojové čtení obsahu vlastní aplikací slouží rozšíření **Veřejné API** (hlavní nabídka **Rozšíření**; výchozí: vypnuto). Je to čtecí JSON API na adresách `/api/clanky`, `/api/clanky/<adresa>` a `/api/rubriky`. Zamčené články vrací s ukázkou a příznakem `zamceno`.

## Související

- [SEO](seo.md)
- [Napojení na Claude](napojeni-na-claude.md)
- [Editor článku](../psani/editor.md)
- [Zamčený obsah](../ctenari-a-prijmy/zamceny-obsah.md)
