# Eigene Vorlage

Wenn Ihnen Farbe, Schriften und Logo aus der [Website-Identität](identita-webu.md) nicht genügen, erstellen Sie eine eigene Vorlage. Das ist ein Ordner mit einigen PHP-Dateien und einem Stylesheet. Diese Seite richtet sich an alle, die HTML und CSS beherrschen und sich vor einfachem PHP nicht scheuen.

Bearbeiten Sie die eingebauten Vorlagen `classic-newspaper`, `modern-magazine` und `minimal` nicht. Jede Aktualisierung überschreibt sie und der **Systemstatus** meldet einen Eingriff in sie als geänderte Datei des Kerns. Eine eigene Vorlage in einem eigenen Ordner wird bei einer Aktualisierung nie überschrieben.

## Vorgehen

1. Kopieren Sie den Ordner der eingebauten Vorlage, die Ihrem Vorhaben am nächsten kommt, unter einem neuen Namen nach `layout/`. Name des Ordners: Kleinbuchstaben, Ziffern und Bindestriche, zum Beispiel `layout/muj-magazin/`.
2. Korrigieren Sie in der Kopie von `base.php` den Pfad zum Stylesheet – statt `layout/classic-newspaper/style.css` muss dort `layout/muj-magazin/style.css` stehen. Sonst würde die Vorlage weiter das Stylesheet des Originals laden.
3. Ändern Sie in `info.php` Namen und Beschreibung.
4. Sehen Sie sich das Ergebnis unter der Adresse `/?sablona=muj-magazin` an. Die Vorschau funktioniert nur für einen angemeldeten Administrator, die Leser sehen weiter die bisherige Vorlage.
5. Bearbeiten Sie `style.css` und die Vorlagendateien. Prüfen Sie laufend Startseite, Ressort, Artikel, Seite und Suche – im hellen und im dunklen Modus und in der Breite eines Telefons.
6. Die fertige Vorlage schalten Sie unter **Design → Website-Identität** ein. Sie erscheint dort als weitere Karte.

Die Vorlage erscheint im Angebot, wenn ihr Ordner die Datei `base.php` enthält.

## Dateien der Vorlage

| Datei | Wozu sie dient |
|---|---|
| `info.php` | Gibt ein Array mit den Schlüsseln `nazev`, `popis` und `rozvrzeni` (`tri`, `dva`, `jeden` oder `plna`) zurück – das Layout, das bei der Auswahl der Vorlage eingestellt wird. |
| `base.php` | Gerüst der Seite: `<head>`, Kopf, Zonen der Blöcke, Inhalt, Fuß. |
| `blok.php` | Hülle eines einzelnen Blocks. |
| `cla_standard.php` | Der Artikel in drei Modi: in der Liste (`nahled`, `kratky`) und vollständig (`cely`). |
| `style.css` | Aussehen. |

Eine Vorlagendatei wird zuerst in Ihrem Ordner gesucht und erst danach unter den Systemdateien in `system/views/front/`. Mit einer gleichnamigen Datei in Ihrem Ordner können Sie so auch die Artikelliste (`vypis.php`), die Seite (`stranka.php`) oder den Inhalt eines Systemblocks (`blok_rub.php`, `blok_nej.php`…) ersetzen. Je weniger Dateien Sie überschreiben, desto weniger Arbeit haben Sie nach Aktualisierungen.

## Was die Vorlage bekommt

**`base.php`:**

| Variable | Inhalt |
|---|---|
| `$web` | Einstellungen der Website; nur die Methoden `get('klic')`, `int('klic')`, `bool('klic')` |
| `$titulek` | Titel der Seite; auf der Startseite leer |
| `$meta` | Array `hlavni`, `popis`, `klicova_slova`, `obrazek`, `typ`, `noindex` |
| `$obsah` | fertiges HTML des Inhalts (Liste, Artikel…) |
| `$zony` | HTML der Blöcke: `hlavicka`, `leva`, `nad`, `pod`, `prava`, `paticka`; eine leere Zone ist eine leere Zeichenkette |
| `$rozvrzeni` | `tri`, `dva`, `jeden` oder `plna` |
| `$rubriky`, `$stranky` | Ressorts und Seiten für die Navigation |
| `$url` | Funktion, die aus einem Pfad eine Adresse macht: `$url('rubrika/sport')` |
| `$kanonicka` | kanonische Adresse der Seite |
| `$jazyk`, `$jazyky_html` | Sprachcode für `<html lang>` und der fertige Sprachumschalter |
| `$hlava`, `$pata` | Markup des Systems für den Kopf und vor das Ende der Seite |

