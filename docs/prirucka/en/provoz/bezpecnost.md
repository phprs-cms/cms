# Security

phpRS is configured securely by default. This page sums up what to do after installation, what the system watches by itself and how to proceed if you suspect the site has been compromised.

## Five things after installation

1. **Delete `install.php`.** As long as it is on the server, System status warns about it.
2. **Turn on HTTPS** and the redirect from `http://`. Every hosting service now offers a certificate (Let's Encrypt) free of charge. Over HTTPS the system also sends the HSTS header.
3. **Turn on two-factor sign-in** for all administrators: avatar in the top right corner → **My account**. Any authenticator app will do (Google Authenticator, 1Password, Aegis…). The eight **backup codes** are shown only once – store them somewhere other than your phone.
4. **Set up off-site backups** – see [Backups and restore](zalohy.md).
5. **Leave automatic security updates on** – see [Updates](../zaciname/aktualizace.md).

## Accounts and permissions

- Every person has **their own account**. Shared accounts make it impossible to find out who changed what.
- Give the **least permissions needed**: an editor gets only the modules they use, or only their sections. Keep the number of administrators as low as possible.
- When a colleague leaves, **block** their account (Users) – their articles stay signed.
- A password has at least 10 characters. After a password change, the account is signed out on all other devices.

## What the system watches by itself

- **Password guessing:** after 10 failed attempts the account is locked for 15 minutes; the same limit applies per IP address and to wrong two-factor codes. The lock is temporary on purpose – otherwise anyone could put the newsroom out of action.
- **Reader sign-in** has the same protection.
- **The administration** sends a strict Content Security Policy (no third-party or inline scripts) and forbids caching.
- **Uploaded files** in the `media/` folder are never executed; only safe types are allowed.
- **Updates** are installed only with a valid publisher signature.
- **Core integrity:** System status compares the files with the signed list of the release and reports changed, missing and added files.
- **The Change log** in the administration records sign-ins and important changes.
- **Comments and forms** are protected by antispam without third-party services and without tracking readers.

## Claude connection and API

Create tokens for the connection (MCP) and the API **separately for each purpose** and delete those you do not use. The connection may only work with content and custom templates – it cannot reach the server settings, users or system files. Custom templates go through a check that does not allow working with files, the network or running processes.

## Suspected compromise

1. In **System status** check the **Core files** row, and check the **Change log** in the administration.
2. Change the passwords of all administrators, the database password and the FTP password. Create new tokens for cron, monitoring, the API and the connection.
3. Overwrite the system files with a clean package of the same version (the manual update procedure). Delete unknown files – especially in `media/` and `layout/`.
4. If you are not sure about the extent, restore the database from a backup created before the compromise.

## Reporting a security vulnerability

Please **do not report security vulnerabilities publicly**. Use private reporting on GitHub (*Security → Report a vulnerability*) or the e-mail address given on the project website. State the version and the steps to reproduce the problem. The fix will be released as a security release, which installs itself on sites with automatic updates turned on.
