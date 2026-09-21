# How to contribute

phpRS is free software and is developed publicly on GitHub in the repository `phprs-cms/cms`. You can contribute by reporting a bug, with a translation, a fix or a suggestion. This page says how to go about it so that your contribution is easy to accept.

## Reporting bugs

Report an ordinary bug in the Issues of the repository. A good report contains:

- the phpRS version – it is in the footer at the bottom of the administration and in **Settings → Backups and updates**,
- the PHP and database versions – **Settings → System status** shows them,
- the exact steps to reproduce the bug, what you expected and what happened,
- the wording of the error message; details tend to be in the file `storage/log/chyby.log`,
- for appearance bugs, the template used, the browser and the window width, possibly a screenshot.

Before you report a bug, check that you have the latest version and go through the page [Troubleshooting](../provoz/reseni-potizi.md). Remove passwords, tokens and readers' personal data from logs and screenshots.

## Security bugs

A security bug must **not be reported publicly** in Issues. Use private reporting on GitHub – the tab **Security → Report a vulnerability** in the repository – or the e-mail given on the project website. Describe the version, the steps and the impact.

What happens next:

1. We respond within 3 working days.
2. The fix is made in private. It usually comes out within 14 days, for critical bugs as soon as possible.
3. It comes out as a version marked as a security release. Installations look for news twice a day and – unless the administrator has turned this off – install such a version by themselves; the administrator receives an e-mail.
4. After the fix is released, we publish a security advisory with a description, the affected versions and thanks to the finder.

Only the latest released version is supported. The rules are also in the file `SECURITY.md`.

## Translations

The system is translated into Czech, Slovak, English and German. Czech is the source; the key of every translation is the Czech text.

| What | Where |
|---|---|
| Site texts and e-mails to readers | `system/jazyky/en.php`, `sk.php`, `de.php` |
| Administration | `system/jazyky/admin-en.php`, `admin-sk.php`, `admin-de.php` |
| The visual block editor and the article editor | `image/jazyky/admin-<code>.js` |
| Installer | `system/jazyky/install-<code>.php` |
| Demo content | `system/demo/` |
| Manual | `docs/prirucka/<language>/` – the files have the same, Czech names in all languages |

Send a translation fix as a pull request, or describe it in Issues: the original wording, the proposed wording and the place where you saw the text. Add missing translations with the tool `tools/slovnik.py`, not by editing a dictionary by hand – see [Project principles](zasady.md). The date format for a language is determined by the keys `datum_format` and `datum_slovy` in the site dictionary.

Adding another language is a bigger job: three dictionaries, the dictionary for JavaScript, the installer, the demo content and an entry in `Core\Jazyk`. Agree on it in advance in Issues.

## Pull requests

1. **For a larger change, agree on it first.** Open an Issue and describe the intent. The project keeps a narrow scope – a publishing system for magazines – and simplicity takes precedence over the number of features. You will save yourself work on something that would not be accepted.
2. Read [Project principles](zasady.md) and [Project structure](struktura-projektu.md).
3. Work in your own branch. One pull request deals with one thing.
4. Keep to the style of the surrounding code: `declare(strict_types=1)`, domain identifiers in Czech without diacritics, comments in Czech. A comment explains why the code does something – not what it does.
5. Before submitting, run `tools/test.sh`, or at least `php tools/testy.php`. With a bug fix add a test that would catch it next time, if it concerns logic without a database.
6. A database change needs a migration, an edit of `schema.sql` and raising `PHPRS_VERZE_DB`.
7. A new text for users needs a translation in all dictionaries.
8. If behaviour or a label in the administration changes, update the Czech manual in `docs/prirucka/cs/` as well.
9. In the description of the pull request state what you are changing and why, and how you tested it. For appearance changes attach screenshots in light and dark mode and at phone width.

Every pull request goes through the checks on GitHub: the smoke test on the supported PHP versions, Semgrep and Gitleaks. Details are on the page [Tests and releases](testy-a-vydavani.md).

### What is not accepted

- new dependencies, frameworks, build steps, external fonts and CDNs,
- third-party plug-ins and uploading code from the administration – extensions are a closed set,
- Claude connection tools that would reach beyond content and custom templates,
- features for a single site. A custom look belongs in a [custom template](../vzhled/vlastni-sablona.md), not in the core.

## Licence

phpRS is released under the **GNU General Public License version 2** or later. The text is in the file `LICENSE`. By submitting a contribution you agree to its publication under the same licence. Do not add code or images whose licence is not compatible with it. The administration font Noto Sans has its own OFL licence, enclosed in `image/pisma/OFL.txt`.

## Supporting the project

You can also support the development financially through GitHub Sponsors. The link **Support phpRS** is in the footer of the administration; the administrator can turn it off in **Settings → Backups and updates**.

## Related

- [Project principles](zasady.md)
- [Tests and releases](testy-a-vydavani.md)
- [Security](../provoz/bezpecnost.md)
- [Troubleshooting](../provoz/reseni-potizi.md)
