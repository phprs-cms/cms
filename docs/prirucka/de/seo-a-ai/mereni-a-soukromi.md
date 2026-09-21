# Webanalyse und Datenschutz

Diese Seite beschreibt zwei Reiter der Einstellungen, die zusammenhängen: **Einstellungen → Webanalyse** (womit Sie die Besuche messen) und **Einstellungen → Datenschutz und Cookies** (ob und wie Sie den Besucher um Einwilligung bitten). Beide verwaltet der Administrator.

Die Grundidee: Was keine Cookies verwendet, läuft sofort und ohne Leiste. Was Cookies verwendet, wartet auf die Einwilligung.

## Integrierte Statistik

Die Option **Integrierte Statistik** im Reiter Webanalyse ist nach der Installation eingeschaltet. Zu ihr gehören die Erweiterung **Statistik** (Hauptmenü **Erweiterungen**; Standard: eingeschaltet) und der Bildschirm **Leser → Statistik**.

Die Messung verwendet keine Cookies und speichert keine IP-Adressen, sie braucht also keine Einwilligung des Besuchers:

- ein Besucher wird an einem Fingerabdruck erkannt, der aus IP-Adresse, Browser und einem Salt gebildet wird, das einen Tag gilt; die IP-Adresse selbst wird nirgends gespeichert,
- die Fingerabdrücke werden nach zwei Tagen gelöscht, ein Leser lässt sich also nicht über die Zeit verfolgen,
- Bots und Artikelvorschauen aus der Administration werden nicht gezählt.

Der Bildschirm **Statistik** zeigt für den gewählten **Zeitraum** (7, 30 oder 90 Tage):

| Angabe | Bedeutung |
|---|---|
| **Besuche** | Zahl der verschiedenen Besucher nach Tagen |
| **Seitenaufrufe** | Zahl der Seitenaufrufe |
| **Seiten pro Besuch** | Verhältnis der beiden Zahlen |
| **Seitenaufrufe und Besuche nach Tagen** | Säulendiagramm; der helle Teil der Säule sind Seitenaufrufe, der dunkle Besuche |
| **Meistgelesene Artikel** | nach Aufrufen im Zeitraum |
| **Woher die Leser kommen** | die 15 häufigsten Websites, von denen die Besucher gekommen sind |

Wenn die Messung ausgeschaltet ist, meldet der Bildschirm **Die Webanalyse ist ausgeschaltet. Sie schalten sie unter Einstellungen → Webanalyse ein.**

## Externe Analyse

Externe Werkzeuge sind freiwillig. Füllen Sie nur die aus, die Sie verwenden.

| Feld | Was einzugeben ist | Einwilligung |
|---|---|---|
| **Google Analytics** | Mess-ID im Format `G-XXXXXXXXXX` | wird erst nach der Einwilligung ausgeführt |
| **Matomo – Adresse** und **Matomo – Website-ID** | Adresse Ihrer Installation und Nummer der Website; gemessen wird nur, wenn beide ausgefüllt sind | wird erst nach der Einwilligung ausgeführt |
| **Plausible – Domain** | Domain der Website, z. B. `example.cz` | verwendet keine Cookies, wird ohne Einwilligung geladen |
| **Eigener Code im Head-Bereich** | beliebiger Code | wird auf jeder Seite unabhängig von der Einwilligung eingefügt – nur für Codes, die keine Cookies speichern |

Matomo, Plausible und der eigene Code stehen im aufklappbaren Abschnitt **Weitere Werkzeuge (Matomo, Plausible, eigener Code)**.

Google Analytics wird mit dem Einwilligungsmodus eingefügt: Solange der Besucher nicht einwilligt, haben alle Speicher den Status „abgelehnt“ und das Messskript wird nicht geladen.

## Cookie-Leiste

Wählen Sie im Reiter **Datenschutz und Cookies** einen von drei Modi:

| Modus | Verhalten |
|---|---|
| **Integriertes Banner** | Standard und empfohlen. Die Leiste wird nur angezeigt, wenn es etwas zu bestätigen gibt. Die Messung startet erst nach der Einwilligung. |
| **Externer Dienst** | Cookiebot, CookieYes, Usercentrics… Sie fügen deren Code ein; die Einwilligung steuert deren Leiste. |
| **Keine** | Mess- und Marketing-Codes werden sofort ausgeführt. Nur wenn Sie die Einwilligung anders lösen. |

### Integriertes Banner

Die Leiste wird nur angezeigt, wenn Sie Google Analytics, Matomo oder **Marketing-Codes** ausgefüllt haben. Eine Website, die nur die integrierte Statistik oder Plausible verwendet, zeigt die Leiste gar nicht an – es gibt nichts zu fragen.

Der Besucher hat die Schaltflächen **Alle akzeptieren**, **Nur notwendige** und **Einstellungen**. In den Einstellungen wählt er die Kategorien:

- **Notwendig – ohne sie funktioniert die Website nicht** (immer eingeschaltet),
- **Statistik – anonyme Besuchermessung** (nur wenn Sie mit einem Werkzeug mit Cookies messen),
- **Marketing – zielgerichtete Werbung** (nur wenn Sie Marketing-Codes haben),

