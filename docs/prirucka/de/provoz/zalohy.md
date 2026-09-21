# Sicherungen und Wiederherstellung

Sicherungen verwalten Sie unter **Einstellungen → Sicherungen und Aktualisierungen**. Das System sichert die **Datenbank** – Artikel, Seiten, Einstellungen, Benutzer, Leser, Kommentare. **Hochgeladene Bilder und Anhänge** liegen im Ordner `media/` und werden getrennt gesichert.

## Sicherungen der Datenbank

- **Automatisch einmal pro Woche**, wenn die Option **Automatische Sicherung einmal pro Woche** eingeschaltet ist. Die Sicherung entsteht bei der Anmeldung eines Administrators oder – wenn Sie den Cron eingerichtet haben – beim Lauf der [Hintergrundaufgaben](ulohy-na-pozadi.md), sobald die letzte Sicherung älter als eine Woche ist.
- **Vor jeder Aktualisierung** des Systems.
- **Manuell** mit der Schaltfläche **Sicherung jetzt erstellen**.

Die Dateien `phprs-JJJJMMTT-HHMMSS-….sql.gz` liegen im Ordner `storage/zalohy/`, der aus dem Internet nicht erreichbar ist. Das System behält die **letzten 10 Sicherungen**, ältere löscht es. Zum Erstellen braucht es kein `mysqldump`, es funktioniert also auch auf Shared Hosting.

Jede Sicherung können Sie **herunterladen**, **wiederherstellen** oder löschen.

## Sicherungskopien außerhalb des Servers

Eine Sicherung auf demselben Server wie die Website hilft nicht, wenn Sie das Hosting verlieren, wenn jemand es angreift oder wenn die Festplatte ausfällt. Legen Sie deshalb im Abschnitt **Sicherungskopien außerhalb des Servers** fest, wohin jede neue Sicherung von selbst hochgeladen werden soll:

- **auf einen FTP-Server** – ein anderes Hosting oder ein NAS zu Hause; einzugeben sind Server, Benutzer, Passwort und Ordner,
- **in einen S3-Speicher** – Amazon S3, Backblaze B2, Wasabi, Cloudflare R2; einzugeben sind Endpunkt, Region, Zugriffsschlüssel, geheimer Schlüssel und Bucket mit Ordner.

Speichern Sie die Einstellungen und klicken Sie auf **Sicherung jetzt erstellen** – die Kopie wird sofort hochgeladen, und Sie sehen, ob die Verbindung funktioniert. Das Ergebnis des letzten Versuchs zeigt auch der **Systemstatus**.

Für Sicherungen empfehlen wir im Speicher einen eigenen Zugang, der in einen einzigen Ordner **nur schreiben** darf.

## Mediensicherung

Die Schaltfläche **Mediensicherung herunterladen (ZIP)** packt den Ordner `media/`. Bei einer Website mit vielen Fotos kann die Datei groß sein und ihre Vorbereitung dauern; dann ist es zuverlässiger, den Ordner `media/` regelmäßig per FTP herunterzuladen oder mit einem Werkzeug des Hostings zu sichern.

## Wiederherstellung

Klicken Sie bei der Sicherung auf **Wiederherstellen**. Die Wiederherstellung **überschreibt den aktuellen Inhalt der Datenbank** mit dem Stand aus der Sicherung – alles, was danach auf der Website hinzugekommen ist, verschwindet. Den aktuellen Stand speichert das System vorher selbst in einer neuen Sicherung, der Schritt lässt sich also rückgängig machen.

Wenn die Administration nicht läuft, gehen Sie nach dem Kapitel [Umzug der Website](../zaciname/presun-webu.md) vor: Spielen Sie die Sicherung mit einem Werkzeug des Hostings in die Datenbank ein.

## Empfohlene Vorgehensweise

1. Lassen Sie die automatischen Sicherungen eingeschaltet.
2. Richten Sie Kopien außerhalb des Servers ein.
3. Versuchen Sie von Zeit zu Zeit, eine Sicherung auf einer Testinstallation wiederherzustellen – eine Sicherung, deren Wiederherstellung niemand ausprobiert hat, ist nur eine Hoffnung.
