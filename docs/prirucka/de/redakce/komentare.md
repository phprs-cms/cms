# Kommentare

Kommentare unter Artikeln gehören zur Erweiterung **Kommentare und Bewertungen**, die nach der Installation eingeschaltet ist. Ein- und ausgeschaltet wird sie vom Administrator unter dem Punkt **Erweiterungen** des Hauptmenüs. Moderiert wird unter **Leser → Kommentare**; Zugriff haben Redakteur und Administrator.

## Wo Kommentare erlaubt werden

Kommentare erscheinen unter einem Artikel, wenn alle drei Bedingungen erfüllt sind:

1. die Erweiterung **Kommentare und Bewertungen** ist eingeschaltet,
2. unter **Einstellungen → Allgemein** ist die Option **Kommentare unter Artikeln** eingeschaltet,
3. beim Artikel ist im Abschnitt **Weitere Einstellungen → Optionen** das Häkchen bei **Kommentare erlauben** gesetzt (bei einem neuen Artikel ist es gesetzt).

Bei einem heiklen Thema genügt es also, die Kommentare bei einem einzelnen Artikel auszuschalten.

## Moderationsmodi

**Einstellungen → Allgemein → Neuer Kommentar:**

| Option | Wie sie sich verhält |
|---|---|
| **sofort veröffentlichen (verdächtige warten auf Freigabe)** | der Kommentar ist sofort sichtbar; ein Kommentar mit zwei und mehr Links wartet auf Freigabe |
| **erst nach Freigabe durch die Redaktion veröffentlichen** | jeder Kommentar wartet auf Freigabe |

Dem Leser wird nach dem Absenden entweder ein Dank angezeigt oder die Mitteilung, dass der Kommentar nach der Freigabe durch die Redaktion erscheint.

## Wer kommentieren darf

In der Standardeinstellung kann jeder kommentieren. Er füllt **Name**, die optionale **E-Mail** (wird nicht veröffentlicht) und einen Text von höchstens 5 000 Zeichen aus.

Mit eingeschalteter Erweiterung **Leser und gesperrter Inhalt** kommt unter **Einstellungen → Leser und Zahlungen** die Option **Nur angemeldete Leser dürfen kommentieren** hinzu. Wer nicht angemeldet ist, sieht dann statt des Formulars eine Aufforderung zur Anmeldung. Ein angemeldeter Leser kommentiert unter dem Namen aus seinem Konto und bei seinen Kommentaren steht die Markierung ✓ (*registrierter Leser*).

## Spamschutz

Das Formular schützt ein eingebauter Spamschutz. Zur Prüfung verwendet er weder CAPTCHA noch Cookies und ruft keinen fremden Dienst auf:

- das Formular trägt einen signierten Zeitstempel – es lässt sich weder früher als nach einigen Sekunden noch nach mehreren Stunden absenden,
- ein verstecktes Feld, das ein Mensch nicht sieht und ein Bot ausfüllt; ein solcher Kommentar wird verworfen und der Bot erfährt nicht, dass er gescheitert ist,
- von einer Adresse gehen höchstens 5 Kommentare in 10 Minuten durch,
- ein Kommentar mit mehreren Links wartet auch im Modus der sofortigen Veröffentlichung auf Freigabe.

## Moderation

**Leser → Kommentare** hat zwei Reiter: **Alle** und **Warten auf Freigabe** mit der Anzahl. Bei jedem Kommentar sehen Sie den Text, Name und E-Mail des Absenders, einen kurzen Fingerabdruck seiner Adresse (gleicher Fingerabdruck = gleicher Verfasser; die IP-Adresse selbst wird nicht gespeichert), den Artikel, das Datum und den Status (*veröffentlicht* oder *wartet / ausgeblendet*).

1. Haken Sie die Kommentare an.
2. Wählen Sie unter der Tabelle **Freigeben**, **Ausblenden** oder **Löschen**.

- **Freigeben** veröffentlicht den Kommentar und setzt die Meldungen auf null.
- **Ausblenden** nimmt ihn von der Website, belässt ihn aber in der Administration. Er lässt sich später wieder freigeben.
- **Löschen** lässt sich nicht rückgängig machen und löscht auch die Antworten auf den Kommentar.

Der Text eines Kommentars lässt sich nicht bearbeiten. Die Zahl der Kommentare, die auf Freigabe warten, zeigt auch die **Übersicht**.

## Antworten und Benachrichtigungen an Leser

Ein Leser kann mit der Schaltfläche **Antworten** auf einen Kommentar antworten. Die Antwort erscheint eingerückt darunter; die Diskussionsstränge haben eine Ebene.

Wer beim Kommentieren eine E-Mail-Adresse angibt und **Per E-Mail benachrichtigen, wenn jemand antwortet** anhakt, erhält eine Nachricht, sobald eine Antwort auf seinen Kommentar veröffentlicht ist – bei Kommentaren mit Freigabe also erst nach der Freigabe. Die E-Mail enthält einen Link in die Diskussion und einen Link, mit dem er weitere Benachrichtigungen zu diesem Kommentar ausschaltet. Die Nachricht kommt in der Sprache der Version der Website, unter deren Artikel der Leser kommentiert hat.

## Einen Kommentar melden

Bei jedem Kommentar steht der Link **melden**. Mit ihm macht ein Leser die Redaktion auf einen unangemessenen Beitrag aufmerksam:

- von einer Adresse zählt die Meldung desselben Kommentars einmal pro Tag,
- nach drei Meldungen wird der Kommentar von selbst ausgeblendet und wartet auf die Beurteilung durch die Redaktion,
- in der Moderation trägt ein gemeldeter Kommentar die Markierung *2× gemeldet*.

Mit der Freigabe wird die Zahl der Meldungen auf null gesetzt und der Kommentar kehrt auf die Website zurück.

## E-Mails an die Redaktion

**Einstellungen → Allgemein → Weitere Optionen → E-Mail an die Redaktion über Kommentare:**

- **wenn ein Kommentar auf Freigabe wartet** (Standard),
- **bei jedem neuen Kommentar**,
- **nicht senden**.

Die Nachricht geht an die **E-Mail der Redaktion** aus Einstellungen → Allgemein, höchstens einmal in 10 Minuten. Sie enthält den Artikel, den Verfasser und den Anfang des Kommentars, die Zahl der wartenden Kommentare und einen Link in die Moderation.

## Sternebewertung

Dieselbe Erweiterung fügt unter den Artikeln eine Bewertung mit Sternen (1–5) hinzu. Eine wiederholte Abstimmung beim selben Artikel nimmt das System 30 Tage lang nicht an. Ausgeschaltet wird sie unter **Einstellungen → Allgemein → Weitere Optionen → Sternebewertung von Artikeln**.

## Siehe auch

- [Rollen und Berechtigungen](role-a-opravneni.md)
- [E-Mail](../provoz/posta.md) – damit die Benachrichtigungen ankommen
- [Sicherheit](../provoz/bezpecnost.md)
