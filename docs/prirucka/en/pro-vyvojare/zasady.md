# Project principles

This page sums up the rules by which phpRS is developed. It saves a contributor a round of comments on a pull request. The full wording including the reasoning is in the file `CLAUDE.md` in the root of the repository; the rules for site templates are in `layout/CLAUDE.md`.

## Simplicity over abstraction

The code should be readable even by an informed layperson who wants to adjust their site. Therefore:

- no dependency containers, ORM, build steps or npm,
- a new dependency only with a strong reason,
- one administration module is one class, one screen is one template,
- the settings of a feature should come down to a few fields – the system does not ask about what can be derived.

The controls must be understandable to a person who sees the system for the first time. The administration has a single look; every change to its templates is checked in light and dark mode and at phone width.

## Extensions are a closed set

Optional features are listed in `Core\Rozsireni::SEZNAM`. All extensions are part of the package and are created within the project. The system has no third-party plug-ins, no public API for plug-in modules and no uploading of code from the administration – it is a security decision.

A new optional feature means:

1. an item in `Rozsireni::SEZNAM` (name, description, default state),
2. the `ROZSIRENI` constant on the administration module,
3. a `Rozsireni::je()` check everywhere the feature shows on the site.

The core – articles, media, sections, pages, blocks, users, settings – cannot be turned off. An extension that is turned off disappears from the menu and from the site; its data remain.

## Changing the database

Every change to the structure is written in three places:

1. **`system/sql/schema.sql`** – the complete schema for new installations.
2. **`system/sql/migrace/NNNN-popis.sql`** – a migration for existing sites. It runs by itself on the first visit to the site or the administration after an update; the number of the last one run is held by the `verze_db` setting.
3. **`PHPRS_VERZE_DB`** in `system/bootstrap.php` – raise it to the number of the new migration. `tools/test.sh` watches this.

A new column of the articles table that is to be visible in listings must also be added to `Front\Clanky::SLOUPCE_VYPISU` – listings deliberately do not load long texts.

## A new setting

A new option in Settings has three parts: a key with a default value in `Settings::DEFAULTS`, a type in `Konfigurace::POLE` (the value is sanitised on saving according to the type) and a `$pole(...)` line in the template `system/views/admin/config/<tab>.php`. Secret values (keys, passwords) have the type `tajne` and are never output back into the form.

## Translations

- Texts of the site and of the administration are wrapped in `t('Česky')`. The key is the Czech text.
- Add a text in a site template or in `system/views/front/` to the dictionaries `en.php`, `sk.php` and `de.php`; an administration text to `admin-en.php`, `admin-sk.php` and `admin-de.php`. E-mails to readers belong in the site dictionaries.
- Form values – the `value` of hidden fields and of buttons with a `name` attribute – are never translated.
- The texts of the visual block editor are in the administration language of the signed-in user, not in the language of the displayed version of the site: `T()` in JavaScript, the dictionary `image/jazyky/admin-<code>.js`.

Do not add translations by hand. Use the tool, which handles apostrophes correctly:

```
tools/slovnik.py system/jazyky/admin-en.php < radky.txt
```

Every line of the input has the form `Czech|translation`. The tool skips existing keys. After changing the dictionaries, run the tests – the completeness of the installer dictionaries and of the demo content is watched by `tools/testy.php`.

## Security

- **Database:** prepared statements only; `{tabulka}` adds the prefix.
- **Output:** everything through `e()`. The HTML of articles and blocks is trusted, because authors write it. Comments and other reader input never.
- **Administration:** every POST has a CSRF token; `Admin\Kernel` checks it.
- **Reader forms** have no session and no CSRF token. They are protected by `Core\Antispam`: a signed time, a hidden field and a limit per IP address fingerprint. The IP address is not stored, only its fingerprint.
- **Uploads:** images always through `Core\Obrazky` (re-encoding), attachments through `Core\Soubory` with an allowlist of extensions. HTML, SVG and scripts never.
- **Mail** always goes through `Core\Posta::odesli()`, never directly through the `mail()` function.
- **The site address** is taken from the settings through `$app->request->origin()`, never from the `Host` header.
- **The Claude connection** may change only content and custom templates. No tool may write outside `layout/<custom>/`, or run code or a query. Add a new function to the allowlist of `Core\SablonaKontrola` only if it does not work with files, the network, processes, callbacks or reflection.

### Content-Security-Policy in the administration

The administration sends the header `script-src 'self'`. The consequence for administration templates:

- no inline `<script>`,
- no `onclick=`, `onchange=` and similar attributes,
- behaviour belongs in `image/admin.js` and is bound through `data-…` attributes (for example `data-odeslat-pri-zmene`, `data-ukaz-heslo`).

A static check in `tools/testy.php` watches this. The same check verifies that no script looks for a `[data-…]` element that is not created anywhere.

### Never window.confirm

The built-in browsers of apps suppress the `window.confirm()` dialog, so a confirmation would silently not take place. In the administration give the form or the button the attribute `data-potvrdit="text of the question"` – `admin.js` handles it. The visual block editor has its own dialog.

## The site and templates

- A new variable for templates or a new system block must be reflected in all three built-in templates.
- A template must output `$hlava` before `</head>` and `$pata` before `</body>`.
- What is not output has no style and no script. A selector that is not created anywhere does not belong in `image/web.css` or in the templates' `style.css`.
- The shared look of elements is at the end of `image/web.css` in `:where()` with zero specificity. A template carries only what differs.
- No external fonts or CDN.
- Every query on the site that outputs content filters by language version.
- Anything that reads articles other than through `Front\Clanky` must respect the content lock by itself.
- Only output for visitors who are not signed in and have no personal cookies is cached. What is to differ by reader runs in JavaScript, or has its own `phprs_*` cookie that turns the cache off.

## The manual

When changing behaviour or a label in the administration, update the manual in `docs/prirucka/cs/` as well. Czech is the source; English and German are translations with the same file names. The order of pages and the translation of addresses are held by `docs/prirucka/osnova.json`; the table in `Core\Napoveda` must match it.

## Related

- [Project structure](struktura-projektu.md)
- [Tests and releases](testy-a-vydavani.md)
- [How to contribute](jak-prispet.md)
