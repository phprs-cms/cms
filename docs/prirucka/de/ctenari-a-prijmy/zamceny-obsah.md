# Gesperrte Inhalte und Abonnement

Einen Artikel können Sie angemeldeten Lesern oder Abonnenten vorbehalten. Die anderen sehen Titel, Vorspann, den Anfang des Textes und eine Aufforderung. Das System verkauft kein Abonnement und rechnet es nicht ab – die Zahlung nehmen Sie auf Ihre Weise entgegen und das Abonnement tragen Sie dem Leser von Hand ein.

Alles auf dieser Seite erfordert die Erweiterung **Leser und gesperrter Inhalt** (Hauptmenü **Erweiterungen**). Registrierung und Konten beschreibt die Seite [Leserkonten](ucty-ctenaru.md).

## Artikel sperren

Im Artikelformular gibt es bei eingeschalteter Erweiterung das Feld **Wer lesen darf**:

| Option | Wer den ganzen Artikel liest |
|---|---|
| **Alle** | jeder (Standard) |
| **Nur angemeldete Leser** | jeder, der ein Leserkonto hat und angemeldet ist |
| **Nur Abonnenten** | ein angemeldeter Leser mit gültigem Abonnement |

Die Option können Sie jederzeit ändern, auch bei einem veröffentlichten Artikel. Ein in der Administration angemeldetes Mitglied der Redaktion sieht alle Artikel vollständig.

## Was ein Leser ohne Zugang sieht

- Titel, Vorspann und Hauptbild,
- eine Vorschau: die ersten Absätze des Textes,
- einen Kasten mit der Aufforderung.

Die Aufforderung unterscheidet sich je nach Sperre:

| Sperre | Überschrift der Aufforderung | Schaltflächen |
|---|---|---|
| Nur angemeldete Leser | **Weiterlesen nach der Anmeldung** | **Anmelden**, **Registrieren** (wenn Registrierungen erlaubt sind) |
| Nur Abonnenten | **Dieser Artikel ist für Abonnenten** | **Abonnement abschließen** und für nicht Angemeldete **Ich habe schon ein Abo – anmelden** |

Nach der Anmeldung kehrt der Leser zu dem Artikel zurück, von dem er gekommen ist. Aus einem gesperrten Artikel werden auch die Fragen und Antworten nicht ausgegeben.

### Einstellungen

Klappen Sie unter **Einstellungen → Allgemein** den Abschnitt **Leser und gesperrter Inhalt** auf:

| Feld | Bedeutung | Standard |
|---|---|---|
| **Vorschau eines gesperrten Artikels** | wie viele Absätze des Textes ein Leser ohne Zugang sieht; den Vorspann sieht er immer; 0 = nur Vorspann; höchstens 10 | 2 |
| **Kostenlose Artikel pro Monat** | weiche Paywall, siehe unten; 0 = aus; höchstens 50 | 0 |
| **Wo man ein Abonnement bekommt** | wohin die Schaltfläche **Abonnement abschließen** führt | leer |
| **Text der Aufforderung unter der Vorschau** | ein eigener Satz in der Aufforderung, höchstens 300 Zeichen; leer = Standardtext | leer |

## Wo man ein Abonnement bekommt

Geben Sie in das Feld **Wo man ein Abonnement bekommt** eine der folgenden Möglichkeiten ein:

- eine Seite Ihrer Website, zum Beispiel `/predplatne` – legen Sie sie unter **Inhalt → Seiten** an und beschreiben Sie darauf Preis und Zahlungsweise (Kontonummer, QR-Code),
- einen Zahlungslink, der mit `https://` beginnt.

Eine andere Form der Adresse wird ignoriert. Solange Sie das Feld nicht ausfüllen, wird die Schaltfläche **Abonnement abschließen** nicht angezeigt – weder bei gesperrten Artikeln noch im Leserkonto – und der Leser weiß nicht, wie er Abonnent wird. Der Bildschirm **Einnahmen** weist darauf mit der Meldung **Es ist nicht ausgefüllt, wo Leser ein Abonnement erhalten.** hin.

## Abonnement eintragen

Ein Abonnement wird von Hand eingetragen, typischerweise nach Eingang der Zahlung.

