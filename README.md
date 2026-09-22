<p><picture><source media="(prefers-color-scheme: dark)" srcset="image/phprs-logo-tmavy.svg"><img src="image/phprs-logo.svg" alt="phpRS" height="48"></picture></p>

**English** · [Čeština](README.cs.md)

# phpRS 3

A publishing system for online magazines, newspapers and blogs, written from scratch for PHP 8.4+ and MySQL 8 / MariaDB 10.6+.
It follows in the footsteps of the Czech [phpRS](https://phprs.net/) (Jiří Lukáš, 2001–2007), whose development ended:
it keeps its simplicity, its focus on articles and sections, and its Czech naming in the database.

**It is not a new version of the old phpRS and you cannot upgrade to it.** Data from phpRS 2 is not converted, old addresses
are not redirected and no old feature is kept for the sake of compatibility.

What's inside: PDO with prepared statements everywhere, `password_hash`, CSRF protection, InnoDB with foreign keys, utf8mb4,
pretty URLs (`/clanek/headline`), a responsive administration and site, no global variables, no framework and no third-party libraries at runtime.

- Website and documentation: [phprs.eu](https://phprs.eu/en/) – manual in [English](https://phprs.eu/en/docs/), [Czech](https://phprs.eu/cs/dokumentace/) and [German](https://phprs.eu/de/dokumentation/)
- Demo: [demo.phprs.eu](https://demo.phprs.eu)
- Download: [Releases](https://github.com/phprs-cms/cms/releases)
- Wiki: [github.com/phprs-cms/cms/wiki](https://github.com/phprs-cms/cms/wiki)

## Installation

1. Upload the contents of the package to your hosting (FTP is enough – no Composer and no command line needed).
2. Create an empty database.
3. Open `https://your-site.com/install.php` and fill in the form. If you like, it also loads a sample magazine (Czech, English or German) that you can later delete with one click.

The installer deletes itself when it finishes. Step-by-step guide: [manual on phprs.eu](https://phprs.eu/en/docs/getting-started/installation/).

Apache uses the bundled `.htaccess` files. Nginx does not read them – use the ready-made sample `system/nginx.priklad.conf`
(access rules, media without script execution, WebP, pretty URLs) and after deployment check that `/config.php` and `/storage/log/chyby.log` return 403.

## Development

```bash
php -S localhost:8080 system/dev-router.php
```

## Structure

```
index.php, admin.php, install.php   entry points
config.php                          created by the installer
image/                              CSS, JS and logo of the administration
layout/<name>/                      site templates: base.php, blok.php, cla_*.php, style.css
media/YYYY/MM/                      uploaded images and attachments
storage/                            logs, cache, backups; not reachable from the web
system/src/Core/                    core: App, Db, Request, Response, Session, View, Auth, Settings
system/src/Admin/Moduly/            administration modules – one module = one class
system/src/Front/                   public part of the site
system/views/                       templates of the administration, the installer and the default site views
system/jazyky/                      dictionaries (the source language of the code is Czech; en, sk, de)
system/sql/schema.sql               database structure
```

A new administration module: a class in `system/src/Admin/Moduly/` extending `Modul` (constants `IDENT`,
`NAZEV`, methods `akceVypis()`, `akceEdit()`…), templates in `system/views/admin/<ident>/` and an entry in
`Kernel::MODULY`. Identifiers and comments in the code are in Czech; user-facing texts go through `t()` and are translated in the dictionaries.

### Site templates (layouts)

| folder | name | look |
| --- | --- | --- |
| `layout/classic-newspaper` | Classic Newspaper | a serious daily – serif headlines, thin rules, a lead story, a right-hand column |
| `layout/modern-magazine` | Modern Magazine | a bold magazine – black bar, hero article, grid of cards, a strip of blocks at the bottom |
| `layout/minimal` | Minimal | a personal magazine, blog or newsletter site – one narrow column, calm typography, a clean list of articles |

A layout is `base.php` (the page), `blok.php` (one block), `cla_*.php` (article templates with the modes
preview / short / full; `$poradi === 0` is the first article on the front page), `style.css` and `info.php`
(name and description). It is chosen during installation and in Appearance → Site identity. A custom layout: copy one of the folders under
a new name and in `base.php` point the link to `style.css` at the new folder. The base look of shared elements (comments, poll,
rating, block types…) lives in `image/web.css` with zero specificity – a template's `style.css` carries only what differs. A layout can also override any template from `system/views/front/` (listing, system blocks, RSS).
Layouts use no external fonts or scripts (GDPR, speed). More in the manual: [Custom template](https://phprs.eu/en/docs/appearance/custom-template/).

## Maintenance and security (for the publisher)

- **No third-party libraries at runtime** – there are no dependency vulnerabilities to track; Dependabot watches only GitHub Actions.
- **Every change:** `.github/workflows/kontrola.yml` – the smoke test `tools/test.sh` (clean installation + a walk through the site
  and the administration) on PHP 8.4 and 8.5, Semgrep (security rules), Gitleaks (keys and passwords in the repository).
  It also runs every Monday without changes. Locally: `tools/test.sh` (needs MySQL; it drops and recreates the database `phprs3_test`).
- **Every day:** `.github/workflows/denni-kontrola.yml` verifies the signature of the update channel, runs the tests on the development version of PHP too,
  Semgrep, Gitleaks and the security headers of the website and the demo; on failure it opens an issue.
- **Release:** bump `PHPRS_VERSION`, commit, tag. The package and `aktualizace.json` are built and **signed only locally**
  (`php tools/vydani.php`, the private key never leaves the publisher's computer); after the tag is pushed, CI only creates a draft release.
  Installations fetch updates from `https://phprs.eu/aktualizace.json` and verify the signature against `system/aktualizace.pub`.
  The whole procedure including key rotation: `docs/VYDAVANI.md`.
- **Security fix:** `php tools/vydani.php … --bezpecnostni`. Installations check for news twice a day,
  install a security release by themselves (can be switched off) and the administrator gets an e-mail. How to report a vulnerability: [`SECURITY.md`](SECURITY.md).

## Licence

GNU GPL version 2 or later – the same as the original phpRS. The licence text is in the file `LICENSE`.

phpRS is free and has no paid edition. If it is useful to you, you can support its development through [GitHub Sponsors](https://github.com/sponsors/phprscms).