> `$hlava` müssen Sie vor `</head>` und `$pata` vor `</body>` ausgeben. Darüber laufen SEO, strukturierte Daten, Messcodes, Cookie-Leiste, gemeinsame Stile und Skripte des Artikels sowie der visuelle Blockeditor. Ohne sie funktioniert die Website nicht richtig.

**`cla_standard.php`:** `$clanek` (Spalten des Artikels und dazu `tema_jm`, `tema_seo`, `autor_jm`, beim vollständigen Artikel `stitky`), `$rezim`, `$poradi` (Reihenfolge in der Liste; 0 ist der Aufmacher), `$url`, `$souvisejici`. Die fertigen HTML-Stücke `shrnuti_html`, `faq_html`, `hodnoceni_html`, `komentare_html` und `reklama_html` geben Sie einfach aus. Setzen Sie vor den Artikeltext keinen Absatz `<p>` – die Vorlagen geben dem ersten Absatz eine Initiale.

**`blok.php`:** `$nadpis`, `$obsah`, `$typ` (Aussehen 1–5; 5 bedeutet ohne Überschrift), `$sys` (Kürzel des Systemblocks) und `$zona`.

### Hilfsfunktionen

| Funktion | Was sie tut |
|---|---|
| `e($text)` | bereitet Text für die Ausgabe in HTML auf; verwenden Sie sie für alles, was kein fertiges HTML ist |
| `t('Text')` | übersetzt einen Text der Vorlage in die Sprache der Website |
| `datum($d)`, `datum_slovy()` | Datum im Format der Sprache der Website; Datum in Worten |
| `cislo($n)` | Dezimalzahl mit Komma oder Punkt je nach Sprache |
| `slugify($text)`, `bez_diakritiky($text)` | Umwandlung eines Textes in eine Adresse; Entfernen diakritischer Zeichen |

## Artikelvorlagen

Ein Artikel kann die Vorlage **Longread**, **Fotoreportage** oder **Interview** haben. Das sind Varianten: `cla_standard.php` gibt dem Element `<article>` die Klasse `sablona-dlouhe-cteni`, `sablona-fotoreportaz` oder `sablona-rozhovor` und das Aussehen liefert `image/web.css`. Ihre Vorlage muss diese Klasse ebenfalls ausgeben – in der Kopie einer eingebauten Vorlage ist das schon so.

Wenn Sie einer Variante ein völlig anderes HTML geben möchten, legen Sie in den Ordner die Datei `cla_dlouhe-cteni.php`, `cla_fotoreportaz.php` oder `cla_rozhovor.php`. Wenn sie existiert, wird sie anstelle von `cla_standard.php` verwendet.

## Gemeinsame Stile und wie Sie sie überschreiben

Die Datei `image/web.css` wird in allen Vorlagen geladen, und zwar erst nach Ihrem `style.css`. Sie enthält zwei Gruppen von Regeln:

- **Grundaussehen der gemeinsamen Elemente** – Schlagwörter des Artikels, Kurz gefasst, Fragen und Antworten, Bewertung, Kommentare, Umfrage, Werbung, Blocktypen. Die Regeln stehen in `:where()`, haben also die Spezifität null. Jede beliebige Regel in Ihrem `style.css` überschreibt sie. Verwenden Sie kein `!important`.
- **Elemente mit der Klasse `rs-…`** – Fotogalerie, Player, Sperre des Artikels, Leserkonto, Sprachumschalter. Sie haben die Spezifität einer Klasse; Sie überschreiben sie mit einem Selektor, der um eine Klasse stärker ist, zum Beispiel `.clanek-text .rs-zamek`.

Schreiben Sie in `style.css` nur das, was anders aussehen soll. Bearbeiten Sie `image/web.css` nicht, eine Aktualisierung überschreibt sie.

Weitere Regeln:

