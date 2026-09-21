# Konto und Anmeldung

Sein eigenes Konto verwaltet jeder selbst auf dem Bildschirm **Mein Konto**. Sie öffnen ihn mit einem Klick auf den Avatar mit dem Anfangsbuchstaben oben rechts. Neben dem Avatar steht auch der Umschalter zwischen hellem und dunklem Modus der Administration.

## Meine Daten

| Feld | Wozu es dient |
|---|---|
| **Name** | erscheint bei Ihren Artikeln auf der Website |
| **E-Mail** | an sie gehen die Benachrichtigungen der Redaktion und der Link bei vergessenem Passwort |
| **Meine Website** | optionale Adresse Ihrer Website |
| **Position in der Redaktion** | zum Beispiel *Kulturredakteurin* |
| **Mein Foto** | quadratisches Foto, 300 × 300 px genügen; Sie wählen es aus den Medien |
| **Ein paar Sätze über mich** | höchstens 1 200 Zeichen |
| **Sprache der Administration** | Čeština, Slovenčina, English oder Deutsch |
| **E-Mail-Benachrichtigungen** | Nachrichten über Korrektur, Veröffentlichung und Rückgabe eines Artikels |

Wenn **Ein paar Sätze über mich** ausgefüllt ist, erscheint unter Ihren Artikeln ein Autorenkasten mit Name, Position, Foto und diesem Text. Dieselben Angaben stehen auf der Autorenseite.

Die **Sprache der Administration** gilt nur für Sie – jedes Mitglied der Redaktion kann in einer anderen Sprache arbeiten. In dieser Sprache erhalten Sie auch die Benachrichtigungen der Redaktion. Die Sprache der Website ändert sich dadurch nicht.

Den **Benutzernamen** können Sie nicht ändern; das tut der Administrator im Bereich Benutzer. Bestätigen Sie Änderungen mit der Schaltfläche **Daten speichern**.

## Passwortänderung

1. Füllen Sie **Aktuelles Passwort** aus.
2. Geben Sie in das Feld **Neues Passwort** das neue Passwort ein (mindestens 10 Zeichen) und noch einmal in das Feld **Neues Passwort wiederholen**.
3. Klicken Sie auf **Passwort ändern**.

Mit der Passwortänderung enden alle anderen Anmeldungen Ihres Kontos – auf einem anderen Computer, im Telefon, in einem vergessenen Browser. Die Anmeldung, in der Sie das Passwort ändern, bleibt bestehen.

## Passwort vergessen

1. Klicken Sie auf der Anmeldeseite auf **Passwort vergessen?**
2. Geben Sie den Benutzernamen oder die E-Mail-Adresse Ihres Kontos ein und klicken Sie auf **Link senden**.
3. Öffnen Sie den Link aus der E-Mail, geben Sie zweimal das neue Passwort ein und bestätigen Sie mit der Schaltfläche **Passwort festlegen**.
4. Melden Sie sich mit dem neuen Passwort an.

Der Link ist eine Stunde gültig und lässt sich einmal verwenden. Der Bildschirm antwortet immer gleich, ob das Konto existiert oder nicht – ein Fremder erfährt so nicht, wer in der Redaktion arbeitet. Die E-Mail erhält nur ein Konto, das eine Adresse eingetragen hat und nicht gesperrt ist. Die Zwei-Faktor-Anmeldung bleibt beim Zurücksetzen des Passworts eingeschaltet. Wenn die E-Mail nicht ankommt, legt Ihnen der Administrator unter **Verwaltung → Benutzer** ein Passwort fest.

## Zwei-Faktor-Anmeldung

Mit der Zwei-Faktor-Anmeldung geben Sie nach dem Passwort noch einen sechsstelligen Code aus der Authentifizierungs-App im Telefon ein. Wer das Passwort errät oder stiehlt, kann sich ohne Ihr Telefon nicht anmelden.

1. Klicken Sie im Abschnitt **Zwei-Faktor-Anmeldung** auf **Zwei-Faktor-Anmeldung einschalten**.
2. Fügen Sie in der Authentifizierungs-App (Google Authenticator, Microsoft Authenticator, 1Password, Aegis…) ein neues Konto hinzu, indem Sie den angezeigten Schlüssel von Hand eingeben. Auf dem Mobilgerät genügt ein Klick auf den Link unter dem Schlüssel – er öffnet die Authentifizierungs-App.
3. Übertragen Sie den Code aus der App in das Feld **Code aus der App** und klicken Sie auf **Bestätigen und einschalten**.
4. Es werden acht **Ersatzcodes** angezeigt. Bewahren Sie sie außerhalb des Telefons auf – sie werden nicht noch einmal angezeigt.

