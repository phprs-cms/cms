# Backups and restore

Backups are managed in **Settings → Backups and updates**. The system backs up the **database** – articles, pages, settings, users, readers, comments. **Uploaded images and attachments** are in the `media/` folder and are backed up separately.

## Database backups

- **Automatically once a week**, if the option **Automatic backup once a week** is on. The backup is created when an administrator signs in, or – if you have cron set up – when the [background jobs](ulohy-na-pozadi.md) run, as soon as the last backup is older than a week.
- **Before every update** of the system.
- **Manually** with the **Create backup now** button.

The files `phprs-YYYYMMDD-HHMMSS-….sql.gz` are stored in the `storage/zalohy/` folder, which is not accessible from the internet. The system keeps the **last 10 backups** and deletes older ones. It does not need `mysqldump` to create them, so it also works on shared hosting.

You can **download**, **restore** or delete each backup.

## Off-site backup copies

A backup on the same server as the site will not help if you lose the hosting, if someone compromises it or if the disk fails. In the **Database backups** section, therefore, expand **Off-site backup copies** and in the **Copy to** field choose where each new backup should upload itself:

- **an FTP server** – another hosting or a home NAS; fill in **Server**, **User name / access key**, **Password / secret key** and **Folder / bucket**,
- **S3 storage** – Amazon S3, Backblaze B2, Wasabi, Cloudflare R2; the endpoint goes into the **Server** field, then **User name / access key**, **Password / secret key**, **Folder / bucket** (the bucket with a folder) and **Region (S3 only)**.

Save the settings and click **Create backup now** – the copy uploads right away and you will see whether the connection works. The result of the last attempt is also shown in **System status**.

For backups we recommend a separate access in the storage that may **only write** to a single folder.

## Media backup

The **Download media backup (ZIP)** button packs the `media/` folder. On a site with many photos the file can be large and take a while to prepare; in that case it is more reliable to download the `media/` folder regularly over FTP or to back it up with the hosting tool.

## Restore

Click **Restore** next to a backup. A restore **overwrites the current contents of the database** with the state from the backup – everything added to the site afterwards disappears. The system saves the current state to a new backup first, so you can go back.

When the administration is not running, follow the chapter [Moving the site](../zaciname/presun-webu.md): import the backup into the database with the hosting tool.

## Recommended routine

1. Leave automatic backups on.
2. Set up off-site copies.
3. From time to time, try restoring a backup on a test installation – a backup nobody has tried to restore is only a hope.
