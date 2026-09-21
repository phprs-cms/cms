# Moving the site to another hosting or domain

A site consists of three things: **files**, the **database** and the **address setting**. Depending on the number of photos, a move takes from minutes to tens of minutes.

## Procedure

1. **Database backup.** **Settings → Backups and updates → Create backup now** and download the file. In the same place you also download the **media backup (ZIP)**.
2. **Files.** Copy the whole site folder to the new hosting, including `config.php`, the folders `media/` and `storage/` and all hidden `.htaccess` files.
3. **Database.** Create an empty database on the new hosting and import the backup into it – through phpMyAdmin (it can load a `.sql.gz` file directly) or with the hosting tool.
4. **`config.php`.** Rewrite the database details in it to match the new hosting: `host`, `port`, `name`, `user`, `password`. Do not change the table prefix.
5. **Site address.** If the domain changes, sign in to the administration and enter the new address including `https://` in **Settings → Basic → Site address**. Links in e-mails, RSS, the sitemap and notifications are built from this setting – not from the address in the browser.
6. **Cache.** Delete the contents of the `storage/cache/` folder; it will be created again.
7. **Check.** Go through **Settings → System status** – it shows missing PHP extensions, write permissions and the HTTPS status in the new location.

## What not to forget

- **Redirect from the old domain.** Leave a 301 redirect to the new domain on the old hosting so that you do not lose links and search engine rankings.
- **Mail.** If the mailbox changes as well, update **Settings → Mail** and send yourself a test message.
- **Cron.** If you have a call to the jobs address set up, move it to the new hosting with the new address (see [Background jobs](../provoz/ulohy-na-pozadi.md)).
- **Web Push.** Notification subscriptions are tied to the domain; after a domain change readers have to turn them on again.
- **Time zone.** It is stored in the site settings and moves with the database; the time zone of the new server does not matter.

## Restoring from a backup

The same procedure also works after a failure. When the administration is running, you can restore a backup directly in it: **Settings → Backups and updates**, then **Restore** next to the chosen backup. A restore overwrites the current contents of the database; the system saves the state before the restore to a new backup by itself.
