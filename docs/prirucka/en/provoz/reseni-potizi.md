# Troubleshooting

Always start in **Settings → System status**: it shows most causes directly, including the **Error log**. The same log is in the file `storage/log/chyby.log`.

## The site shows an error or a blank page

- Look in `storage/log/chyby.log` (over FTP, if the administration does not work).
- After a move to another hosting, the cause is most often a missing PHP extension, a PHP version older than 8.4 or wrong database details in `config.php`.
- A detailed error output right on the page is turned on by `'debug' => true` in `config.php`. **On a live site turn it off again straight away** – the output reveals paths and parts of the code.

## The home page works, articles return 404

Clean URLs are not working.
On Apache, the `.htaccess` file is missing from the site root (FTP clients often do not upload hidden files) or the hosting does not have `mod_rewrite` enabled. On nginx, the `try_files` rule is missing – see [Running on nginx](nginx.md).

## The “site is being updated” message does not go away

The message („Web se právě aktualizuje“) is shown while the files are being overwritten and disappears by itself within 10 minutes at the latest. If the update crashed, delete the file `storage/udrzba.lock` and repeat the update, or carry it out [manually](../zaciname/aktualizace.md).

## You cannot sign in

- **“Too many attempts”** – wait 15 minutes; the lock is temporary.
- **Lost phone with the authenticator app** – enter one of the backup codes instead of the code. If you do not have them, another administrator can turn off two-factor sign-in for you in Users.
- **Forgotten password** – another administrator sets a new password in Users.
- **The only administrator has no access** – only a change in the database helps: in the `rs_user` table (the prefix depends on your installation) put a new password hash into the `password` column and empty `totp_tajemstvi`. You create the hash on the command line:

  ```
  php -r 'echo password_hash("nove-dlouhe-heslo", PASSWORD_DEFAULT), "\n";'
  ```

## A scheduled article was not published on time

Without cron, background jobs run only when the site is visited. Set up [cron](ulohy-na-pozadi.md). If the time is off by whole hours, check the **time zone** in Settings → Basic.

## E-mails are not arriving

See [Mail → Most common problems](posta.md). In short: the **Recent messages** overview tells you whether the message was sent; if it was, look in spam and set up SPF and DKIM.

## An image cannot be uploaded

- **The file is too large** – the limit is set by PHP (`upload_max_filesize`, `post_max_size`); System status shows it in the *upload size limit* row. You raise it in the hosting control panel.
- **Write error** – the `media/` folder is not writable.
- **No thumbnails are created** – the PHP extension `gd` is missing.

## A change of appearance did not show up

The browser is holding an old version of the styles. Reload the page with the cache cleared (Ctrl/⌘+Shift+R). If the site is behind a CDN or a hosting cache, clear it there too. You clear the system's own cache by emptying the `storage/cache/` folder.

## No update is offered

The **Check now** button in Settings → Backups and updates asks the server immediately. The **Updates** row in System status shows a possible connection error – some hosting services block outgoing requests; in that case update manually.

## When none of this helps

Open an issue on the project's GitHub. Include the phpRS and PHP versions, the error text from the log and the steps to reproduce the problem. **Never attach `config.php`, a database backup or tokens.**
