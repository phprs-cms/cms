# Custom template

When the colour, fonts and logo from [Site identity](identita-webu.md) are not enough for you, create your own template. It is a folder with a few PHP files and one stylesheet. This page is meant for someone who knows HTML and CSS and is not afraid of simple PHP.

Do not edit the built-in templates `classic-newspaper`, `modern-magazine` and `minimal`. Every update overwrites them, and **System status** reports a change to them as a modified core file. A custom template in its own folder is never overwritten by an update.

## Procedure

1. Copy the folder of the built-in template that is closest to what you have in mind into `layout/` under a new name. The folder name: lowercase letters, digits and hyphens, for example `layout/muj-magazin/`.
2. In the copy of `base.php` fix the path to the stylesheet – instead of `layout/classic-newspaper/style.css` it must say `layout/muj-magazin/style.css`. Otherwise the template would keep loading the stylesheet of the original.
3. Change the name and description in `info.php`.
4. Look at the result at the address `/?sablona=muj-magazin`. The preview works only for a signed-in administrator; readers still see the original template.
5. Edit `style.css` and the templates. Keep checking the home page, a section, an article, a page and the search – in light and dark mode and at phone width.
6. Turn the finished template on in **Appearance → Site identity**. It appears there as another card.

A template shows up in the selection when its folder contains the file `base.php`.

## Template files

| File | What it is for |
|---|---|
| `info.php` | Returns an array with the keys `nazev`, `popis` and `rozvrzeni` (`tri`, `dva`, `jeden` or `plna`) – the layout that is set when the template is selected. |
| `base.php` | The skeleton of the page: `<head>`, header, block zones, content, footer. |
| `blok.php` | The wrapper of a single block. |
| `cla_standard.php` | An article in three modes: in a listing (`nahled`, `kratky`) and in full (`cely`). |
| `style.css` | The look. |

A template file is looked for in your folder first and only then among the system ones in `system/views/front/`. With a file of the same name in your folder you can thus also replace the article listing (`vypis.php`), a page (`stranka.php`) or the content of a system block (`blok_rub.php`, `blok_nej.php`…). The fewer files you override, the less work you will have after updates.

## What the template receives

**`base.php`:**

| Variable | Content |
|---|---|
| `$web` | the site settings; only the methods `get('klic')`, `int('klic')`, `bool('klic')` |
| `$titulek` | the page title; empty on the home page |
| `$meta` | an array with `hlavni`, `popis`, `klicova_slova`, `obrazek`, `typ`, `noindex` |
| `$obsah` | the finished HTML of the content (listing, article…) |
| `$zony` | the HTML of the blocks: `hlavicka`, `leva`, `nad`, `pod`, `prava`, `paticka`; an empty zone is an empty string |
| `$rozvrzeni` | `tri`, `dva`, `jeden` or `plna` |
| `$rubriky`, `$stranky` | sections and pages for the navigation |
| `$url` | a function that turns a path into an address: `$url('rubrika/sport')` |
| `$kanonicka` | the canonical address of the page |
| `$jazyk`, `$jazyky_html` | the language code for `<html lang>` and the finished language switcher |
| `$hlava`, `$pata` | the system's tags for the head and for just before the end of the page |

> You must output `$hlava` before `</head>` and `$pata` before `</body>`. This is how SEO, structured data, analytics codes, the cookie banner, the shared article styles and scripts and the visual block editor get in. Without them the site will not work properly.

**`cla_standard.php`:** `$clanek` (the article columns plus `tema_jm`, `tema_seo`, `autor_jm`, and `stitky` for a full article), `$rezim`, `$poradi` (the position in the listing; 0 is the lead story), `$url`, `$souvisejici`. Just output the finished pieces of HTML `shrnuti_html`, `faq_html`, `hodnoceni_html`, `komentare_html` and `reklama_html`. Do not insert a `<p>` paragraph before the article text – the templates give the first paragraph a drop cap.

**`blok.php`:** `$nadpis`, `$obsah`, `$typ` (appearance 1–5; 5 means no heading), `$sys` (the short code of a system block) and `$zona`.

### Helper functions

| Function | What it does |
|---|---|
| `e($text)` | escapes text for output into HTML; use it on everything that is not finished HTML |
| `t('Text')` | translates a template text into the language of the site |
| `datum($d)`, `datum_slovy()` | the date in the format of the site language; the date in words |
| `cislo($n)` | a decimal number with a comma or a point according to the language |
| `slugify($text)`, `bez_diakritiky($text)` | converts text into an address; removes diacritics |