1. Öffnen Sie **Leser → Leser** und suchen Sie den Leser nach seiner E-Mail.
2. Wählen Sie in der Spalte **Abonnement** im Menü **ändern…** eine der Möglichkeiten **+ 1 Monat**, **+ 3 Monate** oder **+ 1 Jahr**. Die Änderung wird sofort gespeichert.
3. Eine Meldung bestätigt das neue Datum: **Das Abonnement gilt bis …**

Die Verlängerung wird ab dem Ende des laufenden Abonnements gerechnet; bei einem Leser ohne Abonnement oder mit abgelaufenem ab heute. Durch wiederholte Auswahl addieren Sie also die Dauer. Die Option **beenden** nimmt das Abonnement weg.

Das Etikett in der Spalte zeigt den Status: **bis** mit Datum, **abgelaufen** oder **keines**. Das Abonnement gilt bis zum Ende des angegebenen Tages. Nach dessen Ablauf verliert der Leser ohne weiteren Eingriff den Zugang zu Artikeln für Abonnenten; das Konto bleibt ihm. Der Leser muss ein Konto haben, bevor Sie ihm das Abonnement eintragen – bitten Sie ihn, sich mit der E-Mail-Adresse zu registrieren, von der er gezahlt hat oder die er bei der Zahlung angegeben hat.

## Weiche Paywall

Das Feld **Kostenlose Artikel pro Monat** erlaubt jedem Besucher, einige gesperrte Artikel im Monat ohne Anmeldung zu lesen. Erst danach sieht er die Aufforderung.

- Gezählt werden nur geöffnete gesperrte Artikel, jeder einmal. Listen werden nicht gezählt.
- Unter einem kostenlos gelesenen Artikel steht der Hinweis **Dies ist Artikel 2 von 5, die Sie diesen Monat kostenlos lesen können.** mit dem Link **Anmelden**.
- Wenn das Kontingent aufgebraucht ist, ergänzt die Aufforderung: **Sie haben diesen Monat bereits alle 5 kostenlosen Artikel gelesen.**
- Ein neuer Monat beginnt wieder bei null.

Der Zähler liegt in dem signierten Cookie `phprs_cteno` im Browser des Lesers. Wer Cookies löscht oder die Website in einem privaten Fenster öffnet, beginnt von vorn. Bei einer weichen Paywall ist das üblich und gewollt: Ziel ist es, treue Leser freundlich hinzuweisen, nicht lückenlos abzusperren.

## Listen, Feeds und Suchmaschinen

| Ort | Was er bei einem gesperrten Artikel enthält |
|---|---|
| Listen auf der Website (Startseite, Ressort, Schlagwort, Suche) | Titel, Vorspann und Bild wie bei den übrigen Artikeln; ein besonderes Schloss-Symbol haben sie nicht |
| RSS | Titel und Vorspann (wie bei allen Artikeln) |
| JSON Feed, Markdown-Version des Artikels, Öffentliche API | Vorspann und Vorschau, nie der ganze Text; die API gibt zusätzlich das Kennzeichen `zamceno` zurück |
| Newsletter und Benachrichtigungen | Titel und Vorspann mit Link zur Website |

Der Text eines gesperrten Artikels wird an einer einzigen Stelle gekürzt, bevor er in irgendeine Vorlage oder einen Feed gelangt. Er lässt sich also nicht auf einem Umweg beschaffen.

**Suchmaschinen.** Die strukturierten Daten eines gesperrten Artikels tragen die Angabe `isAccessibleForFree: False`. Die Suchmaschine weiß so, dass es sich um bezahlten Inhalt handelt und nicht um untergeschobenen Text. Bei harter Sperre (weiche Paywall ausgeschaltet) sieht der Bot dasselbe wie ein nicht angemeldeter Leser: Vorspann und Vorschau. Mit eingeschalteter weicher Paywall sieht er die Artikel vollständig, weil er keine Cookies sendet und jeder Artikel für ihn der erste im Monat ist. Wenn Sie möchten, dass die vollständigen Texte indexiert werden, schalten Sie die weiche Paywall mit mindestens einem Artikel pro Monat ein.

## Siehe auch

- [Leserkonten](ucty-ctenaru.md)
- [Unterstützung und Einnahmen](podpora-a-prijmy.md)
- [SEO](../seo-a-ai/seo.md)
- [Kommentare](../redakce/komentare.md)
