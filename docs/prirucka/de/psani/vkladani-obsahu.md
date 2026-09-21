# Einbetten von Videos und Beiträgen aus sozialen Netzwerken

Ein Video, einen Podcast oder einen Beitrag aus einem sozialen Netzwerk betten Sie in einen Artikel ein, indem Sie seine Adresse in eine eigene Zeile setzen. Einen Einbettungscode brauchen Sie nicht.

## Vorgehen

1. Kopieren Sie im Browser die Adresse des Videos oder Beitrags.
2. Beginnen Sie im Artikeltext einen neuen Absatz und fügen Sie die Adresse dort ein. Im Absatz darf nichts anderes stehen.
3. Speichern Sie den Artikel. Im Editor bleibt die Adresse sichtbar; der Player oder der Beitrag entsteht daraus erst auf der Website. Das Ergebnis prüfen Sie mit der Schaltfläche **Vorschau**.

Eine Adresse mitten im Satz bleibt gewöhnlicher Text oder ein Link.

## Unterstützte Dienste

| Dienst | Welche Adresse |
|---|---|
| YouTube | Video, Shorts und Livestream (`youtube.com/watch?v=…`, `youtu.be/…`) |
| Vimeo | Adresse des Videos (`vimeo.com/123456789`) |
| Spotify | Episode, Sendung oder Titel (`open.spotify.com/episode/…`) |
| X (Twitter) | einzelner Beitrag (`x.com/konto/status/…`) |
| Instagram | Beitrag, Reel oder Video (`instagram.com/p/…`, `/reel/…`) |
| Facebook | Beitrag, Video oder Foto (`facebook.com/…/posts/…`) |
| TikTok | Video (`tiktok.com/@konto/video/…`) |
| Mastodon | Beitrag auf einem beliebigen Server (`https://server/@konto/1234567890`) |

Adressen anderer Dienste bleiben im Artikel so stehen, wie Sie sie geschrieben haben.

## Geladen wird erst nach dem Klick

Der Leser sieht an der Stelle des Videos zuerst eine Schaltfläche – **Video abspielen**, **Audio abspielen** oder **Beitrag anzeigen von …** – und darunter den Namen des Dienstes, von dem der Inhalt geladen wird. Erst nach dem Klick wird der Inhalt tatsächlich vom fremden Dienst geladen.

Das hat zwei Gründe:

- **Privatsphäre.** Solange der Leser nicht klickt, erfährt der fremde Dienst nichts von seinem Besuch.
- **Geschwindigkeit.** Die Seite wird nicht durch das Laden fremder Player aufgehalten.

Videos von YouTube werden über die Adresse `youtube-nocookie.com` abgespielt. Bei einem Beitrag aus einem Netzwerk bleibt unter der Schaltfläche der Link **Originalbeitrag öffnen**, der auch ohne Klick auf die Schaltfläche funktioniert.

## Eigenes Audio und Video

Eine Datei MP3, M4A, OGG, WAV, MP4 oder WebM laden Sie als Anhang in die Medien hoch. In den Artikel bringen Sie sie dann auf zwei Wegen:

- als **Link zum Herunterladen** – mit der Schaltfläche **Bild** in der Leiste des Editors, siehe [Bilder, Galerien und Anhänge](obrazky-a-galerie.md),
- als **Player über dem Text** – fügen Sie die Adresse der Datei in das Feld **Audio oder Video** im Abschnitt **Podcast, Video, live, Rezension** ein, siehe [Inhaltstypen](typy-obsahu.md).

Eine eigene Datei wird direkt von Ihrer Website abgespielt, deshalb erscheint bei ihr keine Schaltfläche zum Laden.

## Barrierefreiheit

Wenn Sie im Modus **HTML** einen eigenen Frame (`iframe`) einfügen, geben Sie ihm das Attribut `title` mit dem Namen des Inhalts. Die **Barrierefreiheitsprüfung** im Editor meldet einen Frame ohne Titel.

## Siehe auch

- [Artikeleditor](editor.md)
- [Inhaltstypen](typy-obsahu.md)
