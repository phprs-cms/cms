# SEO

Většinu práce pro vyhledávače dělá systém sám: čitelné adresy, popisy, kanonické adresy, mapu webu, strukturovaná data i kanály. V **Nastavení → SEO a GEO** rozhodujete jen o tom hlavním. Tato stránka popisuje nastavení webu, volby u článku a přesměrování. Volby pro AI vyhledávače ze stejné záložky má vlastní stránka [AI vyhledávače](ai-vyhledavace.md).

## Viditelnost webu

| Pole | Význam | Výchozí |
|---|---|---|
| **Web smí být ve vyhledávačích** | Hlavní vypínač indexování. Vypněte jen u webu ve výstavbě. | zapnuto |
| **AI vyhledávače a asistenti** | viz [AI vyhledávače](ai-vyhledavace.md) | povolit |
| **Obrázek pro sdílení** | Ukáže se na sociálních sítích u stránek bez vlastního obrázku. Ideálně 1200×630 px. | prázdné |

Když **Web smí být ve vyhledávačích** vypnete, soubor `robots.txt` zakáže procházení celého webu a všechny stránky dostanou značku `noindex`. Nepošle se ani žádné oznámení přes IndexNow. Před spuštěním webu nezapomeňte volbu zapnout.

## Ověření vlastnictví webu

Rozbalte **Ověření vlastnictví webu (Google Search Console, Bing)**:

1. V Google Search Console zvolte ověření značkou HTML a zkopírujte hodnotu `content` z meta tagu `google-site-verification`. Vložte ji do pole **Google**.
2. Pro Bing Webmaster Tools vložte do pole **Bing** hodnotu `content` z meta tagu `msvalidate.01`.
3. Uložte a ověření ve službě dokončete.
4. Do služby pak vložte adresu mapy webu – je vypsaná pod poli, například `https://www.example.cz/sitemap.xml`.

## Co systém generuje

Odkazy na všechny soubory jsou dole v oddílu **Pro pokročilé**.

| Adresa | Obsah |
|---|---|
| `/robots.txt` | Pravidla pro roboty a odkaz na mapu webu. Vždy zakazuje `/admin.php`, vyhledávání a náhledy článků. |
| `/sitemap.xml` | Mapa webu: hlavní stránky všech jazykových verzí, rubriky, stránky a až 45 000 článků s datem poslední změny. Je jedna pro všechny jazyky. |
| `/sitemap-news.xml` | Mapa pro Google News: články za poslední dva dny. |
| `/rss.xml` | RSS kanál: 20 nejnovějších článků, titulek a perex. |
| `/feed.json` | JSON Feed 1.1: 20 nejnovějších článků včetně textu. |
| `/podcast.xml` | Podcastový kanál pro Apple Podcasts, Spotify a další aplikace: články se zvukovým souborem (mp3, m4a, ogg, oga, wav, aac), nejvýše 300 epizod. Zamčené články v něm nejsou. |

Krátké zprávy a články s volbou noindex se do map webu nezařazují. Na vícejazyčném webu mají RSS, JSON Feed, podcast i mapa pro Google News vlastní verzi pod předponou jazyka (`/en/rss.xml`).

Obal podcastu je **Obrázek pro sdílení**; když chybí, použije se logo webu. Jak z článku udělat epizodu, popisuje stránka [Typy obsahu](../psani/typy-obsahu.md).

## Pro pokročilé

| Pole | Význam | Výchozí |
|---|---|---|
| **Strukturovaná data schema.org** | Značky JSON-LD, podle kterých vyhledávače rozumí obsahu. | zapnuto |
| **Oznamovat nové články vyhledávačům (IndexNow)** | Bing, Seznam a Yandex se o článku dozvědí hned. | vypnuto |
| **Soubor llms.txt**, **Čistá verze článků (.md)** | viz [AI vyhledávače](ai-vyhledavace.md) | zapnuto |
| **Vlastní pravidla robots.txt** | Řádky, které se připojí do `robots.txt` za pravidla systému. | prázdné |

### Strukturovaná data

Se zapnutou volbou systém vkládá:

- na celý web údaje o webu a vydavateli včetně vyhledávacího pole,
- u článku typ `NewsArticle` s titulkem, daty, autorem, obrázkem a vydavatelem a drobečkovou navigaci (web → rubrika → článek),
- u živé reportáže typ `LiveBlogPosting` s jednotlivými zápisy,
- u článku s otázkami a odpověďmi `FAQPage`,
- u recenze hodnocení a recenzovaný předmět,
- u článku se zvukem nebo videem údaj o médiu,
- u zamčeného článku příznak placeného obsahu (viz [Zamčený obsah](../ctenari-a-prijmy/zamceny-obsah.md)).

Nic z toho nevyplňujete zvlášť – údaje se berou z článku.

### IndexNow

Po zapnutí si systém při uložení nastavení vytvoří klíč a vystaví ho na webu jako textový soubor; nic dalšího nenastavujete. Oznámení odchází při vydání článku (i naplánovaného) a při pozdější úpravě vydaného článku. Neoznamují se články s volbou noindex. Na adrese `localhost` a na doménách `.test` se neposílá nic.

## Volby u článku

Ve formuláři článku jsou tři pole pro vyhledávače:

| Pole | Význam |
|---|---|
| **Titulek pro vyhledávače** | Jiný titulek do výsledků hledání. Prázdné = titulek článku. |
| **Popis pro vyhledávače** | Nejvýše 320 znaků. Prázdné = začátek perexu. Návrh umí i [AI asistent](../psani/ai-asistent.md). |
| **Skrýt před vyhledávači (noindex)** | Článek zůstane na webu, ale dostane značku `noindex`, vypadne z map webu a neoznamuje se (IndexNow, Web Push, webhook). Automatický newsletter ho nezařadí. |

Kanonickou adresu, značky Open Graph pro sdílení a u jazykových verzí značky hreflang doplňuje systém sám. Popis a klíčová slova celého webu jsou v **Nastavení → Základní**.

## Přesměrování

Rozšíření **Přesměrování** je po instalaci zapnuté. Spravuje ho administrátor ve **Správa → Přesměrování**.

Nejčastější případ řeší systém sám: když změníte adresu vydaného článku, vznikne přesměrování 301 ze staré adresy na novou. Řetězy nevznikají – starší přesměrování se přepíšou na nový cíl.

Ruční přesměrování:

1. V oddílu **Přidat přesměrování** vyplňte **Stará adresa** – cestu na tomto webu, která už neexistuje, například `/stara-stranka.html`.
2. Do **Přesměrovat na** zadejte cíl: cestu (`/clanek/nova-adresa`) nebo celou adresu `https://…`.
3. Klepněte na **Přesměrovat**.

Přesměrování se použije jen tehdy, když na staré adrese nic není. Existující stránku nepřebije. Tabulka ukazuje u každého záznamu, kolikrát byl **Použit**.

Pod ní je přehled **Adresy, které čtenáři nenašli (404)** – 25 nejčastějších za posledních 60 dnů s počtem a datem. Odkaz u adresy předvyplní formulář, takže chybějící stránku přesměrujete na dvě klepnutí. **Vyprázdnit přehled** seznam smaže.

## Související

- [AI vyhledávače](ai-vyhledavace.md)
- [Měření a soukromí](mereni-a-soukromi.md)
- [Jazyky webu](../jazykove-verze/jazyky-webu.md)
- [Typy obsahu](../psani/typy-obsahu.md)
