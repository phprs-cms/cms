# Leserkonten

Leser können sich auf der Website registrieren. Mit einem Konto können sie Artikel für später speichern, gesperrte Artikel lesen und – wenn Sie es so einstellen – kommentieren. Leserkonten sind von den Konten der Redaktion getrennt: Ein Leser gelangt nie in die Administration.

## Einschalten

1. Öffnen Sie im Hauptmenü **Erweiterungen**, haken Sie **Leser und gesperrter Inhalt** an und speichern Sie.
2. Fügen Sie der Website den Block **Leserkonto** hinzu (**Design → Blöcke und Layout**). Er gibt die Schaltfläche **Anmelden / Mein Konto** aus. Ohne den Block gelangt der Leser zur Anmeldung nur über die Aufforderung bei einem gesperrten Artikel, über den Link **Für später speichern** unter dem Artikel und direkt unter der Adresse `/ctenar`.
3. Prüfen Sie, dass die Website E-Mails versendet – Registrierung und Anmeldung per Link beruhen darauf. Siehe [E-Mail](../provoz/posta.md).

In der Administration kommt der Bereich **Leser → Leser** hinzu und unter **Einstellungen → Allgemein** der Abschnitt **Leser und gesperrter Inhalt**. Beides sieht nur der Administrator.

## Registrierung

Der Leser füllt auf der Seite `/ctenar` im Teil **Ich bin neu hier** die **E-Mail** und gegebenenfalls den **Namen** (freiwillig) aus und klickt auf **Kostenlos registrieren**. Ist die Erweiterung Newsletter eingeschaltet, kann er **Ich möchte den Newsletter erhalten** anhaken.

Ein Passwort wird bei der Registrierung nicht eingegeben. Der Leser erhält eine E-Mail mit einem Link, über den er sein Passwort festlegt (mindestens 8 Zeichen) – damit schließt er die Registrierung ab und ist gleich angemeldet. Der Link gilt 3 Tage. Wer eine fremde Adresse registriert, kommt so nicht an das Konto. Ein bei der Registrierung gewähltes Newsletter-Abonnement wird mit demselben Link bestätigt.

Versucht jemand, eine E-Mail-Adresse zu registrieren, die schon ein Konto hat, antwortet die Website genauso wie bei einer neuen Registrierung. Der Inhaber der Adresse erhält eine E-Mail, dass er bereits ein Konto hat. Die Website verrät so nicht, welche Adressen registriert sind.

Neue Registrierungen stoppen Sie mit der Option **Neue Registrierungen erlauben** unter **Einstellungen → Allgemein → Leser und gesperrter Inhalt**. Bestehende Leser melden sich weiterhin an.

## Anmeldung

Der Leser hat drei Möglichkeiten:

| Weg | Wie er funktioniert |
|---|---|
| **E-Mail und Passwort** | gewöhnliche Anmeldung |
| **Mit einem Link per E-Mail anmelden** | der Leser füllt nur die E-Mail aus und erhält einen einmaligen Link; er gilt 20 Minuten |
| **Passwort vergessen?** | sendet einen Link zum Festlegen eines neuen Passworts; er gilt 2 Stunden |

Der Link aus der E-Mail meldet nicht sofort an – er zeigt die Schaltfläche **Anmelden**. Das ist Absicht: Manche E-Mail-Programme öffnen Links vorab und würden den einmaligen Link verbrauchen.

Nach zehn falschen Passwörtern innerhalb von 15 Minuten wird die Anmeldung mit Passwort für die jeweilige E-Mail-Adresse vorübergehend gesperrt. Die Anmeldung per Link aus der E-Mail funktioniert weiter.

Die Anmeldung hält das Cookie `phprs_ctenar` für 60 Tage. Es ist ein technisches Cookie, das für die Anmeldung notwendig ist. Für einen angemeldeten Leser werden die Seiten nicht aus dem Cache genommen.

## Was der Leser im Konto hat

Die Seite `/ctenar` zeigt nach der Anmeldung:

- die E-Mail und gegebenenfalls das Datum, bis zu dem das Abonnement gilt (oder die Schaltfläche **Abonnement abschließen**),
- **Gespeicherte Artikel** – eine Liste mit der Möglichkeit **Aus Gespeicherten entfernen**,
- die Änderung von Name und Passwort (**Passwort ändern** verlangt das bisherige Passwort),
- **Abmelden**,
- **Konto löschen** – löscht nach Eingabe des Passworts das Konto und alle Daten dazu; das lässt sich nicht rückgängig machen.

### Gespeicherte Artikel

Unter jedem Artikel steht der Link **☆ Für später speichern**. Einen nicht angemeldeten Leser führt er zur Anmeldung und bringt ihn zurück zum Artikel. Bei einem gespeicherten Artikel ändert sich die Schaltfläche in **★ Gespeichert – entfernen** und es kommt der Link **Meine gespeicherten Artikel** hinzu. Ein Leser kann höchstens 500 Artikel speichern.

## Leser in der Administration

Der Bereich **Leser → Leser** zeigt oben drei Zahlen: **Registrierte**, **Abonnenten** und **Gesperrte Artikel**. Darunter stehen die Suche nach E-Mail oder Name und eine Tabelle der letzten 300 Konten:

| Spalte | Inhalt |
|---|---|
| **E-Mail** | bei einer nicht abgeschlossenen Registrierung mit dem Etikett „E-Mail nicht bestätigt“ |
| **Name** | sofern der Leser ihn ausgefüllt hat |
| **Registrierung** | Datum der Registrierung |
| **Zuletzt** | Datum der letzten Anmeldung |
| **Abonnement** | Status und das Menü **ändern…** – siehe [Gesperrte Inhalte und Abonnement](zamceny-obsah.md) |
| **Aktionen** | **Löschen** – löscht nach Bestätigung das Konto |

**CSV herunterladen** speichert die Datei `ctenari.csv` mit allen bestätigten Konten: E-Mail, Name, Datum der Registrierung und Ende des Abonnements. Passwörter und gespeicherte Artikel sind darin nicht enthalten.

Der Administrator sieht das Passwort eines Lesers nicht und kann es nicht ändern. Der Leser legt selbst ein neues über den Link aus der E-Mail fest.

## Kommentare nur für Angemeldete

Die Option **Nur angemeldete Leser dürfen kommentieren** (**Einstellungen → Allgemein → Leser und gesperrter Inhalt**) beschränkt die Diskussion auf Registrierte. Der Leser kommentiert dann unter seinem Konto. Mehr auf der Seite [Kommentare](../redakce/komentare.md).

## Anfrage auf Auskunft oder Löschung von Daten

Sein Konto löscht der Leser selbst. Wenn er Sie darum bittet (DSGVO), verwenden Sie **Einstellungen → Datenschutz und Cookies**, Abschnitt **Anfrage eines Lesers zu personenbezogenen Daten**:

1. Geben Sie in das Feld **E-Mail des Lesers** seine Adresse ein.
2. **Seine Daten herunterladen** speichert die Datei `osobni-udaje.json` mit seinen Kommentaren, dem Newsletter-Abonnement und dem Leserkonto.
3. **Seine Daten löschen** löscht nach Bestätigung unwiderruflich seine Kommentare, das Newsletter-Abonnement und das Leserkonto. Die Meldung nach dem Löschen führt auf, was entfernt wurde.

## Siehe auch

- [Gesperrte Inhalte und Abonnement](zamceny-obsah.md)
- [Newsletter](newsletter.md)
- [Kommentare](../redakce/komentare.md)
- [Webanalyse und Datenschutz](../seo-a-ai/mereni-a-soukromi.md)
