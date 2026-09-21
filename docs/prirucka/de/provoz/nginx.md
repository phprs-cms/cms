# Betrieb mit nginx

Auf Apache und LiteSpeed kümmern sich die mitgelieferten Dateien `.htaccess` um den Schutz sensibler Ordner, um sprechende Adressen und um den Cache statischer Dateien. **Nginx liest sie nicht.** Ohne eigene Regeln ließen sich von der Website Dateien herunterladen, die nicht öffentlich sein sollen – `config.php` mit dem Passwort zur Datenbank, der Ordner `storage/` mit den Sicherungen.

> Eine Beispielkonfiguration liegt im Paket in der Datei `system/nginx.priklad.conf`. Sie wurde bisher **nicht auf einem laufenden Server überprüft** – führen Sie vor dem Einsatz `nginx -t` aus und gehen Sie die Kontrolle am Ende dieser Seite durch.

## Was die Konfiguration sicherstellen muss

1. **Kein Zugriff** auf `config.php`, auf die Ordner `system/`, `storage/`, `tools/`, `docs/`, auf versteckte Dateien (`.git`, `.htaccess`) und auf `.php`-Dateien innerhalb von `layout/`.
2. **Im Ordner `media/` wird nichts ausgeführt.** Dateien `.php`, `.html`, `.svg` und `.js` werden von dort gar nicht ausgeliefert, Dokumente werden zum Herunterladen angeboten.
3. **Sprechende Adressen:** Was keine Datei ist, bedient `index.php` (`try_files $uri $uri/ /index.php?$query_string;`).
4. **PHP wird nur aus drei Dateien ausgeführt:** `index.php`, `admin.php`, `install.php`. Alle anderen `.php` liefern 404.
5. **Der Header `Authorization`** wird an PHP weitergegeben – ihn brauchen die Verbindung mit Claude (MCP) und die API.
6. **WebP:** Ein Browser, der das Format unterstützt, erhält statt `foto.jpg` die Datei `foto.jpg.webp`, sofern sie existiert. Das erfordert die Map `$webp_pripona` im Block `http`.
7. **Das Upload-Limit** `client_max_body_size` ist auf `upload_max_filesize` in PHP abgestimmt.

## Kontrolle nach dem Einsatz

Diese Adressen müssen **403 oder 404** liefern, niemals den Inhalt der Datei:

```
https://www.example.de/config.php
https://www.example.de/system/sql/schema.sql
https://www.example.de/storage/log/chyby.log
https://www.example.de/.htaccess
```

Gehen Sie danach **Einstellungen → Systemstatus** durch und versuchen Sie, ein Bild hochzuladen, einen Artikel mit sprechender Adresse zu öffnen und sich in der Administration anzumelden.