und bestätigt sie mit der Schaltfläche **Auswahl speichern**. Die Auswahl wird für 6 Monate im Cookie `phprs_souhlas` gespeichert. Ändern kann er sie jederzeit über die Schaltfläche **Cookie-Einstellungen**, die auf der Website bleibt.

Was auszufüllen ist:

| Feld | Bedeutung |
|---|---|
| **Text des Banners** | Ein Satz für den Besucher. Standard: „Wir verwenden Cookies zur Messung der Besuche. Sie helfen uns herauszufinden, was die Leser interessiert.“ |
| **Link zur Datenschutzerklärung** | Zum Beispiel `/zasady-ochrany-soukromi`. Die Seite legen Sie unter **Inhalt → Seiten** an. In der Leiste erscheint er als **Mehr Informationen**. |

Die Schaltflächen der Leiste werden von selbst in die Sprache der Website übersetzt; der Text der Leiste ist einer für alle Sprachversionen.

### Codes und Protokollierung

| Feld | Bedeutung |
|---|---|
| **Code eines externen Dienstes** | Skript des Anbieters (bei Cookiebot die Zeile mit `data-cbid`). Es wird als Erstes geladen. Verwendet wird es im Modus Externer Dienst. |
| **Marketing-Codes** | Meta Pixel, Sklik Retargeting, Google Ads… Sie werden erst nach der Einwilligung in Marketing ausgeführt. |
| **Einwilligungen protokollieren** | Standard: eingeschaltet. Speichert Zeit, eine zufällige Kennung und die gewählten Kategorien – ohne IP-Adresse. Ein Nachweis für eine etwaige Prüfung. |

Unter dem Formular steht die Zusammenfassung **Einwilligungen der letzten 30 Tage** nach Kategorien.

Messskripte, die auf die Einwilligung warten, tragen auch Markierungen, die Cookiebot versteht. Im Modus **Externer Dienst** führt sie nach der Einwilligung also dessen Leiste aus. Prüfen Sie bei einem anderen Dienst, dass er die Skripte nach der Einwilligung wirklich freigibt.

Auf die Einwilligung in Marketing warten auch die Codes der Werbenetzwerke aus dem [Anzeigensystem](../ctenari-a-prijmy/reklama.md).

## Welche Cookies das System selbst speichert

| Cookie | Wozu es dient | Wann es entsteht |
|---|---|---|
| `phprs_souhlas`, `phprs_souhlas_id` | Auswahl in der Cookie-Leiste und zufällige Kennung für die Protokollierung | nach der Auswahl in der Leiste |
| `phprs_ctenar` | Anmeldung des Lesers | nach der Anmeldung des Lesers |
| `phprs_cteno` | Zähler der weichen Paywall | nach dem kostenlosen Öffnen eines gesperrten Artikels |
| `phprs_h…`, `phprs_a…` | Merkmal, dass der Leser einen Artikel schon bewertet oder in einer Umfrage abgestimmt hat; gilt 30 Tage | nach der Bewertung oder Abstimmung |
| Sitzungs-Cookie der Administration | Anmeldung der Redaktion | nach der Anmeldung in der Administration |

Das sind technische Cookies. Ein Leser, der die Website nur liest – sich nicht anmeldet, nicht abstimmt und keine Cookie-Leiste sieht –, bekommt kein einziges. Diese Übersicht können Sie als Grundlage für Ihre Datenschutzerklärung verwenden; die rechtliche Beurteilung liegt bei Ihnen.

## Eingebettete Inhalte erst nach Klick

Ein Video von YouTube oder Vimeo, der Player von Spotify und Beiträge aus sozialen Netzwerken, die in einen Artikel eingebettet sind, werden nicht von selbst vom fremden Dienst geladen. Der Leser sieht zuerst eine Schaltfläche mit dem Namen des Dienstes und der Inhalt wird erst nach dem Klick geladen. Solange er nicht klickt, erfährt der fremde Dienst nichts von seinem Besuch und speichert keine Cookies. Dafür muss nichts eingestellt und in der Cookie-Leiste nichts gefragt werden. Einzelheiten stehen auf der Seite [Einbetten von Videos und Beiträgen aus sozialen Netzwerken](../psani/vkladani-obsahu.md).

Eine Ausnahme ist Code, den Sie selbst als HTML einfügen – in einen Artikel oder in den Block **Text**. Er wird sofort geladen. Wenn er Cookies speichert, überlegen Sie, ob er nicht eher zu den **Marketing-Codes** gehört.

## Anfrage eines Lesers zu personenbezogenen Daten

Der Abschnitt **Anfrage eines Lesers zu personenbezogenen Daten** im selben Reiter kann anhand der E-Mail Kommentare, das Newsletter-Abonnement und das Leserkonto herunterladen oder löschen. Das Vorgehen steht auf der Seite [Leserkonten](../ctenari-a-prijmy/ucty-ctenaru.md).

## Siehe auch

- [SEO](seo.md)
- [Einbetten von Videos und Beiträgen aus sozialen Netzwerken](../psani/vkladani-obsahu.md)
- [Werbung](../ctenari-a-prijmy/reklama.md)
- [Sicherheit](../provoz/bezpecnost.md)
