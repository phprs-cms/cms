# Hosting requirements

phpRS runs on ordinary shared hosting. It needs no SSH access, Composer, Node.js or build step – you upload the files and open the installer.

## What the hosting must provide

| What | Requirement |
| --- | --- |
| PHP | **8.4 or newer** |
| Database | **MySQL 8** or **MariaDB 10.6** and newer |
| PHP extensions – required | `pdo_mysql`, `mbstring`; for working with images `gd` (the installation completes without it, but no thumbnails are created and System status reports it as an error) |
| PHP extensions – recommended | `exif` (correct rotation of photos from phones), `intl` (language-aware sorting), `curl` (notifying search engines about new content), `zip` and `sodium` (one-click updates), `zlib` (compressed backups) |
| Web server | Apache with `.htaccess` enabled, or nginx (see [Operations → nginx](../provoz/nginx.md)) |
| HTTPS | strongly recommended; without it Web Push and the Claude connection do not work, and sign-in details travel unencrypted |
| Disk space | the system itself takes under 3 MB; plan mainly for photos |

Without the recommended extensions the system installs, but some features will not be available. After installation, **Settings → System status** shows exactly what is missing.

## What to prepare before installation

1. **An empty database** with its server name, database name, user and password. You create it in the hosting control panel.
2. **Access for uploading files** – FTP, SFTP or the hosting file manager.
3. **A domain or subdomain** where the site will run, preferably with an HTTPS certificate already in place.
4. **A newsroom mailbox**, ideally on the same domain. The site will send e-mails from it and notifications will arrive there.

## PHP limits worth checking

- `upload_max_filesize` and `post_max_size` – the defaults of 2 MB and 8 MB are too low for today's photos. We recommend at least **16 MB** and **32 MB**.
- `memory_limit` – at least **256 MB** for resizing large photos.
- `max_execution_time` – the default 30 seconds is enough; a longer time only helps when the assistant translates long articles.

On most hosting services you change the limits in the hosting control panel, in a section called something like “PHP settings”.
