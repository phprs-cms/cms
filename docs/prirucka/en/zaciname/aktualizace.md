# Updates

phpRS can update itself. Every package is signed by the publisher and the system installs only a package whose signature matches – it rejects a forged or damaged one.

## How it works

- Twice a day the system asks at `https://phprs.eu/aktualizace.json` whether a new version has been released. It sends nothing but its own version number in the request header.
- You install a **regular version** yourself with a single button.
- A **security release** is installed **automatically** by default: the system first backs up the database, verifies the checksum and signature of the package, overwrites the files and sends an e-mail to the newsroom. If you do not want this, turn off the option **Install security updates automatically** – you will then only receive a notification e-mail.

## One-button update

**Settings → Backups and updates.** When a new version is available, you see its number, the list of changes and a button to install it. After you confirm:

1. a database backup is created,
2. the package is downloaded and its checksum and signature are verified,
3. for a few seconds the site responds with a maintenance message,
4. the system files are overwritten, files the new release no longer contains are removed, and the database is adjusted to the new structure on the first request.

The update **does not overwrite** `config.php`, the folders `media/` and `storage/`, or your own templates in the `layout/` folder. The built-in templates (`default`, `classic-newspaper`, `modern-magazine`, `minimal`) are overwritten – so do not edit them; make your own design as a copy under a different name.

The **Check now** button asks for a new version immediately.

### What the update needs

The PHP extensions `zip` and `sodium` and write access to the site folder. If any of these is missing, the system says so and offers the manual procedure.

## Manual update

This always works, even when the automatic update does not:

1. In **Settings → Backups and updates** click **Create backup now** and download the backup.
2. Download the new release and unpack it.
3. Over FTP, **overwrite all files except** `config.php` and the folders `media/` and `storage/`. Do not upload the `install.php` file from the package to the server.
4. Open the site or the administration – the database adjusts itself.

## Integrity check

Every release carries a signed list of core files with their checksums. Based on it, **Settings → System status** reports changed, missing and added files in the **Core files** row.
A report after your own change to the core is fine only if you know about that change; otherwise it is a sign that the site has been compromised – see [Operations → Security](../provoz/bezpecnost.md).
