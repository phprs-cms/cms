# Hintergrundaufgaben (cron)

Manche Arbeiten geschehen nicht auf Anweisung eines Menschen, sondern zu einer bestimmten Zeit: die Veröffentlichung eines geplanten Artikels, der Versand von Benachrichtigungen und Newslettern, der erneute Versand nicht zugestellter E-Mails, die wöchentliche Sicherung.

## Es funktioniert auch ohne Einrichtung

In der Standardeinstellung werden diese Aufgaben **bei Besuchen der Website** ausgeführt. Eine Website mit gewöhnlichen Besucherzahlen muss also nichts einrichten. Bei einer Website, die nachts niemand besucht, erscheint ein für 6:00 Uhr geplanter Artikel aber erst mit dem ersten Leser am Morgen – und der Newsletter geht später hinaus, als Sie wollten.

## Die genaue Zeit sichert der Cron

1. Öffnen Sie **Einstellungen → Systemstatus**, Abschnitt **Hintergrundaufgaben (cron)**.
2. Klicken Sie auf **Adresse für cron erstellen**. Es erscheint eine fertige Zeile:

   ```
   */5 * * * * curl -s "https://www.example.de/ulohy?token=…" > /dev/null
   ```

3. Fügen Sie die Zeile in die Aufgabenplanung (Cron) in der Verwaltung des Hostings ein. Wenn das Hosting nicht den ganzen Befehl, sondern nur eine Adresse verlangt, geben Sie nur die Adresse und das Intervall **5 Minuten** ein.

Ein Hosting ohne Cron ersetzt jeder Dienst, der eine Adresse regelmäßig aufrufen kann (zum Beispiel Monitoring-Tools wie UptimeRobot).

Die Adresse enthält ein geheimes Token. Sollte es bekannt werden, tauschen Sie die Adresse mit der Schaltfläche **Neue Adresse erstellen (die alte wird ungültig)** aus; vergessen Sie danach nicht, den Cron anzupassen.

## Woran ich erkenne, dass es läuft

**Systemstatus**, Zeile **Hintergrundaufgaben**: In Ordnung ist sie, wenn die Aufgaben in den letzten 30 Minuten gelaufen sind. Eine Warnung bedeutet, dass der Cron nicht aufruft oder dass niemand die Website besucht hat.

Die Adresse können Sie auch im Browser öffnen – sie antwortet mit der Zeile `OK`, der Zeit und einer Liste dessen, was ausgeführt wurde.

## Was die Aufgaben genau tun

- geplante Artikel veröffentlichen und die Benachrichtigungen dazu verschicken (E-Mail, Web Push, Webhooks),
- den nächsten Teil eines laufenden Newsletter-Versands senden,
- E-Mails erneut senden, die nicht zugestellt werden konnten,
- die wöchentliche Sicherung der Datenbank erstellen und außerhalb des Servers hochladen, wenn das eingeschaltet ist.
