# Bearbeiten direkt auf der Website

Einen Tippfehler in einem veröffentlichten Artikel oder einen veralteten Satz auf der Seite Über uns korrigieren Sie gleich dort, wo er Ihnen aufgefallen ist. Die Schaltfläche **Hier bearbeiten** öffnet den Text in der Website-Vorlage im selben Editor, den Sie aus der Administration kennen. Sie müssen den Artikel nicht in der Liste suchen.

## Wer die Schaltfläche sieht

Die Schaltfläche **Hier bearbeiten** steht rechts unten auf der Seite eines Artikels und auf einer eigenständigen Seite (Über uns, Kontakt…). Sie sieht nur, wer in der Administration angemeldet ist und den jeweiligen Text bearbeiten darf:

| Inhalt | Wer darf |
|---|---|
| Seite | wer Zugriff auf den Bereich **Seiten** hat (Redakteur, Administrator) |
| Veröffentlichter Artikel | wer Zugriff auf den Bereich **Artikel** hat, veröffentlichen darf und den Artikel zu denen zählt, die er verwaltet |

Für Artikel gelten dieselben Regeln wie in der Administration. Ein Autor ohne das Recht zum Veröffentlichen darf einen veröffentlichten Artikel nicht ändern, er sieht die Schaltfläche dort also nicht. Ein auf bestimmte Ressorts beschränkter Benutzer sieht sie nur bei Artikeln aus seinen Ressorts. Einzelheiten stehen auf der Seite [Rollen und Berechtigungen](../redakce/role-a-opravneni.md).

Die Leser sehen die Schaltfläche nie. In Artikellisten, auf der Seite eines Ressorts und auf der Startseite gibt es die Schaltfläche nicht – nur bei einem einzelnen Artikel oder einer Seite.

Das Bearbeiten auf der Website ist vor allem für veröffentlichte Inhalte gedacht. Es funktioniert aber auch in der Vorschau eines Entwurfs (Schaltfläche **Vorschau** im Artikeleditor): **Hier bearbeiten** und die Rückkehr nach dem Speichern bleiben in der Vorschau.

## Vorgehen

1. Melden Sie sich in der Administration an und öffnen Sie auf der Website einen Artikel oder eine Seite.
2. Klicken Sie auf **Hier bearbeiten**. Anstelle des Textes erscheint ein Formular mit dem Editor.
3. Bearbeiten Sie den Text.
4. Klicken Sie auf **Speichern**. Sie kehren auf dieselbe Seite zurück und sehen das Ergebnis. **Abbrechen** kehrt ohne Änderung zurück.

## Was sich ändern lässt

| Inhalt | Felder |
|---|---|
| Artikel | **Titel**, **Vorspann**, **Text** |
| Seite | **Titel**, **Text** |

Der Editor hat dieselben Werkzeuge wie in der Administration einschließlich des Einfügens von Bildern aus den Medien. Der Titel darf nicht leer bleiben – sonst kommt das Formular mit der Meldung **Der Titel darf nicht leer bleiben.** zurück.

Alles andere – Ressort, Schlagwörter, Hauptbild, Datum, Status, Einstellungen für Suchmaschinen, Adresse der Seite – ändern Sie über den Link **Alle Einstellungen in der Administration**, der das vollständige Formular öffnet. Nicht gespeicherte Änderungen aus dem Editor auf der Website werden dabei nicht übernommen; speichern Sie zuerst.

Das Bearbeiten auf der Website ist kein Seitenbaukasten. Layout, Spalten und die Blöcke rund um den Inhalt lassen sich damit nicht ändern. Die Blöcke haben einen eigenen Editor – siehe [Blöcke und Layout](bloky-a-rozvrzeni.md).

## Sperre des Artikels

Zwei Personen können denselben Artikel nicht gleichzeitig überschreiben. Die Sperre ist mit der Administration gemeinsam:

- mit dem Öffnen des Editors sperren Sie den Artikel für sich; solange Sie ihn geöffnet haben, verlängert sich die Sperre von selbst,
- ein Kollege, der den Artikel zu öffnen versucht (auf der Website oder in der Administration), sieht anstelle des Editors die Meldung **… hat diesen Text gerade geöffnet. Versuchen Sie es gleich noch einmal.**,
- mit dem Speichern wird die Sperre aufgehoben; nach dem Schließen des Fensters ohne Speichern läuft sie innerhalb von drei Minuten von selbst ab.

Seiten haben keine Sperre.

## Versionen und Änderungsprotokoll

Das Speichern eines Artikels, bei dem sich Titel, Vorspann oder Text geändert haben, legt eine Version an, genauso wie das Speichern in der Administration. Zum vorherigen Wortlaut kehren Sie im vollständigen Artikelformular zurück – siehe [Planung und Versionen](../psani/planovani-a-revize.md). Seiten haben keine Versionen.

Jedes Speichern wird im **Änderungsprotokoll** als „Bearbeitung direkt auf der Website“ mit dem Titel des Artikels oder der Seite vermerkt. Das Protokoll sieht der Administrator unter **Verwaltung → Änderungsprotokoll**.

Nach dem Speichern eines veröffentlichten Artikels wird außerdem:

- die Suche auf der Website aktualisiert,
- der Seiten-Cache gelöscht, sodass die Leser die Korrektur sofort sehen,
- eine Benachrichtigung an die Suchmaschinen über IndexNow gesendet, sofern Sie es eingeschaltet haben (siehe [SEO](../seo-a-ai/seo.md)).

Das Veröffentlichungsdatum ändert sich nicht und die Benachrichtigung an die Leser (Web Push, Webhook) wird nicht erneut gesendet.

## Siehe auch

- [Artikeleditor](../psani/editor.md)
- [Planung und Versionen](../psani/planovani-a-revize.md)
- [Blöcke und Layout](bloky-a-rozvrzeni.md)
- [Rollen und Berechtigungen](../redakce/role-a-opravneni.md)
