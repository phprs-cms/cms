# Tests and releases

The project has two sets of tests, continuous checking on GitHub and signed releases. This page says what to run before submitting a change and how a finished change reaches users.

## Unit tests

```
php tools/testy.php
```

The tests run without a framework and without a database, in an instant. They watch the logic that a walk through the site does not detect: cryptography, parsing and text conversions. They include, for example:

- text conversions, search, splitting SQL migrations into statements,
- TOTP, passkeys (WebAuthn) against a software authenticator, the Web Push signature, the signature for backups to S3,
- the permitted PHP syntax in templates (`Core\SablonaKontrola`), including the fact that all built-in templates pass it,
- translation of an article by the assistant: the HTML skeleton stays from the original,
- verification of release signatures: several keys, replacing and revoking a key,
- the completeness of the installer dictionaries and of the demo content in all languages,
- the match between the address table in `Core\Napoveda` and `docs/prirucka/osnova.json`,
- static checks: the administration contains no inline scripts or event handlers; no script looks for a `[data-…]` element that is not created anywhere.

A new test is another call of `over('popis', $skutecne, $ocekavane)`. With every change in cryptography and parsing add a test including a negative case – an input that must not pass.

## Smoke test

```
tools/test.sh
```

It needs a running MySQL or MariaDB and the `mysql` and `curl` commands. The test procedure:

1. checks the syntax of all PHP files,
2. verifies that `PHPRS_VERZE_DB` matches the number of the last migration,
3. runs the unit tests,
4. copies the project into a temporary folder, creates a clean database and performs an installation; verifies that the installer has deleted itself afterwards,
5. walks through the site: the home page, an article, a section, the search, feeds, sitemaps, `robots.txt`, `llms.txt`, the clean version of an article, the 404 page and the fact that `system/` and `config.php` are not accessible from the web,
6. tries all three built-in templates and the fallback template when a folder is missing,
7. signs in to the administration and walks through its screens, including permissions by section and editing an article right on the site,
8. walks through the import of a sample WordPress export and the site export,
9. walks through Stripe payments without a network: webhooks signed on the spot and a fake server instead of the API (port in the `STRIPE_PORT` variable, default 8098).

A response is also considered faulty when it contains the text `Warning:`, `Notice:`, `Deprecated:` or `Fatal error`.

The database connection is taken from environment variables:

| Variable | Default |
|---|---|
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `phprs3_test` |
| `DB_USER` | `root` |
| `DB_PASS` | empty |
| `PORT` | `8099` – the port of the temporary web server |

> The test **drops and recreates** the `DB_NAME` database. Never enter a database you care about.

After a change to templates, editor dialogs or styles the tests are not enough. Go through the affected pages in a browser – in light and dark mode and at phone width.

## Checks on GitHub

The **Kontrola** workflow runs on every change and once a week even without changes:

- the smoke test on PHP 8.4 and 8.5,
- Semgrep with security rules for PHP and JavaScript,
- Gitleaks – there must be no keys or passwords in the repository,
- a check that the publisher's private key is not in git.

Dependabot watches only GitHub Actions. The project has no other dependencies.

## Daily security check

The **Denní kontrola** workflow runs every night. It releases and signs nothing; it only warns in time that action is needed:

| Check | What it reveals |
|---|---|
| The update channel | The update manifest on the project website is not signed with the publisher's key, the package does not match the hash or carries a foreign public key – that is, forgery of or damage to what installations download. |
| Tests on the supported PHP versions and on the upcoming version | A change in PHP that breaks the system, before it reaches hosting providers. The upcoming version is allowed to fail. |
| Static analysis with rules refreshed daily | Newly described vulnerable patterns in the code. Only the repository maintainers see the findings. |
| The project website and the demo from outside | Missing security headers, an accessible `config.php`, `system/`, `storage/` or `.git/`. |

When something fails, an issue is created in the repository with a link to the run of the check.

## How a release is made

The version number is the constant `PHPRS_VERSION` in `system/bootstrap.php`. A release is built by the script `tools/vydani.php`. It creates a ZIP package and an update manifest that carries the version number, the hash of the package, the description of changes and a flag saying whether it is a regular or a security release.

What is signed:

- a string composed of the version, the SHA-256 hash of the package and the kind of release. The security release flag is therefore covered by the signature – someone who controlled only the website with the manifest cannot declare a regular release a security one and force its automatic installation,
- the list of core files `system/soubory.json`. By it **System status** reports changed, missing and added files, and an update cleans up files that the new release no longer contains.

The signatures are Ed25519 and only `Core\Podpis` verifies them. The public keys are in `system/aktualizace.pub`, one per line. A signature by any of them is valid; thanks to this the operational key can be replaced and there is a backup key in case it is lost. The file is part of the package, so new keys reach installations through an update and revoked ones disappear from them.

Two rules that do not change:

- **Signing is done locally, not in CI.** In CI anyone who may change a workflow could sign with the key. After a version is marked with a tag, the **Vydání** workflow only verifies that the version in the code matches the tag, runs the tests and creates a draft release.
- **Private keys never belong in git or in the package.** Both `.gitignore` and the check on GitHub watch this.

The security release flag is reserved for real security fixes. With the default settings installations install such releases by themselves and the administrator receives an e-mail. What an update looks like from the point of view of the site administrator is described on the page [Updates](../zaciname/aktualizace.md).

The complete procedure for the publisher – creating and replacing keys, what to do when a key is lost or leaked, releasing a patch – is in `docs/VYDAVANI.md`. After the release of version 3.0.0, fixes are made on the main branch and carried over to the branch of the maintained series, from which the 3.0.x versions come; new features go only into the main branch.

## Related

- [Project principles](zasady.md)
- [How to contribute](jak-prispet.md)
- [Updates](../zaciname/aktualizace.md)
- [System status](../provoz/stav-systemu.md)