- Farbe und Schriften nehmen Sie aus den Variablen `--rs-akcent`, `--rs-pismo-titulky` und `--rs-pismo-text` mit einem eigenen Standardwert, zum Beispiel `--akcent: var(--rs-akcent, #326891)`. Nur so funktioniert die Website-Identität.
- An das Ende von `style.css` gehört der Dunkelmodus: `@media (prefers-color-scheme: dark) { :root[data-tmavy] { … } }`. Das Attribut `data-tmavy` gibt `base.php` dem Element `<html>` je nach Einstellung. Schreiben Sie Farben deshalb über Variablen.
- Keine externen Schriften und keine Skripte von einem CDN.
- Die Maße der Bilder ergänzt das System im fertigen HTML selbst. Bei Bildern mit fester Höhe im CSS rechnen Sie mit dem Attribut `height`.

## Erlaubte PHP-Schreibweise

Die Vorlage ist die Präsentationsschicht: Sie gibt die Daten aus, die sie bekommen hat. PHP-Dateien, die über die Verbindung mit Claude gespeichert werden, prüft `Core\SablonaKontrola`, und eine Datei, die die Regeln verletzt, wird nicht gespeichert. Die Prüfung arbeitet mit einer Erlaubnisliste – was nicht ausdrücklich erlaubt ist, kommt nicht durch. Die eingebauten Vorlagen bestehen sie, ihre Kopien lassen sich also weiter bearbeiten. Halten Sie sich auch bei der Arbeit von Hand an dieselben Regeln.

| Erlaubt | Verboten |
|---|---|
| Ausgabe `<?= e($x) ?>`, `if`, `foreach`, `for`, `while`, `match` | `include`, `require`, `eval`, Backticks |
| Closures: `$f = fn ($x) => …`, `$f = function () { … }` | benannte Funktionen, `class`, `new`, `namespace`, Import von Klassen über `use` |
| `$url('…')` und eigene Closures | Aufruf einer anderen Variablen als Funktion, `$$x`, `${…}` in einer Zeichenkette |
| `$web->get()`, `->int()`, `->bool()` | andere Methoden von Objekten, Aufrufe von Klassen `Trida::metoda()` |
| Funktionen für Text, Zahlen, Datum und Arrays (`count`, `implode`, `mb_substr`, `number_format`, `date`, `array_map`, `preg_replace`…) | jede andere Funktion: Dateien, Netzwerk, Prozesse, Datenbank, Reflection |
| Callback als Closure oder `trim(...)` | Funktionsname in einer Zeichenkette (`'trim'`) |
| – | `$_GET`, `$_POST`, `$_COOKIE`, `$_SERVER`, `$_SESSION`, `$GLOBALS`, `$this`, `$app`, `$db`, `try`, `throw`, `exit`, `global`, `goto`, `clone` |

Der Grund ist die Sicherheit. Die Vorlage läuft bei jeder Anzeige einer Seite mit denselben Rechten wie das System. Dürfte sie Dateien lesen oder das Netzwerk aufrufen, würde eine einzige untergeschobene Vorlage genügen, um die Website zu übernehmen. Die vollständige Liste der erlaubten Funktionen steht in `system/src/Core/SablonaKontrola.php`.

Ein Stylesheet (`.css`) wird nur auf die veralteten ausführbaren Konstruktionen `expression(` und `behavior:` geprüft. Eine Datei darf höchstens 300 kB haben.

## Vorlage mit Hilfe von Claude

Mit eingeschalteter Erweiterung **Verbindung mit Claude** kann Claude die Vorlage über MCP erstellen und bearbeiten – mit dem Konto eines Administrators. Er hat dafür Werkzeuge zum Kopieren einer eingebauten Vorlage, zum Lesen und Speichern einer Datei und zum Umschalten der Website auf die Vorlage. Schreiben darf er nur in den Ordner einer eigenen Vorlage, nur Dateien `.php` und `.css`, und jede PHP-Datei durchläuft die oben beschriebene Prüfung. Nach jedem Speichern bekommt er die Adresse der Vorschau `/?sablona=…`, die Sie im Browser öffnen.

Die Einrichtung beschreibt die Seite [Verbindung mit Claude](../seo-a-ai/napojeni-na-claude.md). Die Regeln für Claude, der direkt mit den Dateien arbeitet, stehen in `layout/CLAUDE.md`.

## Siehe auch

- [Website-Vorlagen](sablony.md)
- [Website-Identität](identita-webu.md)
- [Aktualisierungen](../zaciname/aktualizace.md)
- [Systemstatus](../provoz/stav-systemu.md)
