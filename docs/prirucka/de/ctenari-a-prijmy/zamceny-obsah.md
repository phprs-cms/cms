# Gesperrte Inhalte und Abonnement

Einen Artikel können Sie angemeldeten Lesern oder Abonnenten vorbehalten. Die anderen sehen Titel, Vorspann, den Anfang des Textes und eine Aufforderung. Ein Abonnement bekommt der Leser auf einem von zwei Wegen: Er bezahlt es selbst mit Karte über den Dienst Stripe und die Website schaltet es ihm von selbst ein und verlängert es (siehe [Zahlungen mit Stripe](platby-stripe.md)), oder Sie nehmen die Zahlung auf Ihre Weise entgegen und tragen ihm das Abonnement von Hand ein. Beide Wege lassen sich gleichzeitig nutzen.

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

Die Schaltfläche **Abonnement abschließen** führt bei eingeschalteten Zahlungen mit Stripe ins Leserkonto, wo das Abonnement bezahlt wird; sonst zur Adresse aus dem Feld **Wo man ein Abonnement bekommt**.

Nach der Anmeldung kehrt der Leser zu dem Artikel zurück, von dem er gekommen ist. Aus einem gesperrten Artikel werden auch die Fragen und Antworten nicht ausgegeben.

### Einstellungen

Öffnen Sie **Einstellungen → Leser und Zahlungen**, Abschnitt **Leser und gesperrter Inhalt**:

| Feld | Bedeutung | Standard |
|---|---|---|
| **Vorschau eines gesperrten Artikels** | wie viele Absätze des Textes ein Leser ohne Zugang sieht; den Vorspann sieht er immer; 0 = nur Vorspann; höchstens 10 | 2 |
| **Kostenlose Artikel pro Monat** | weiche Paywall, siehe unten; 0 = aus; höchstens 50 | 0 |
| **Wo man ein Abonnement bekommt** | wohin die Schaltfläche **Abonnement abschließen** führt, solange die Zahlungen mit Stripe nicht eingeschaltet sind | leer |
| **Text der Aufforderung unter der Vorschau** | ein eigener Satz in der Aufforderung, höchstens 300 Zeichen; leer = Standardtext | leer |

Im selben Abschnitt befindet sich auch der Teil **Zahlungen mit Stripe** – ihn beschreibt eine [eigene Seite](platby-stripe.md).

## Wo man ein Abonnement bekommt

Mit eingeschalteten [Zahlungen mit Stripe](platby-stripe.md) wird dieses Feld nicht verwendet: Die Schaltfläche **Abonnement abschließen** führt ins Leserkonto, wo ein angemeldeter Leser das Monats- oder Jahresabonnement wählt und mit Karte bezahlt. Ein nicht angemeldeter Leser meldet sich zuerst an oder registriert sich.

Ohne Zahlungen mit Stripe geben Sie in das Feld **Wo man ein Abonnement bekommt** eine der folgenden Möglichkeiten ein:

- eine Seite Ihrer Website, zum Beispiel `/predplatne` – legen Sie sie unter **Inhalt → Seiten** an und beschreiben Sie darauf Preis und Zahlungsweise (Kontonummer, QR-Code),
- einen Zahlungslink, der mit `https://` beginnt.

Eine andere Form der Adresse wird ignoriert. Solange Sie das Feld nicht ausfüllen (und die Zahlungen mit Stripe nicht eingeschaltet haben), wird die Schaltfläche **Abonnement abschließen** nicht angezeigt – weder bei gesperrten Artikeln noch im Leserkonto – und der Leser weiß nicht, wie er Abonnent wird. Der Bildschirm **Einnahmen** weist darauf mit der Meldung **Es ist nicht ausgefüllt, wo Leser ein Abonnement erhalten.** hin.

## Abonnement von Hand eintragen

Von Hand tragen Sie ein Abonnement typischerweise nach Eingang der Zahlung auf dem Konto ein oder wenn Sie es jemandem schenken möchten. Das funktioniert auch mit eingeschalteten Zahlungen mit Stripe: Ein von Hand eingetragenes Datum verkürzt eine Zahlung nie – es gilt das spätere von beiden.

1. Öffnen Sie **Leser → Leser** und suchen Sie den Leser nach seiner E-Mail.
2. Wählen Sie in der Spalte **Abonnement** im Menü **ändern…** eine der Möglichkeiten **+ 1 Monat**, **+ 3 Monate** oder **+ 1 Jahr**. Die Änderung wird sofort gespeichert.
3. Eine Meldung bestätigt das neue Datum: **Das Abonnement gilt bis …**

Die Verlängerung wird ab dem Ende des laufenden Abonnements gerechnet; bei einem Leser ohne Abonnement oder mit abgelaufenem ab heute. Durch wiederholte Auswahl addieren Sie also die Dauer. Die Option **beenden** nimmt das Abonnement weg.

Das Etikett in der Spalte zeigt den Status: **bis** mit Datum, **abgelaufen** oder **keines**. Bei Lesern, die über Stripe zahlen, steht darunter noch der Status des Abonnements aus Stripe (zum Beispiel **Stripe: zahlt**); bei den übrigen mit gültigem Abonnement der Hinweis **manuell eingetragen**. Das Abonnement gilt bis zum Ende des angegebenen Tages. Nach dessen Ablauf verliert der Leser ohne weiteren Eingriff den Zugang zu Artikeln für Abonnenten; das Konto bleibt ihm. Der Leser muss ein Konto haben, bevor Sie ihm das Abonnement eintragen – bitten Sie ihn, sich mit der E-Mail-Adresse zu registrieren, von der er gezahlt hat oder die er bei der Zahlung angegeben hat.

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
- [Zahlungen mit Stripe](platby-stripe.md)
- [Unterstützung und Einnahmen](podpora-a-prijmy.md)
- [SEO](../seo-a-ai/seo.md)
- [Kommentare](../redakce/komentare.md)
