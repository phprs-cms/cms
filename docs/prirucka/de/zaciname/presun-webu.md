# Umzug der Website auf ein anderes Hosting oder eine andere Domain

Die Website besteht aus drei Dingen: **Dateien**, **Datenbank** und **Einstellung der Adresse**. Der Umzug dauert je nach Menge der Fotos einige Minuten bis einige Dutzend Minuten.

## Vorgehen

1. **Sicherung der Datenbank.** **Einstellungen → Sicherungen und Aktualisierungen → Sicherung jetzt erstellen**, dann laden Sie die Datei herunter. An derselben Stelle laden Sie auch die **Mediensicherung (ZIP)** herunter.
2. **Dateien.** Kopieren Sie den gesamten Ordner der Website auf das neue Hosting, einschließlich `config.php`, der Ordner `media/` und `storage/` und aller versteckten Dateien `.htaccess`.
3. **Datenbank.** Legen Sie auf dem neuen Hosting eine leere Datenbank an und spielen Sie die Sicherung ein – über phpMyAdmin (eine Datei `.sql.gz` kann es direkt einlesen) oder mit einem Werkzeug des Hostings.
4. **`config.php`.** Tragen Sie darin die Datenbankdaten des neuen Hostings ein: `host`, `port`, `name`, `user`, `password`. Das Tabellenpräfix ändern Sie nicht.
5. **Adresse der Website.** Wenn sich die Domain ändert, melden Sie sich in der Administration an und geben Sie unter **Einstellungen → Allgemein → Adresse der Website** die neue Adresse einschließlich `https://` ein. Aus dieser Einstellung – nicht aus der Adresse im Browser – werden die Links in E-Mails, im RSS, in der Sitemap und in Benachrichtigungen zusammengesetzt.
6. **Cache.** Löschen Sie den Inhalt des Ordners `storage/cache/`; er entsteht neu.
7. **Kontrolle.** Gehen Sie **Einstellungen → Systemstatus** durch – er zeigt fehlende PHP-Erweiterungen, Schreibrechte und den Stand von HTTPS am neuen Ort.

## Woran Sie denken sollten

- **Weiterleitung von der alten Domain.** Lassen Sie auf dem alten Hosting eine 301-Weiterleitung auf die neue Domain bestehen, damit Sie keine Links und keine Positionen in den Suchmaschinen verlieren.
- **E-Mail.** Wenn sich auch das E-Mail-Postfach ändert, passen Sie **Einstellungen → E-Mail** an und schicken Sie sich eine Testnachricht.
- **Cron.** Wenn Sie den Aufruf der Adresse für Aufgaben eingerichtet haben, übertragen Sie ihn mit der neuen Adresse auf das neue Hosting (siehe [Hintergrundaufgaben](../provoz/ulohy-na-pozadi.md)).
- **Web Push.** Abonnements von Benachrichtigungen sind an die Domain gebunden; nach einem Domainwechsel müssen die Leser sie neu einschalten.
- **Zeitzone.** Sie ist in den Einstellungen der Website gespeichert und zieht mit der Datenbank um; die Zeitzone des neuen Servers spielt keine Rolle.

## Wiederherstellung aus einer Sicherung

Dasselbe Vorgehen hilft auch nach einem Ausfall. Wenn die Administration läuft, lässt sich eine Sicherung auch direkt dort wiederherstellen: **Einstellungen → Sicherungen und Aktualisierungen**, bei der gewählten Sicherung **Wiederherstellen**. Die Wiederherstellung überschreibt den aktuellen Inhalt der Datenbank; den Stand davor speichert das System selbst in einer neuen Sicherung.
