# Sicherheit

phpRS ist in der Standardeinstellung sicher konfiguriert. Diese Seite fasst zusammen, was nach der Installation zu tun ist, was das System selbst überwacht und wie Sie bei Verdacht auf einen Angriff vorgehen.

## Fünf Dinge nach der Installation

1. **Prüfen Sie, dass `install.php` entfernt ist.** Das Installationsprogramm löscht sich nach Abschluss selbst; wenn der Server das nicht zugelassen hat, löschen Sie die Datei von Hand – der Systemstatus weist in der Zeile **Installer** darauf hin.
2. **Schalten Sie HTTPS ein** und die Weiterleitung von `http://`. Ein Zertifikat (Let's Encrypt) bietet heute jeder Hoster kostenlos an. Über HTTPS sendet das System auch den Header HSTS.
3. **Schalten Sie die Zwei-Faktor-Anmeldung** für alle Administratoren ein: Ein Klick auf den Avatar oben rechts öffnet **Mein Konto**, Abschnitt **Zwei-Faktor-Anmeldung**. **Passkeys** (Fingerabdruck, Face ID) werden erst nach deren Einschalten angeboten. Es genügt eine beliebige Authentifizierungs-App (Google Authenticator, 1Password, Aegis…). Die acht **Ersatzcodes** werden nur einmal angezeigt – bewahren Sie sie außerhalb des Telefons auf.
   Wer nicht bei jeder Anmeldung einen Code abtippen möchte, fügt an derselben Stelle einen **Passkey** hinzu: Fingerabdruck, Face ID, Windows Hello oder einen Sicherheitsschlüssel. Ein Passkey ist an die Domain der Website gebunden, eine gefälschte Seite kann ihn daher nicht erlangen; der Code aus der App und die Ersatzcodes bleiben als Rückfallebene für den Fall, dass Sie das Gerät nicht dabeihaben. Nach einem Umzug der Website auf eine andere Domain müssen Passkeys neu hinzugefügt werden.
4. **Richten Sie Sicherungen außerhalb des Servers ein** – siehe [Sicherungen und Wiederherstellung](zalohy.md).
5. **Lassen Sie die automatischen Sicherheitsaktualisierungen eingeschaltet** – siehe [Aktualisierungen](../zaciname/aktualizace.md).

## Konten und Berechtigungen

- Jede Person hat ein **eigenes Konto**. Bei geteilten Konten lässt sich nicht nachvollziehen, wer was geändert hat.
- Vergeben Sie die **geringsten nötigen Rechte**: einem Redakteur nur die Module, die er verwendet, gegebenenfalls nur seine Ressorts. Halten Sie die Zahl der Administratoren so klein wie möglich.
- Wenn jemand die Redaktion verlässt, **sperren** Sie sein Konto (**Verwaltung → Benutzer** → Konto → **Detaillierte Einstellungen** → **Konto sperren**) – seine Artikel bleiben mit seinem Namen gekennzeichnet.
- Ein Passwort hat mindestens 10 Zeichen. Nach einer Passwortänderung wird das Konto auf allen anderen Geräten abgemeldet.

## Was das System selbst überwacht

- **Erraten von Passwörtern:** Nach 10 Fehlversuchen wird das Konto für 15 Minuten gesperrt; dasselbe Limit gilt für eine IP-Adresse und für falsche Codes der Zwei-Faktor-Anmeldung. Die Sperre ist absichtlich vorübergehend – sonst könnte jeder die Redaktion lahmlegen.
- **Die Anmeldung der Leser** hat denselben Schutz.
- **Die Administration** sendet eine strenge Content Security Policy (keine fremden und keine eingebetteten Skripte) und ein Verbot der Speicherung im Cache.
- **Hochgeladene Dateien** im Ordner `media/` werden nie ausgeführt; erlaubt sind nur sichere Typen.
- **Aktualisierungen** werden nur mit gültiger Signatur des Herausgebers installiert.
- **Integrität des Kerns:** Der Systemstatus vergleicht die Dateien mit der signierten Liste der Version und meldet geänderte, fehlende und hinzugefügte Dateien.
- **Verwaltung → Änderungsprotokoll** zeichnet Anmeldungen und wichtige Änderungen auf.
- **Kommentare und Formulare** schützt ein Spamschutz ohne fremde Dienste und ohne Tracking der Leser.

## Verbindung mit Claude und API

Erstellen Sie Tokens für die Verbindung (MCP) und die API **für jeden Zweck getrennt** und löschen Sie unbenutzte. Die Verbindung darf nur mit Inhalten und eigenen Vorlagen arbeiten – sie erreicht weder die Einstellungen des Servers noch die Benutzer oder die Dateien des Systems. Eigene Vorlagen durchlaufen eine Prüfung, die weder die Arbeit mit Dateien und dem Netzwerk noch das Starten von Prozessen zulässt.

## Verdacht auf einen Angriff

1. Prüfen Sie im **Systemstatus** die Zeile **Kerndateien** und gehen Sie **Verwaltung → Änderungsprotokoll** durch.
2. Ändern Sie die Passwörter aller Administratoren, das Passwort zur Datenbank und zum FTP. Erstellen Sie neue Tokens für Cron, Monitoring, API und die Verbindung.
3. Überschreiben Sie die Dateien des Systems mit einem sauberen Paket derselben Version (Vorgehen wie bei der manuellen Aktualisierung). Löschen Sie unbekannte Dateien – vor allem in `media/` und `layout/`.
4. Wenn Sie sich über das Ausmaß nicht sicher sind, stellen Sie die Datenbank aus einer Sicherung wieder her, die vor dem Angriff erstellt wurde.

## Eine Sicherheitslücke melden

Bitte melden Sie Sicherheitslücken **nicht öffentlich**. Verwenden Sie die private Meldung auf GitHub (*Security → Report a vulnerability*) oder die E-Mail-Adresse, die auf der Website des Projekts angegeben ist. Nennen Sie die Version und die Schritte, mit denen sich der Fehler auslösen lässt. Die Korrektur erscheint als Sicherheitsversion, die sich auf Websites mit eingeschalteter Automatik von selbst installiert.
