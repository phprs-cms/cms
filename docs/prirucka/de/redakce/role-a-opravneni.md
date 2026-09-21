# Rollen und Berechtigungen

Jedes Mitglied der Redaktion hat ein eigenes Konto. Konten legt der Administrator unter **Verwaltung → Benutzer** an und verwaltet sie dort. Was jemand in der Administration sieht und darf, bestimmen seine Rolle und einige ergänzende Optionen.

## Drei Rollen

| Rolle | Was sie tut |
|---|---|
| **Autor** | Schreibt und bearbeitet eigene Artikel. Ein Redakteur veröffentlicht sie. |
| **Redakteur** | Bearbeitet und veröffentlicht die Artikel aller, verwaltet Ressorts, Kommentare und weitere Inhalte. |
| **Administrator** | Alles einschließlich Benutzern, Design und Einstellungen der Website. |

## Was welche Rolle sieht

Das Hauptmenü zeigt nur die Bereiche, auf die der Benutzer Zugriff hat. **Übersicht**, **Mein Konto** und **Medien** haben alle.

| Bereich | Autor | Redakteur | Administrator |
|---|---|---|---|
| Artikel | ja | ja | ja |
| Medien | ja | ja | ja |
| Ressorts, Schlagwörter und Themenseiten, Seiten | – | ja | ja |
| Kommentare, Statistik und weitere eingeschaltete Inhaltserweiterungen | – | ja | ja |
| Blöcke und Layout | – | ja | ja |
| Website-Identität, Benutzer, Weiterleitungen, Änderungsprotokoll, Erweiterungen, Einstellungen, Leser | – | – | ja |

Ein Bereich, der zu einer ausgeschalteten Erweiterung gehört, wird niemandem angezeigt.

Unter **Artikel** sieht ein Autor nur seine Artikel; Redakteur und Administrator sehen alle. Dasselbe gilt für den Redaktionskalender, die Übersicht und die Artikelsuche in der Befehlspalette. In den **Medien** sehen alle alles, aber die Beschreibung ändern und eine Datei löschen darf nur, wer sie hochgeladen hat, und der Administrator.

## Das Recht zum Veröffentlichen

Redakteur und Administrator veröffentlichen immer. Ein Autor darf nicht veröffentlichen, solange ihm der Administrator nicht die Option **darf eigene Artikel selbst veröffentlichen** anhakt.

Wer das Recht zum Veröffentlichen nicht hat:

- hat im Feld **Status** nur **Entwurf – in Arbeit** und **Zur Korrektur – fertig, bitte prüfen**,
- kann einen bereits veröffentlichten Artikel weder ändern noch löschen,
- entscheidet nicht über die Startseite (die Optionen **Auf der Startseite anzeigen** und **Oben anheften (Aufmacher)** sieht er nicht) und hat den Bildschirm **Titelseite** nicht.

Wie ein Artikel vom Autor zur Veröffentlichung gelangt, beschreibt [Übergabe und Korrektur](predavka-a-korektura.md).

## Neuer Benutzer

1. **Verwaltung → Benutzer → Neuer Benutzer.**
2. Füllen Sie **Vor- und Nachname** (erscheint bei den Artikeln), **Benutzername** und **E-Mail** aus. Der Benutzername hat 2–40 Zeichen: Buchstaben ohne Umlaute und Akzente, Ziffern, Punkt, Bindestrich und Unterstrich. Ohne E-Mail-Adresse erhält der Benutzer keine Benachrichtigungen und kann ein vergessenes Passwort nicht zurücksetzen.
3. Geben Sie ein **Passwort** mit mindestens 10 Zeichen ein. Der Benutzer ändert es dann unter **Mein Konto**.
4. Wählen Sie die **Rolle** und klicken Sie auf **Benutzer hinzufügen**.

## Detaillierte Einstellungen

Der aufklappbare Abschnitt **Detaillierte Einstellungen** im Benutzerformular legt genauer fest, was die Rolle erlaubt.

### Zugriff auf Bereiche

In der Standardeinstellung ergibt sich der Zugriff aus der Rolle. Mit dem Häkchen bei **manuell festlegen (sonst nach Rolle)** wählen Sie die Bereiche einzeln aus – einem Autor können Sie so etwa die Kommentare hinzufügen, einem Redakteur Blöcke und Layout wegnehmen. Bereiche, die dem Administrator vorbehalten sind, stehen nicht in der Liste und lassen sich nicht hinzufügen.

### Nur diese Ressorts

Ist nichts angehakt, darf der Benutzer in allen Ressorts schreiben. Mit einem Häkchen sieht und bearbeitet er nur Artikel aus den gewählten Ressorts. Die Beschränkung vererbt sich auf Unterressorts, auch auf solche, die später entstehen. In ein anderes Ressort kann er einen Artikel weder speichern noch per Sammelaktion verschieben. Ein Administrator lässt sich nicht beschränken.

### Fremde Artikel bearbeiten

Die Option **Darf auch Artikel von Autoren bearbeiten** ist für die Rolle Autor gedacht – zum Beispiel für einen Ressortleiter. Indem Sie Kollegen anhaken, machen Sie ihm deren Artikel zugänglich: Er sieht sie in der Liste, kann sie bearbeiten und im Feld **Autor** zwischen diesen Autoren wählen. Das Recht zum Veröffentlichen erhält er dadurch nicht.

### Ein Konto sperren

**Konto sperren → Benutzer kann sich nicht anmelden.** Ein gesperrter Benutzer wird sofort abgemeldet, auch aus laufender Arbeit. Seine Artikel bleiben mit seinem Namen gekennzeichnet. Das eignet sich für einen Kollegen, der die Redaktion verlassen hat. In der Benutzerliste trägt ein gesperrtes Konto den Hinweis *gesperrt*.

**Löschen** dagegen entfernt das Konto; die Artikel bleiben erhalten, aber ohne Autor.

Sein eigenes Konto kann ein Administrator weder sperren noch löschen noch ihm die Rolle Administrator entziehen.

### Zwei-Faktor-Anmeldung

Hat ein Benutzer die Zwei-Faktor-Anmeldung eingeschaltet, steht bei ihm in der Liste die Markierung **2FA**. Verliert er Telefon und Ersatzcodes, schaltet der Administrator sie ihm mit der Option **deaktivieren (Benutzer hat Telefon und Backup-Codes verloren)** aus. Damit werden auch seine Passkeys gelöscht. Einzelheiten stehen auf der Seite [Konto und Anmeldung](ucet-a-prihlaseni.md).

## Benutzerliste

Die Tabelle zeigt Benutzername, Name, E-Mail, Rolle, die Spalte **Darf veröffentlichen** (Ja/Nein), die Zahl der Artikel und die letzte Anmeldung.

## Empfehlungen

- Jede Person hat ein eigenes Konto. Bei einem geteilten Konto lässt sich nicht nachvollziehen, wer was geändert hat.
- Vergeben Sie die geringsten nötigen Rechte und halten Sie die Zahl der Administratoren so klein wie möglich.
- Weitere Grundsätze fasst die Seite [Sicherheit](../provoz/bezpecnost.md) zusammen.
