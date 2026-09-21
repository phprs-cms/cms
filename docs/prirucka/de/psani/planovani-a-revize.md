# Planung und Versionen

Ob und wann ein Artikel auf der Website steht, entscheiden zwei Felder im Abschnitt **Veröffentlichung**: **Status** und **Veröffentlichungsdatum**.

## Status eines Artikels

| Status | Was er bedeutet | Wer ihn setzen kann |
|---|---|---|
| **Entwurf – in Arbeit** | am Artikel wird gearbeitet, nur die Redaktion sieht ihn | jeder |
| **Zur Korrektur – fertig, bitte prüfen** | der Autor ist fertig und übergibt den Artikel zur Prüfung | jeder |
| **Freigegeben – wartet auf Veröffentlichung** | geprüft, es fehlt nur noch die Veröffentlichung | wer veröffentlichen darf |
| **Veröffentlicht** | der Artikel steht auf der Website oder erscheint dort zur angegebenen Zeit | wer veröffentlichen darf |

Ein Autor ohne das Recht zum Veröffentlichen hat in der Auswahl nur die ersten beiden Optionen. Wer das Recht zum Veröffentlichen hat, sagt die Seite [Rollen und Berechtigungen](../redakce/role-a-opravneni.md); wie die Redaktion einen Artikel weitergibt, beschreibt [Übergabe und Korrektur](../redakce/predavka-a-korektura.md).

In der Artikelliste entsprechen den Status die Reiter **Alle**, **Veröffentlicht**, **Geplant**, **Entwürfe**, **Zur Korrektur** und **Freigegeben**.

## Geplante Veröffentlichung

1. Stellen Sie den **Status** auf **Veröffentlicht**.
2. Geben Sie im Feld **Veröffentlichungsdatum** ein Datum und eine Uhrzeit in der Zukunft ein.
3. Speichern Sie.

Der Artikel trägt in der Liste die Markierung *geplant* und Sie finden ihn im Reiter **Geplant** und im Redaktionskalender. Auf der Website erscheint er von selbst zur angegebenen Zeit. Bis dahin sieht ihn niemand außer der angemeldeten Redaktion (über die Schaltfläche **Vorschau**).

Das Veröffentlichungsdatum richtet sich nach der Zeitzone der Website. Der Administrator stellt sie unter **Einstellungen → Allgemein → Zeitzone** ein; der Hinweis beim Feld zeigt, wie spät es danach gerade ist.

Die Schaltfläche **Veröffentlichen**, die in der Liste bei einem nicht veröffentlichten Artikel sieht, wer veröffentlichen darf, bewirkt dasselbe wie der Status **Veröffentlicht**: Liegt das Veröffentlichungsdatum in der Zukunft, wird der Artikel geplant, andernfalls erscheint er sofort.

### Die Rolle der Hintergrundaufgaben

Ein geplanter Artikel erscheint zur angegebenen Zeit auch ohne weitere Einstellungen. An die Veröffentlichung schließen sich aber Arbeiten an, die die Hintergrundaufgaben erledigen: das Erneuern der zwischengespeicherten Seiten, damit der Artikel auch auf der Startseite erscheint, und der Versand von Benachrichtigungen. In der Standardeinstellung werden die Aufgaben bei Besuchen der Website ausgeführt. Auf einer Website, die nachts niemand besucht, kann sich ein Morgenartikel deshalb verspäten. Die genaue Zeit sichert ein Cron – das Vorgehen steht auf der Seite [Hintergrundaufgaben](../provoz/ulohy-na-pozadi.md).

### Von der Startseite nehmen

Das Feld **Von der Startseite nehmen** im Abschnitt **Weitere Einstellungen** ist optional. Nach dem angegebenen Datum verschwindet der Artikel von der Startseite; im Ressort und in der Suche bleibt er erhalten.

## Aktualisiert

Bei einem veröffentlichten Artikel steht im Abschnitt **Veröffentlichung** die Option **Als aktualisiert markieren (der Leser sieht „Aktualisiert“ mit dem heutigen Datum)**. Setzen Sie das Häkchen, wenn Sie den Artikel um eine wesentliche neue Information ergänzen, und speichern Sie. Der Leser sieht über dem Text **Aktualisiert** mit Datum und Uhrzeit.

Die Option gilt für ein Speichern. Lassen Sie sie bei der Korrektur eines Tippfehlers leer – das Datum der Aktualisierung ändert sich nicht.

## Versionen

Immer wenn Sie einen Artikel mit geändertem Titel, Vorspann oder Text speichern, wird die vorherige Fassung als Version gesichert. Aufbewahrt werden die letzten 20 Versionen. Änderungen anderer Felder (Ressort, Schlagwörter, Datum) erzeugen keine Version.

Die Versionen finden Sie unten in der Spalte mit den Einstellungen im Abschnitt **Versionsverlauf**. Bei jeder stehen das Datum, der Name dessen, der die Änderung gespeichert hat, und der Link **was sich geändert hat**.

### Versionsvergleich

Der Link **was sich geändert hat** öffnet den Bildschirm **Versionsvergleich**. Er zeigt den Unterschied zwischen der gewählten Version und der aktuellen Fassung getrennt für Titel, Vorspann und Text; hinzugefügter und gelöschter Text ist farblich unterschieden und oben steht ihre Summe. Änderungen an Formatierung und Bildern werden nicht verglichen.

### Eine ältere Version wiederherstellen

1. Klicken Sie im **Versionsverlauf** auf das Datum der Version oder im Vergleich auf **Diese Version in den Editor laden**.
2. Die ältere Fassung wird in den Editor geladen. Oben erscheint der Hinweis, dass sie erst nach dem Speichern gilt.
3. Prüfen Sie den Text und klicken Sie auf **Speichern**. Die bisherige Fassung wird dabei von selbst als weitere Version gesichert, sodass Sie zu ihr zurückkehren können.

Wenn Sie es sich anders überlegen, verlassen Sie das Formular einfach ohne zu speichern.

## Bearbeiten direkt auf der Website

Ein angemeldeter Benutzer, der den Artikel bearbeiten darf, sieht auf dessen Seite auf der Website den Link **Hier bearbeiten**.

1. Klicken Sie auf **Hier bearbeiten**. Anstelle des Artikels öffnet sich in der Website-Vorlage ein Editor mit den Feldern **Titel**, **Vorspann** und **Text**.
2. Bearbeiten Sie den Text und klicken Sie auf **Speichern**. Sie kehren zur Seite des Artikels zurück. **Abbrechen** kehrt ohne Änderung zurück.

Es gelten dieselben Regeln wie in der Administration: Einen veröffentlichten Artikel darf nur ändern, wer das Recht zum Veröffentlichen hat, die vorherige Fassung wird als Version gesichert und der Artikel ist für die Dauer der Bearbeitung gesperrt. Hat ihn gerade ein Kollege geöffnet, wird dessen Name angezeigt und der Editor öffnet sich nicht.

Die übrigen Einstellungen (Ressort, Schlagwörter, Datum) lassen sich so nicht ändern – zu ihnen führt der Link **Alle Einstellungen in der Administration**.

Genauso funktionieren die Seiten der Website (**Inhalt → Seiten**): Den Link **Hier bearbeiten** sieht bei ihnen, wer Zugriff auf den Bereich Seiten hat, und bearbeitet werden Titel und Text. Seiten haben keinen Versionsverlauf.

## Siehe auch

- [Artikeleditor](editor.md)
- [Titelseite und Kalender](../redakce/titulni-strana-a-kalendar.md)
