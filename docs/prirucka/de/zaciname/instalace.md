# Installation

Die Installation dauert einige Minuten und besteht aus vier Schritten auf einem Bildschirm.

## 1. Dateien hochladen

1. Laden Sie die aktuelle Version herunter – die Datei `phprs-X.Y.Z.zip`.
2. Entpacken Sie sie und laden Sie den **Inhalt** in den Ordner der Website auf dem Hosting. Im Zielordner müssen direkt `index.php`, `admin.php`, `install.php` und die Ordner `system/`, `layout/`, `image/`, `media/`, `storage/` liegen – nicht ein weiterer verschachtelter Ordner.
3. Prüfen Sie, dass auch die **versteckten Dateien `.htaccess`** hochgeladen wurden: Eine liegt im Stammverzeichnis, weitere in den Ordnern `system/`, `storage/` und `media/`. Manche FTP-Programme zeigen versteckte Dateien in der Standardeinstellung nicht an und übertragen sie nicht. Ohne sie wären Dateien aus dem Internet lesbar, die nicht öffentlich sein sollen.

Auf einem Server mit nginx gelten die Dateien `.htaccess` nicht – richten Sie die Regeln nach dem Kapitel [Betrieb → nginx](../provoz/nginx.md) ein.

## 2. Installationsprogramm öffnen

Öffnen Sie im Browser die Adresse `https://www.example.de/install.php`. Das Installationsprogramm erscheint in der Sprache Ihres Browsers; oben rechts stellen Sie es auf Tschechisch, Slowakisch, Englisch oder Deutsch um.
Die gewählte Sprache wird zugleich als Sprache der Website, als Sprache der Administration für Ihr Konto und als Sprache der Beispielinhalte eingestellt.

### Schritt 1 – Serverprüfung

Das Installationsprogramm prüft die PHP-Version, die erforderlichen Erweiterungen und den Schreibzugriff auf das Stammverzeichnis und auf `storage/`. Mit einem Kreuz markierte Punkte müssen Sie beim Hosting beheben; laden Sie danach die Seite neu.

### Schritt 2 – Datenbank

Tragen Sie die Daten der leeren Datenbank ein. Der **Server** heißt meist `localhost`, viele Hoster verwenden aber eine eigene Adresse – Sie finden sie in der Verwaltung des Hostings bei der Datenbank.
Das **Tabellenpräfix** (`rs_`) ändern Sie nur dann, wenn in derselben Datenbank mehrere Installationen laufen sollen.

### Schritt 3 – Website und Administrator

- **Name der Website** – erscheint in der Kopfzeile und in Suchmaschinen; später ändern Sie ihn in den Einstellungen.
- **Benutzername** und **Passwort** des ersten Kontos. Das Passwort muss mindestens 10 Zeichen lang sein; wählen Sie ein langes und einmaliges, denn dieses Konto darf auf der Website alles.
- **Vor- und Nachname** – wird bei Artikeln angezeigt.
- **E-Mail** – wird zur E-Mail-Adresse der Redaktion: Dorthin gehen die Hinweise des Systems.
- **Zeitzone** – danach werden geplante Artikel veröffentlicht und Datumsangaben angezeigt.
- **Beispielinhalte laden** – optional. Statt eines einzelnen Begrüßungsartikels erhält die Website fünf Ressorts, zehn Artikel und Bilder einer erfundenen Tageszeitung in der Sprache der Installation (eine slowakische Installation erhält Tschechisch) – so sehen Sie sofort, wie die Vorlage mit Inhalt aussieht. Alles ist frei erfunden und frei verwendbar. Später löschen Sie die Beispiele mit einem Klick unter **Einstellungen → Allgemein → Beispielinhalte**; dort lassen sie sich auch nachträglich laden.

### Schritt 4 – Vorlage der Website

Wählen Sie eine der drei eingebauten Vorlagen: Classic Newspaper (Tageszeitung), Modern Magazine (auffälliges Magazin) oder Minimal (Blog, persönliches Magazin). Sie können sie jederzeit später unter **Design → Website-Identität** ändern, die Inhalte bleiben erhalten.

Die Schaltfläche **phpRS 3 installieren** legt die Tabellen in der Datenbank und die Datei `config.php` mit dem Zugang zur Datenbank an.

## 3. Nach der Installation

1. Das Installationsprogramm **löscht sich nach Abschluss selbst**. Erlauben das die Rechte auf dem Server nicht, weist es darauf hin – löschen Sie die Datei `install.php` dann von Hand; solange sie dort liegt, erinnert der Systemstatus daran.
2. Melden Sie sich unter der Adresse `https://www.example.de/admin.php` an.
3. Schalten Sie die **Zwei-Faktor-Anmeldung** ein: Klicken Sie oben rechts auf Ihren Avatar → **Mein Konto**.
4. Fahren Sie mit dem Kapitel [Erste Schritte](prvni-kroky.md) fort.

## Wenn die Installation nicht gelingt

- **„Verbindung zur Datenbank fehlgeschlagen“** – meist eine falsche Serveradresse oder ein falsches Passwort. Kopieren Sie die Daten aus der Verwaltung des Hostings, tippen Sie sie nicht ab.
- **„In der Datenbank existieren bereits Tabellen mit diesem Präfix“** – die Datenbank ist nicht leer. Wählen Sie ein anderes Präfix oder entfernen Sie die alten Tabellen.
- **„Die Tabellen wurden angelegt, aber config.php konnte nicht geschrieben werden“** – das Stammverzeichnis der Website ist nicht beschreibbar. Passen Sie die Rechte an und starten Sie die Installation erneut mit einem anderen Tabellenpräfix, oder löschen Sie vorher die Tabellen.
- **Weiße Seite oder Fehler 500** – das Hosting läuft vermutlich mit einer älteren PHP-Version. Stellen Sie auf 8.4 oder neuer um.
