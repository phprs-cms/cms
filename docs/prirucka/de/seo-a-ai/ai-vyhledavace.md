# KI-Suchmaschinen

Ein Teil der Leser sucht heute nicht in einer Suchmaschine, sondern fragt einen Assistenten – ChatGPT, Claude, Perplexity, Gemini. Diese Assistenten durchsuchen das Web ähnlich wie Suchmaschinen und verweisen in ihren Antworten auf Quellen. Die Optimierung für sie heißt GEO; deshalb heißt der Reiter in den Einstellungen **SEO und GEO**.

phpRS gibt dem Herausgeber zwei Dinge: die Entscheidung, ob er KI-Bots auf die Website lässt, und – falls ja – Grundlagen, aus denen sie die Website leicht und richtig lesen. Alle Optionen stehen unter **Einstellungen → SEO und GEO**.

## Erlauben oder verbieten

Das Feld **KI-Suchmaschinen und Assistenten** im Abschnitt **Sichtbarkeit der Website**:

| Option | Was sie bewirkt |
|---|---|
| **erlauben – Inhalte können in KI-Antworten mit Link zur Website erscheinen** | Standard. Die Bots der KI-Dienste haben dieselben Regeln wie die übrigen. |
| **verbieten – ChatGPT, Claude, Perplexity, Gemini und weitere** | In `robots.txt` wird ein Verbot der ganzen Website für bekannte KI-Bots eingetragen. |

Das Verbot betrifft diese Bots: GPTBot, OAI-SearchBot, ChatGPT-User, ClaudeBot, Claude-User, anthropic-ai, PerplexityBot, Perplexity-User, Google-Extended, Applebot-Extended, CCBot, Bytespider, Amazonbot, meta-externalagent und cohere-ai. Die Liste ist Teil des Systems; einen anderen Bot ergänzen Sie mit einer eigenen Regel (siehe unten).

Drei Dinge, die man wissen sollte:

- **Es handelt sich nicht um einen technischen Schutz.** `robots.txt` ist eine Bitte. Seriöse Bots halten sich an die Regel; ein Bot, der sie ignoriert, gelangt weiterhin auf die Website.
- **Die gewöhnliche Suche ändert sich nicht.** Google-Extended und Applebot-Extended steuern nur die Nutzung der Inhalte für KI. Die Bots der gewöhnlichen Suche, Googlebot und Bingbot, stehen nicht in der Liste.
- **Das Verbot gilt nicht rückwirkend.** Was die Dienste früher gelesen haben, wird dadurch nicht aus ihnen entfernt.

Wie Sie sich entscheiden, ist eine redaktionelle und geschäftliche Frage. Das Erlauben bringt Erwähnungen und Links in KI-Antworten. Das Verbot ist sinnvoll für einen Herausgeber, der nicht möchte, dass seine Texte zum Training von Modellen verwendet werden oder dass eine KI-Antwort den Besuch der Website ersetzt. Bezahlter Inhalt ist unabhängig von dieser Option geschützt – aus einem gesperrten Artikel bekommt ein Bot nur die Vorschau, siehe [Gesperrte Inhalte und Abonnement](../ctenari-a-prijmy/zamceny-obsah.md).

Eigene Regeln, zum Beispiel das Verbot eines einzelnen Bots, schreiben Sie in das Feld **Eigene Regeln für robots.txt** im Abschnitt **Für Fortgeschrittene**:

```
User-agent: Bytespider
Disallow: /
```

## Datei llms.txt

Die Option **Datei llms.txt** (Standard: eingeschaltet) stellt unter der Adresse `/llms.txt` einen Wegweiser durch die Website für Sprachmodelle nach dem Vorschlag von llmstxt.org bereit. Es ist reiner Text im Format Markdown, den das System selbst zusammenstellt:

- der Name der Website und die **Beschreibung der Website** aus **Einstellungen → Allgemein**,
- die Liste der angezeigten Ressorts mit Links und Beschreibungen,
- die 30 neuesten Artikel mit Link und Anfang des Vorspanns (ohne Artikel mit der Option **Vor Suchmaschinen verbergen (noindex)**).

