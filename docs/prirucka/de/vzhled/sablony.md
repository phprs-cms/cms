# Website-Vorlagen

Die Vorlage bestimmt, wie die Website aussieht: Kopf, Artikelliste, Gestalt des Artikels, Fuß. Mit dem System kommen drei Vorlagen. Inhalt, Blöcke und Einstellungen hängen nicht von der Vorlage ab, Sie können sie also jederzeit austauschen.

## Drei eingebaute Vorlagen

| Vorlage | Für wen sie ist | Layout nach der Auswahl |
|---|---|---|
| **Classic Newspaper** | Seriöse Tageszeitung: Serifen-Überschriften, feine Linien, Aufmacher und Spaltensatz. | 2 Spalten |
| **Modern Magazine** | Markantes Online-Magazin: schwarze Leiste, große Überschriften und Fotos, Kartenraster. | Volle Breite |
| **Minimal** | Persönliches Magazin, Blog oder Newsletter-Website: eine schmale Spalte und ruhige Typografie. | 1 Spalte |

Eine neue Installation verwendet Classic Newspaper, sofern Sie im Installationsprogramm keine andere gewählt haben. Keine der Vorlagen lädt Schriften oder Skripte von fremden Servern.

## Vorlage wechseln

Die Vorlage ändert der Administrator.

1. Öffnen Sie **Design → Website-Identität**.
2. Klicken Sie im Abschnitt **Vorlage** auf die Karte der Vorlage.
3. Klicken Sie auf **Speichern**. Das Ergebnis sehen Sie sich über den Link **Website anzeigen** an.

Die Änderung gilt sofort für alle Leser.

### Was der Wechsel ändert und was nicht

Zusammen mit der Vorlage wird das Seitenlayout eingestellt, das zu ihr gehört (siehe Tabelle oben). Feiner stimmen Sie es im Bereich **Blöcke und Layout** ab. Blöcke aus einer Zone, die das neue Layout nicht hat, werden dabei genauso verschoben wie bei einer manuellen Änderung des Layouts (siehe [Seitenlayout](#seitenlayout)).

Unverändert bleiben:

- der gesamte Inhalt – Artikel, Seiten, Ressorts, Medien, Kommentare,
- die Blöcke und ihre Einstellungen,
- Logo, Website-Icon, Hauptfarbe, Schriften und Dunkelmodus aus der [Website-Identität](identita-webu.md),
- die Artikelvorlagen (Longread, Fotoreportage, Interview) und die Inhaltstypen – sie funktionieren in allen drei Vorlagen,
- die Adressen der Seiten, SEO, Webanalyse und Cookie-Leiste.

Eine Hauptfarbe, die auf **Farbe der Vorlage** steht, ändert sich mit der neuen Vorlage, weil jede Vorlage ihre eigene Standardfarbe hat. Dasselbe gilt für die Schrift **Wie in der Vorlage**.

## Vorschau einer anderen Vorlage

Der Administrator kann sich eine Vorlage ansehen, ohne sie einzuschalten. Es genügt, in der Administration angemeldet zu sein und an die Adresse der Website den Parameter `sablona` mit dem Namen des Vorlagenordners anzuhängen:

```
https://www.example.cz/?sablona=modern-magazine
https://www.example.cz/clanek/muj-clanek?sablona=minimal
```

Die Ordner der eingebauten Vorlagen heißen `classic-newspaper`, `modern-magazine` und `minimal`. Genauso sehen Sie sich auch eine [eigene Vorlage](vlastni-sablona.md) an. Die Vorschau sehen nur Sie; bei einem nicht angemeldeten Leser und bei einem Redakteur wirkt der Parameter nicht. Er gilt für eine einzige Seite – nach dem Klick auf einen Link sind Sie wieder in der eingestellten Vorlage, der Parameter muss also erneut angehängt werden.

## Seitenlayout

Das Layout legt fest, wie viele Spalten die Seite hat und welche Zonen für Blöcke es darin gibt. Es gilt für die ganze Website.

| Layout | Beschreibung | Zonen |
|---|---|---|
| **3 Spalten** | Blöcke links und rechts, Inhalt in der Mitte. | Kopfzeile, Linke Spalte, Über dem Inhalt, Unter dem Inhalt, Rechte Spalte, Fußzeile |
| **2 Spalten** | Inhalt und rechts eine schmale Spalte mit Blöcken. | Kopfzeile, Über dem Inhalt, Unter dem Inhalt, Rechte Spalte, Fußzeile |
| **1 Spalte** | Schmale Spalte für angenehmes Lesen, Blöcke unter dem Inhalt. | Kopfzeile, Über dem Inhalt, Unter dem Inhalt, Fußzeile |
| **Volle Breite** | Inhalt über die gesamte Seitenbreite, Blöcke unter dem Inhalt. | Kopfzeile, Über dem Inhalt, Unter dem Inhalt, Fußzeile |

Das Layout ändern Sie im visuellen Blockeditor: **Design → Blöcke und Layout**, in der oberen Leiste die Schaltflächen bei der Beschriftung **Layout:**. Das Vorgehen beschreibt die Seite [Blöcke und Layout](bloky-a-rozvrzeni.md).

Wenn das neue Layout eine Spalte nicht hat, werden die Blöcke daraus verschoben:

- beim Wechsel zu **2 Spalten** aus der linken Spalte in die rechte,
- beim Wechsel zu **1 Spalte** oder **Volle Breite** aus beiden Spalten unter den Inhalt.

Verschobene Blöcke reihen sich hinter denen ein, die in der Zielzone schon stehen.

### Welche Vorlage welches Layout unterstützt

| Layout | Classic Newspaper | Modern Magazine | Minimal |
|---|---|---|---|
| 3 Spalten | ja | ja | ohne Spalten – alles in einer Spalte |
| 2 Spalten | ja | ja | ohne Spalten – alles in einer Spalte |
| 1 Spalte | ja (Inhalt bis 760 px) | ja (Inhalt bis 820 px) | ja |
| Volle Breite | ja | ja | eine schmale Spalte |

Minimal ist immer einspaltig. Wenn Sie darin ein Layout mit Spalten wählen, gibt es die Zonen **Linke Spalte** und **Rechte Spalte** weiterhin, sie werden aber nicht neben dem Inhalt dargestellt – ihre Blöcke erscheinen in derselben schmalen Spalte, durch eine Linie getrennt. Zu Minimal passt deshalb das Layout **1 Spalte**, das sich mit ihr von selbst einstellt.

In den Vorlagen Classic Newspaper und Modern Magazine erscheinen die Seitenspalten nur auf einem breiten Bildschirm. Auf dem Telefon und einem schmaleren Tablet wird alles untereinander angeordnet: zuerst der Inhalt, darunter die Blöcke aus der linken und der rechten Spalte. Eine Spalte, in der kein Block steht, nimmt keinen Platz ein – der Inhalt dehnt sich aus.

## Wenn der Ordner der Vorlage fehlt

Wenn die eingestellte Vorlage im Ordner `layout/` nicht vorhanden ist (zum Beispiel weil Sie sie gelöscht haben), wird die Website mit der Vorlage Classic Newspaper dargestellt. Wählen Sie dann in der **Website-Identität** eine andere.

## Siehe auch

- [Website-Identität](identita-webu.md)
- [Blöcke und Layout](bloky-a-rozvrzeni.md)
- [Eigene Vorlage](vlastni-sablona.md)
- [Inhaltstypen](../psani/typy-obsahu.md)
