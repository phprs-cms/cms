# Problemlösung

Beginnen Sie immer unter **Einstellungen → Systemstatus**: Die meisten Ursachen zeigt er direkt, einschließlich des **Fehlerprotokolls**. Dasselbe Protokoll steht in der Datei `storage/log/chyby.log`.

## Die Website zeigt einen Fehler oder eine weiße Seite

- Sehen Sie in `storage/log/chyby.log` nach (per FTP, wenn die Administration nicht erreichbar ist).
- Nach einem Umzug auf ein anderes Hosting liegt es meist an einer fehlenden PHP-Erweiterung, einer älteren PHP-Version als 8.4 oder falschen Datenbankdaten in `config.php`.
- Eine ausführliche Fehlerausgabe direkt auf der Seite schaltet `'debug' => true` in `config.php` ein. **Schalten Sie sie auf einer Live-Website sofort wieder aus** – die Ausgabe verrät Pfade und Teile des Codes.

## Die Startseite funktioniert, Artikel liefern 404

Die sprechenden Adressen funktionieren nicht.
Auf Apache fehlt im Stammverzeichnis der Website die Datei `.htaccess` (FTP-Programme laden versteckte Dateien oft nicht hoch) oder das Hosting hat `mod_rewrite` nicht aktiviert. Auf nginx fehlt die Regel `try_files` – siehe [Betrieb mit nginx](nginx.md).

## „Die Website wird gerade aktualisiert“ verschwindet nicht

Der Hinweis erscheint während des Überschreibens der Dateien und verschwindet spätestens nach 10 Minuten von selbst. Wenn die Aktualisierung abgebrochen ist, löschen Sie die Datei `storage/udrzba.lock` und wiederholen Sie die Aktualisierung, oder führen Sie sie [manuell](../zaciname/aktualizace.md) durch.

## Die Anmeldung gelingt nicht

- **„Zu viele Versuche“** – warten Sie 15 Minuten; die Sperre ist vorübergehend.
- **Telefon mit der Authentifizierungs-App verloren** – geben Sie statt des Codes einen der Ersatzcodes ein. Wenn Sie keine haben, schaltet ein anderer Administrator Ihre Zwei-Faktor-Anmeldung unter Benutzer aus.
- **Passwort vergessen** – ein neues Passwort legt ein anderer Administrator unter Benutzer fest.
- **Einziger Administrator ohne Zugang** – hier hilft nur ein Eingriff in die Datenbank: Tragen Sie in der Tabelle `rs_user` (Präfix je nach Ihrer Installation) in die Spalte `password` einen neuen Passwort-Hash ein und leeren Sie `totp_tajemstvi`. Den Hash erzeugen Sie auf der Kommandozeile:

  ```
  php -r 'echo password_hash("nove-dlouhe-heslo", PASSWORD_DEFAULT), "\n";'
  ```

## Ein geplanter Artikel ist nicht rechtzeitig erschienen

Ohne Cron werden die Hintergrundaufgaben erst bei einem Besuch der Website ausgeführt. Richten Sie den [Cron](ulohy-na-pozadi.md) ein. Wenn die Zeit um ganze Stunden abweicht, prüfen Sie die **Zeitzone** unter Einstellungen → Allgemein.

## E-Mails kommen nicht an

Siehe [E-Mail → Häufigste Probleme](posta.md). Kurz gesagt: Die Übersicht **Letzte Nachrichten** zeigt, ob die Nachricht hinausgegangen ist; wenn ja, suchen Sie im Spam und richten Sie SPF und DKIM ein.

## Ein Bild lässt sich nicht hochladen

- **Die Datei ist zu groß** – das Limit bestimmt PHP (`upload_max_filesize`, `post_max_size`); der Systemstatus zeigt es in der Zeile *Limit für hochgeladene Dateien*. Sie erhöhen es in der Verwaltung des Hostings.
- **Schreibfehler** – der Ordner `media/` hat keinen Schreibzugriff.
- **Es entstehen keine Vorschaubilder** – die PHP-Erweiterung `gd` fehlt.

## Eine Änderung am Design ist nicht sichtbar

Der Browser hält eine alte Version der Stylesheets. Laden Sie die Seite mit geleertem Cache neu (Strg/⌘+Umschalt+R). Wenn die Website hinter einem CDN oder einem Cache des Hostings steht, leeren Sie ihn auch dort. Den Cache des Systems selbst löschen Sie, indem Sie den Ordner `storage/cache/` leeren.

## Es wird keine Aktualisierung angeboten

Die Schaltfläche **Jetzt prüfen** unter Einstellungen → Sicherungen und Aktualisierungen fragt den Server sofort. Die Zeile **Aktualisierungen** im Systemstatus zeigt einen möglichen Verbindungsfehler – manche Hoster blockieren ausgehende Anfragen; aktualisieren Sie dann manuell.

## Wenn nichts davon hilft

Legen Sie eine Meldung auf dem GitHub des Projekts an. Fügen Sie die Version von phpRS und PHP, den Fehlertext aus dem Protokoll und die Schritte hinzu, mit denen sich der Fehler auslösen lässt. **Hängen Sie niemals `config.php`, eine Sicherung der Datenbank oder Tokens an.**
