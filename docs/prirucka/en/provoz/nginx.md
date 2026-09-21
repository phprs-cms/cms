# Running on nginx

On Apache and LiteSpeed, the supplied `.htaccess` files take care of protecting sensitive folders, clean URLs and caching of static files. **Nginx does not read them.** Without rules of your own, files that must not be public could be downloaded from the site – `config.php` with the database password, the `storage/` folder with backups.

> A sample configuration is included in the package in the file `system/nginx.priklad.conf`. So far it **has not been verified on a running server** – before deploying it, run `nginx -t` and go through the check at the end of this page.

## What the configuration must ensure

1. **Deny access** to `config.php`, the folders `system/`, `storage/`, `tools/`, `docs/`, hidden files (`.git`, `.htaccess`) and `.php` files inside `layout/`.
2. **Nothing is executed in the `media/` folder.** The files `.php`, `.html`, `.svg` and `.js` are not served from there at all; documents are offered for download.
3. **Clean URLs:** whatever is not a file is handled by `index.php` (`try_files $uri $uri/ /index.php?$query_string;`).
4. **PHP runs from three files only:** `index.php`, `admin.php`, `install.php`. Other `.php` files return 404.
5. **The `Authorization` header** is passed to PHP – the Claude connection (MCP) and the API need it.
6. **WebP:** a browser that supports it is served `foto.jpg.webp` instead of `foto.jpg`, if it exists. This requires the `$webp_pripona` map in the `http` block.
7. **The upload limit** `client_max_body_size` aligned with `upload_max_filesize` in PHP.

## Check after deployment

These addresses must return **403 or 404**, never the contents of the file:

```
https://www.example.com/config.php
https://www.example.com/system/sql/schema.sql
https://www.example.com/storage/log/chyby.log
https://www.example.com/.htaccess
```

Then go through **Settings → System status** and try uploading an image, opening an article with a clean URL and signing in to the administration.