Jeder Ersatzcode lässt sich einmal verwenden, anstelle des Codes aus der App. Wie viele übrig sind, sehen Sie im Abschnitt Zwei-Faktor-Anmeldung. Stimmt der Code beim Einschalten nicht, prüfen Sie die Uhrzeit im Telefon.

Ausschalten: Geben Sie das **Passwort zur Bestätigung** ein und klicken Sie auf **Zwei-Faktor-Anmeldung ausschalten**. Wenn Sie Telefon und Ersatzcodes verlieren, schaltet der Administrator sie in Ihrem Konto im Bereich Benutzer aus.

## Passkeys

Ein Passkey ersetzt das Abtippen des Codes: Den zweiten Schritt der Anmeldung bestätigen Sie mit Fingerabdruck, Face ID, Windows Hello oder einem Sicherheitsschlüssel.

Voraussetzungen:

- ein Passkey lässt sich nur einem Konto mit eingeschalteter Zwei-Faktor-Anmeldung hinzufügen – der Abschnitt **Passkeys** wird bis dahin nicht angezeigt,
- die Website muss über HTTPS laufen und der Browser muss Passkeys unterstützen,
- ein Passkey ist an die Domain der Website gebunden. Unter einer anderen Adresse funktioniert er nicht, eine gefälschte Anmeldeseite kann ihn daher nicht erlangen. Nach einem Umzug der Website auf eine andere Domain müssen Passkeys neu hinzugefügt werden.

Einen Passkey hinzufügen:

1. Schreiben Sie in das Feld **Gerätename**, um welches Gerät es sich handelt (zum Beispiel *MacBook* oder *Telefon*).
2. Klicken Sie auf **Passkey von diesem Gerät hinzufügen** und bestätigen Sie die Aufforderung des Geräts.

Anmeldung: Klicken Sie nach der Eingabe von Benutzername und Passwort auf **Mit Fingerabdruck oder Passkey anmelden**. Der Code aus der App und die Ersatzcodes funktionieren weiterhin – für den Fall, dass Sie das Gerät nicht dabeihaben.

Die Tabelle der Passkeys zeigt das Gerät, das Datum des Hinzufügens und die letzte Verwendung. Mit der Schaltfläche **Löschen** entfernen Sie einen Passkey, etwa nach dem Verlust des Geräts. Mit dem Ausschalten der Zwei-Faktor-Anmeldung werden alle Passkeys gelöscht.

## Schutz der Anmeldung

Nach 10 Fehlversuchen in Folge wird das Konto für 15 Minuten gesperrt. Genauso zählen falsche Codes des zweiten Schritts und es gilt auch ein Limit von 10 Versuchen in 15 Minuten von einer Adresse. Die Sperre endet nach einer Viertelstunde von selbst.

## Tokens für die Verbindung mit Claude

Der Abschnitt **Verbindung mit Claude** ist nur mit eingeschalteter Erweiterung **Verbindung mit Claude** sichtbar (Hauptmenü **Erweiterungen**). Ein Token erlaubt Claude, in Ihrem Namen und mit Ihren Rechten mit der Website zu arbeiten. Neue Artikel legt er als Entwürfe an und alle seine Eingriffe stehen im Änderungsprotokoll.

1. Füllen Sie **Name des neuen Tokens** aus (zum Beispiel *Claude auf dem Notebook*) und klicken Sie auf **Token erstellen**.
2. Das Token wird nur einmal angezeigt, zusammen mit einer Anleitung zur Verbindung. Kopieren Sie es sofort.

Ein Token funktioniert ohne Passwort und ohne Zwei-Faktor-Anmeldung – schützen Sie es wie ein Passwort. Ein nicht mehr benötigtes Token widerrufen Sie mit der Schaltfläche **Token widerrufen**. Wenn Sie Tokens haben, bietet das Formular zur Passwortänderung die Option **auch die Verbindungs-Token widerrufen (Claude, API)**; lassen Sie sie bei Verdacht auf Missbrauch angehakt.

## Siehe auch

- [Rollen und Berechtigungen](role-a-opravneni.md)
- [Übergabe und Korrektur](predavka-a-korektura.md)
- [Sicherheit](../provoz/bezpecnost.md)