Nichts darin pflegen Sie von Hand. Es lohnt sich aber, eine treffende **Beschreibung der Website** und Beschreibungen der Ressorts zu haben – das sind die Sätze, nach denen sich ein Modell ein erstes Bild von der Website macht.

Auf einer mehrsprachigen Website hat jede Version ihre eigene Datei (`/en/llms.txt`) mit ihren Ressorts und Artikeln. Ist auch die bereinigte Version der Artikel eingeschaltet, führen die Links in der Datei gleich dorthin.

Nach dem Ausschalten der Option existiert die Adresse `/llms.txt` nicht mehr.

## Bereinigte Version der Artikel (.md)

Die Option **Bereinigte Version der Artikel (.md)** (Standard: eingeschaltet) macht jeden veröffentlichten Artikel auch als reinen Text im Format Markdown zugänglich. Die Adresse entsteht durch Anhängen von `.md` an die Adresse des Artikels:

```
https://www.example.cz/clanek/muj-clanek
https://www.example.cz/clanek/muj-clanek.md
```

Die bereinigte Version enthält:

- den Titel,
- Autor, Datum der Veröffentlichung und der Aktualisierung, Ressort und einen Link zum ursprünglichen Artikel als Quelle,
- die Punkte aus **Kurz gefasst**, sofern der Artikel sie hat,
- Vorspann und Text, in Markdown umgewandelt.

Sie enthält weder Navigation noch Blöcke, Werbung, Kommentare oder Skripte. Das Modell bekommt so den bloßen Text mit Quellenangabe und muss ihn nicht aus der HTML-Seite heraussuchen.

Weitere Eigenschaften:

- Die Seite des Artikels verweist im Kopf auf die bereinigte Version (`rel="alternate"`, Typ `text/markdown`), Werkzeuge finden sie also von selbst.
- Die bereinigte Version wird mit dem Header `X-Robots-Tag: noindex` gesendet. In den Ergebnissen der gewöhnlichen Suche erscheint sie nicht und es entsteht kein doppelter Inhalt.
- Ein gesperrter Artikel hat in der bereinigten Version nur Vorspann und Vorschau, genauso wie auf der Website.
- Kurze Meldungen haben keine bereinigte Version.

Die bereinigte Version nützt auch Menschen: zur Archivierung, zur Übernahme des Textes durch eine Partner-Website oder zum Lesen im Terminal.

## Was beim Schreiben hilft

Ein übersichtlich gegliederter Text mit genanntem Autor und Datum lässt sich maschinell leichter lesen. Der Editor bietet dafür drei Werkzeuge; alle beschreibt das Kapitel Schreiben:

- **Kurz gefasst** – drei bis fünf Punkte mit den wichtigsten Fakten. Sie erscheinen über dem Artikel und gehen auch in die bereinigte Version ein.
- **Fragen und Antworten** – sie werden unter dem Artikel ausgegeben und gehen als `FAQPage` in die strukturierten Daten ein.
- **Als aktualisiert markieren** – der Leser sieht beim Artikel „Aktualisiert“ mit Datum; das Änderungsdatum wird auch in die strukturierten Daten übernommen.

Die strukturierten Daten (Autor, Herausgeber, Datumsangaben, Brotkrumen-Navigation) ergänzt das System selbst – siehe [SEO](seo.md).

## Öffentliche API

Für das maschinelle Lesen der Inhalte durch eine eigene Anwendung dient die Erweiterung **Öffentliche API** (**Verwaltung → Erweiterungen**; Standard: ausgeschaltet). Es ist eine lesende JSON-API unter den Adressen `/api/clanky`, `/api/clanky/<adresa>` und `/api/rubriky`. Gesperrte Artikel gibt sie mit Vorschau und dem Kennzeichen `zamceno` zurück.

## Siehe auch

- [SEO](seo.md)
- [Verbindung mit Claude](napojeni-na-claude.md)
- [Artikeleditor](../psani/editor.md)
- [Gesperrte Inhalte und Abonnement](../ctenari-a-prijmy/zamceny-obsah.md)
