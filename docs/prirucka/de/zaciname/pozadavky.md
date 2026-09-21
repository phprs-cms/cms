# Anforderungen an das Hosting

phpRS läuft auf gewöhnlichem Shared Hosting. Es braucht weder SSH-Zugang noch Composer, Node.js oder einen Build-Schritt – Sie laden die Dateien hoch und öffnen das Installationsprogramm.

## Was das Hosting können muss

| Was | Anforderung |
| --- | --- |
| PHP | **8.4 oder neuer** |
| Datenbank | **MySQL 8** oder **MariaDB 10.6** und neuer |
| PHP-Erweiterungen – erforderlich | `pdo_mysql`, `mbstring`; für die Arbeit mit Bildern `gd` (die Installation läuft auch ohne sie durch, es entstehen aber keine Vorschaubilder und der Systemstatus meldet das als Fehler) |
| PHP-Erweiterungen – empfohlen | `exif` (richtige Drehung von Handyfotos), `intl` (Sortierung nach den Regeln der Sprache), `curl` (Benachrichtigung der Suchmaschinen über neue Inhalte), `zip` und `sodium` (Aktualisierung mit einem Klick), `zlib` (komprimierte Sicherungen) |
| Webserver | Apache mit aktiviertem `.htaccess` oder nginx (siehe [Betrieb → nginx](../provoz/nginx.md)) |
| HTTPS | dringend empfohlen; ohne HTTPS funktionieren weder Web Push noch die Verbindung mit Claude, und die Anmeldedaten werden unverschlüsselt übertragen |
| Speicherplatz | das System selbst belegt weniger als 3 MB; rechnen Sie vor allem mit den Fotos |

Ohne die empfohlenen Erweiterungen lässt sich das System installieren, nur stehen einige Funktionen nicht zur Verfügung. Was genau fehlt, zeigt nach der Installation **Einstellungen → Systemstatus**.

## Was Sie vor der Installation vorbereiten

1. **Eine leere Datenbank** und dazu den Namen des Servers, den Datenbanknamen, den Benutzer und das Passwort. Sie wird in der Verwaltung des Hostings angelegt.
2. **Einen Zugang zum Hochladen der Dateien** – FTP, SFTP oder den Dateimanager des Hostings.
3. **Eine Domain oder Subdomain**, unter der die Website laufen wird, am besten schon mit einem Zertifikat für HTTPS.
4. **Ein E-Mail-Postfach der Redaktion**, idealerweise auf derselben Domain. Von dort verschickt die Website E-Mails, und dorthin gehen die Hinweise des Systems.

## PHP-Limits, die einen Blick wert sind

- `upload_max_filesize` und `post_max_size` – die Standardwerte 2 MB und 8 MB sind für heutige Fotos zu wenig. Wir empfehlen mindestens **16 MB** und **32 MB**.
- `memory_limit` – für das Verkleinern großer Fotos mindestens **256 MB**.
- `max_execution_time` – die voreingestellten 30 Sekunden genügen; mehr Zeit hilft nur, wenn der Assistent lange Artikel übersetzt.

Bei den meisten Hostern ändern Sie die Limits in der Verwaltung des Hostings, in einem Bereich mit einem Namen wie „PHP-Einstellungen“.