## Article templates

An article can have the **Long read**, **Photo story** or **Interview** template. They are variants: `cla_standard.php` gives the `<article>` element the class `sablona-dlouhe-cteni`, `sablona-fotoreportaz` or `sablona-rozhovor`, and `image/web.css` supplies the look. Your template must output this class too – in a copy of a built-in template it is already there.

If you want to give one of the variants completely different HTML, add the file `cla_dlouhe-cteni.php`, `cla_fotoreportaz.php` or `cla_rozhovor.php` to the folder. When it exists, it is used instead of `cla_standard.php`.

## Shared styles and overriding them

The file `image/web.css` is loaded in all templates, after your `style.css`. It contains two groups of rules:

- **The basic look of shared elements** – article tags, In brief, questions and answers, rating, comments, poll, advertising, block types. The rules are written in `:where()`, so they have zero specificity. Any rule in your `style.css` overrides them. Do not use `!important`.
- **Elements with an `rs-…` class** – the photo gallery, the player, the article lock, the reader account, the language switcher. They have the specificity of one class; you override them with a selector one class stronger, for example `.clanek-text .rs-zamek`.

Write into `style.css` only what is to look different. Do not edit `image/web.css`; an update overwrites it.

Further rules:

- Take the colour and fonts from the variables `--rs-akcent`, `--rs-pismo-titulky` and `--rs-pismo-text` with your own default value, for example `--akcent: var(--rs-akcent, #326891)`. Only then will Site identity work.
- Dark mode belongs at the end of `style.css`: `@media (prefers-color-scheme: dark) { :root[data-tmavy] { … } }`. `base.php` gives the `<html>` element the `data-tmavy` attribute according to the settings. Therefore write colours through variables.
- No external fonts or scripts from a CDN.
- The system adds image dimensions to the finished HTML by itself. For images with a fixed height in CSS, allow for the `height` attribute.

## Permitted PHP syntax

A template is a presentation layer: it outputs the data it has received. PHP files saved through the Claude connection are checked by `Core\SablonaKontrola`, and a file that breaks the rules is not saved. The check is an allowlist – whatever is not explicitly permitted does not pass. The built-in templates pass it, so copies of them can be edited further. Keep to the same rules when working by hand.

| Permitted | Forbidden |
|---|---|
| output `<?= e($x) ?>`, `if`, `foreach`, `for`, `while`, `match` | `include`, `require`, `eval`, backticks |
| closures: `$f = fn ($x) => …`, `$f = function () { … }` | named functions, `class`, `new`, `namespace`, importing classes through `use` |
| `$url('…')` and your own closures | calling another variable as a function, `$$x`, `${…}` in a string |
| `$web->get()`, `->int()`, `->bool()` | other object methods, class calls `Trida::metoda()` |
| functions for text, numbers, dates and arrays (`count`, `implode`, `mb_substr`, `number_format`, `date`, `array_map`, `preg_replace`…) | every other function: files, network, processes, database, reflection |
| a callback as a closure or `trim(...)` | a function name in a string (`'trim'`) |
| – | `$_GET`, `$_POST`, `$_COOKIE`, `$_SERVER`, `$_SESSION`, `$GLOBALS`, `$this`, `$app`, `$db`, `try`, `throw`, `exit`, `global`, `goto`, `clone` |

The reason is security. A template runs on every page view with the same rights as the system. If it were allowed to read files or call the network, a single planted template would be enough to take over the site. The complete list of permitted functions is in `system/src/Core/SablonaKontrola.php`.

A stylesheet (`.css`) is checked only for the obsolete executable constructs `expression(` and `behavior:`. A single file may be at most 300 kB.

## A template with the help of Claude

With the **Claude connection** extension turned on, Claude can create and edit a template through MCP – with an administrator account. For this it has tools for copying a built-in template, reading and saving a file, and switching the site to a template. It may write only into the folder of a custom template, only `.php` and `.css` files, and every PHP file goes through the check described above. After every save it receives the preview address `/?sablona=…`, which you open in your browser.

The setup is described on the page [Claude connection](../seo-a-ai/napojeni-na-claude.md). The rules for Claude working directly with the files are in `layout/CLAUDE.md`.

## Related

- [Site templates](sablony.md)
- [Site identity](identita-webu.md)
- [Updates](../zaciname/aktualizace.md)
- [System status](../provoz/stav-systemu.md)
