# KI-Assistent

Der KI-Assistent ist ein optionaler Helfer im Artikeleditor. Er schlägt Titel, Vorspann, Zusammenfassung, Schlagwörter und die Beschreibung für Suchmaschinen vor, liest Korrektur, beschreibt Bilder und kann einen Artikel in eine andere Sprachversion der Website übersetzen. Er schlägt nur vor – von sich aus speichert und veröffentlicht er nichts.

## Einschalten

Der Assistent ist nach der Installation ausgeschaltet. Eingeschaltet wird er vom Administrator:

1. Öffnen Sie **Verwaltung → Erweiterungen** und setzen Sie das Häkchen bei **KI-Assistent im Editor**.
2. Klappen Sie weiter unten den Abschnitt **KI-Assistent – Schlüssel und Modell** auf und fügen Sie in das Feld **Claude API-Schlüssel** Ihren eigenen Schlüssel ein. Sie erstellen ihn auf console.anthropic.com im Bereich API Keys.
3. Wählen Sie im Feld **Modell** eine der drei Möglichkeiten: schnell und sparsam, ausgewogen (empfohlen) oder am gründlichsten.
4. Klicken Sie auf **Einstellungen speichern**.

Die Nutzung bezahlen Sie dem Dienst Anthropic nach dem tatsächlichen Verbrauch. Der Schlüssel wird nur auf Ihrer Website gespeichert und im Formular nicht mehr ausgegeben – angezeigt wird nur sein Ende. Mit der Option **Gespeicherten Schlüssel entfernen** löschen Sie ihn. Ohne Schlüssel erscheinen die Schaltflächen des Assistenten im Editor nicht.

## Was der Assistent kann

Bei den Beschriftungen der Felder im Artikelformular kommen Schaltflächen mit dem Zeichen ✦ hinzu:

| Feld | Schaltfläche | Was Sie erhalten |
|---|---|---|
| **Titel** | **✦ Vorschlagen** | mehrere unterschiedlich angelegte Titel |
| **Vorspann (Einleitung)** | **✦ Vorschlagen** | Varianten des Vorspanns |
| **Artikeltext** | **✦ Korrektur** | eine Liste von Korrekturen zu Rechtschreibung, Tippfehlern, Zeichensetzung und Typografie |
| **Schlagwörter** | **✦ Vorschlagen** | Schlagwörter, bevorzugt aus denen, die die Website schon hat |
| **Kurz gefasst** (in **Weitere Einstellungen**) | **✦ Vorschlagen** | drei bis fünf Punkte mit den wichtigsten Fakten |
| **Beschreibung für Suchmaschinen** (in **Weitere Einstellungen**) | **✦ Vorschlagen** | Varianten einer kurzen Beschreibung |

Der Assistent geht vom Artikeltext aus. Solange der Artikel zu kurz ist, bittet er Sie, zuerst ein Stück zu schreiben.

### Vorschläge

Nach dem Klick öffnet sich ein Fenster mit Vorschlägen. Klicken Sie beim gewählten auf **Übernehmen** – der Vorschlag wird in das Feld eingefügt und Sie können ihn weiter bearbeiten. Vorgeschlagene Schlagwörter werden zu denen hinzugefügt, die schon im Feld stehen. Nichts wird gespeichert, solange Sie den Artikel nicht selbst speichern.

### Korrektur

Die Korrektur zeigt eine Liste von Korrekturen: ursprünglicher Wortlaut, korrigierter Wortlaut und Begründung. Lassen Sie die Korrekturen, die Sie wünschen, angehakt und klicken Sie auf **Ausgewählte korrigieren**. Der Assistent ändert weder Stil noch Fakten noch Bedeutung. Eine Korrektur, deren Abschnitt über eine Formatierung hinwegreicht (ein Teil ist etwa fett), lässt sich nicht anhaken – korrigieren Sie sie von Hand.

### Bildbeschreibungen

Im Abschnitt **Barrierefreiheitsprüfung** steht bei jedem Bild ohne Beschreibung die Schaltfläche **✦**. Der Assistent sieht sich das Bild an und schlägt einen Alternativtext vor. Der Vorschlag wird in das Feld eingefügt; passen Sie ihn nach Bedarf an und bestätigen Sie mit Enter. Das funktioniert nur bei Bildern, die in die Medien hochgeladen wurden.

### Übersetzung des Artikels

Die Übersetzung wird auf einer Website angeboten, auf der die Erweiterung **Sprachversionen der Website** eingeschaltet ist und die mindestens eine weitere Sprachversion hat.

1. Speichern Sie den Artikel in der Standardsprache.
2. Klicken Sie im Abschnitt **Übersetzung des Artikels** bei der gewünschten Sprache auf **Mit dem Assistenten übersetzen** und bestätigen Sie die Rückfrage. Die Übersetzung kann bis zu einer Minute dauern.
3. Es öffnet sich ein neuer Artikel in der Zielsprache. Er ist als Entwurf im Ressort der jeweiligen Sprache angelegt und mit dem Original verknüpft.
4. Lesen Sie die Übersetzung. Der Assistent kann bei Namen, Zahlen und Fachbegriffen Fehler machen. Veröffentlichen Sie den Artikel erst danach.

Übersetzt wird die zuletzt gespeicherte Version: Titel, Vorspann, Text, Kurz gefasst, Fragen und Antworten, Schlüsselwörter sowie Titel und Beschreibung für Suchmaschinen. Formatierung, Bilder und Links bleiben aus dem Original erhalten. Bild, Vorlage, Autor, Schlagwörter und weitere Einstellungen werden übernommen. Übersetzen lässt sich nur ein Artikel in der Standardsprache und in jede Sprache nur einmal; in der Zielsprache muss es ein Ressort geben, in dem Sie schreiben dürfen.

## Was wohin gesendet wird

Ohne Klick auf eine Schaltfläche des Assistenten wird nichts irgendwohin gesendet. Das Schreiben selbst, das Speichern und das automatische Speichern des Zwischenstands haben mit dem Assistenten nichts zu tun.

Beim Klick geht an den Dienst Anthropic (Claude):

- bei Vorschlägen und Korrektur Titel, Vorspann und Text des Artikels in Arbeit,
- bei Schlagwörtern zusätzlich die Liste der Schlagwörter Ihrer Website,
- bei der Bildbeschreibung das jeweilige Bild und der Anfang des Artikels als Kontext,
- bei der Übersetzung die oben aufgezählten gespeicherten Texte des Artikels.

Angaben über Leser, Kommentare und andere Artikel werden nicht gesendet. Verwenden Sie den Assistenten nicht bei Texten, die die Redaktion nicht verlassen dürfen – zum Beispiel bei unbestätigten Informationen von einer geschützten Quelle.

## Einschränkungen

- Jeder Benutzer kann den Assistenten höchstens 60× pro Stunde verwenden. Das ist eine Sicherung gegen unbeabsichtigte Ausgaben.
- Die Verwendung des Assistenten wird im **Änderungsprotokoll** aufgezeichnet (der Administrator sieht es).
- Einen sehr langen Artikel lehnt der Assistent zu übersetzen ab.

## Siehe auch

- [Artikeleditor](editor.md)
- [Inhaltstypen](typy-obsahu.md)
