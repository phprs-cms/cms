# Project structure

phpRS is written in plain PHP 8.4+ on top of MySQL or MariaDB. It has no framework, no Composer and no build step. Pages are assembled by the server; only a small amount of ordinary JavaScript runs in the browser. This page is a map for anyone who wants to read the code, debug it or contribute to it.

## Why without a framework, Composer and npm

It is intentional, not debt:

- **Installation over FTP.** The system is meant to run on ordinary shared hosting. You upload the files, open `install.php` and it is done. No command line is needed.
- **Readability.** The code should be readable even by an informed layperson who wants to adjust their site. No dependency container, ORM or generated code – what happens is visible in one file.
- **Security and maintenance.** No third-party library is used at runtime. There is nothing to watch for vulnerabilities in dependencies, and an update is a single signed package.
- **No build.** CSS and JavaScript are written the way they are sent to the browser.

A new dependency needs a strong reason. Things for which a library is used elsewhere (TOTP, WebAuthn, Web Push, the S3 signature, SMTP) are written here in a few hundred lines and covered by tests.

## Entry points

| File | What it is for |
|---|---|
| `index.php` | the public site; hands control to `Front\Kernel` |
| `admin.php` | the administration; sends security headers including Content-Security-Policy and hands control to `Admin\Kernel` |
| `install.php` | the installer; deletes itself after finishing (not in a development copy with a `.git` folder) |
| `sw.js` | the service worker for Web Push notifications |
| `system/bootstrap.php` | the constants `PHPRS_VERSION` and `PHPRS_VERZE_DB`, the PSR-4 autoloader for the `PhpRS\` namespace, helper functions |
| `system/dev-router.php` | the router for PHP's built-in server during development |

Pretty addresses on Apache are provided by `.htaccess`. For nginx there is a ready-made example in `system/nginx.priklad.conf` – see [nginx](../provoz/nginx.md).

## Folders

| Folder | Content |
|---|---|
| `system/src/Core/` | the core: `App`, `Db`, `Request`, `Response`, `Session`, `View`, `Auth`, `Settings` and services (mail, images, backups, updates, signatures, Web Push, AI assistant, languages, extensions…) |
| `system/src/Admin/` | the administration `Kernel`, the base class `Modul`, the user account, password recovery, the change log |
| `system/src/Admin/Moduly/` | administration modules – one module is one class |
| `system/src/Front/` | the public site: routing (`Kernel`), articles, blocks, SEO, readers, newsletter, advertising, cache, statistics, API |
| `system/src/Mcp/` | the MCP server for the Claude connection: `Server` and `Nastroje` |
| `system/src/Install/` | the installer |
| `system/views/admin/` | administration templates; a folder per module identifier |
| `system/views/front/` | the default site templates, which a site template may override |
| `system/views/install/` | installer templates |
| `system/jazyky/` | dictionaries: the site (`en.php`, `sk.php`, `de.php`), the administration (`admin-*.php`), the installer (`install-*.php`) |
| `system/sql/` | `schema.sql` for new installations and `migrace/NNNN-popis.sql` for existing ones |
| `system/demo/` | demo content in Czech, English and German |
| `layout/` | site templates; the built-in `classic-newspaper`, `modern-magazine`, `minimal` |
| `image/` | the CSS and JavaScript of the administration, the editor and the visual block editor, and the shared `web.css` and `web.js` for the site; the administration font in `image/pisma/` |
| `media/` | uploaded files, sorted into `YYYY/MM/` folders |
| `storage/` | cache, logs, backups and site exports (`zalohy/`), WordPress import files (`import/`); not accessible from the web |
| `tools/` | tests, the dictionary tool, the release script |
| `docs/` | this manual (`docs/prirucka/`) and the release procedure |

`config.php` with the database access is created by the installer. It does not belong in the repository; the model is `config.sample.php`.

## How a request flows

**Website.** `Front\Kernel` sets the site address and the time zone, runs pending migrations if any, recognises the language version from the address prefix (`/en/…`) and selects the site template. Then it tries the page cache, and if that fails, it calls a handler according to the path (article, section, page, feeds, reader account…). The result is wrapped by the template's `base.php`. A template file is looked for first in the folder of the site template, then in `system/views/front/`.

**Administration.** `Admin\Kernel` verifies the sign-in and, for every POST request, the CSRF token. By the `modul` parameter it finds the class in the `Kernel::MODULY` list, verifies the extension and the permissions and calls the method `akce<Name>()` – `?modul=clanky&akce=edit` leads to `Moduly\Clanky::akceEdit()`.

### A new administration module

1. A class in `system/src/Admin/Moduly/` extending `Modul`. The constants `IDENT`, `NAZEV`, `SKUPINA` (the group in the menu), `IKONA`; as needed `ROZSIRENI` (the extension key) and `JEN_ADMIN`.
2. The methods `akceVypis()`, `akceEdit()`, `akceUloz()`… return a `Response`.
3. Templates in `system/views/admin/<ident>/`.
4. An entry for the class in `Admin\Kernel::MODULY`.

## Naming

The domain of the system is Czech and the code reflects that:

- **Czech without diacritics:** tables and columns (`rs_clanky.titulek`, `rs_topic`), module methods (`akceUloz`), domain variables (`$clanek`, `$rubrika`), templates (`vypis.php`, `formular.php`), setting keys (`nazev_webu`).
- **Czech with diacritics:** comments, texts for users, commit messages.
- **English:** the core API in `Core/` (`Request::post()`, `Db::all()`, `Settings::get()`).

Tables have a prefix (`rs_` by default). In queries you write `{clanky}` and `Db` adds the prefix.

Texts for users are wrapped in the function `t('Česky')`. The dictionary key is the Czech text; whatever is missing from a dictionary is shown in Czech.

## Running during development

```
php -S localhost:8080 system/dev-router.php
```

You need PHP 8.4+ with the extensions `pdo_mysql`, `mbstring` and `gd` and a running MySQL or MariaDB. You trigger a clean installation by deleting `config.php`, dropping the `rs_*` tables and opening `/install.php`.

## Related

- [Project principles](zasady.md)
- [Tests and releases](testy-a-vydavani.md)
- [Custom template](../vzhled/vlastni-sablona.md)
- [Requirements](../zaciname/pozadavky.md)
