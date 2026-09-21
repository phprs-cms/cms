# Benachrichtigungen im Browser (Web Push)

Der Leser schaltet mit einem Klick die Benachrichtigungen ein und sein Browser weist ihn dann auf jeden neuen Artikel hin – auch wenn er Ihre Website gerade nicht geöffnet hat. Das funktioniert ohne fremden Dienst, ohne Registrierung und ohne personenbezogene Daten.

## Was nötig ist

| Anforderung | Warum |
|---|---|
| Website auf **HTTPS** | Browser erlauben Benachrichtigungen nur gesicherten Websites. |
| PHP-Erweiterungen **openssl** und **curl** | Benachrichtigungen werden mit dem Schlüssel der Website signiert und an die Dienste der Browser gesendet. Ohne sie schaltet sich die Funktion von selbst aus und der Block wird nicht angezeigt. |
| Eingeschaltete Erweiterung **Benachrichtigungen im Browser** | Hauptmenü **Erweiterungen**. In der Standardeinstellung ist sie ausgeschaltet. |
| Block **Benachrichtigungen** auf der Website | Die Schaltfläche, mit der der Leser die Benachrichtigungen einschaltet. |

Sie geben keine Schlüssel ein. Das Schlüsselpaar zum Signieren (VAPID) erzeugt die Website bei der ersten Verwendung selbst.

## Einschalten

1. Öffnen Sie im Hauptmenü **Erweiterungen**, haken Sie **Benachrichtigungen im Browser** an und speichern Sie.
2. Öffnen Sie **Design → Blöcke und Layout** und fügen Sie in einer passenden Zone den Block **Benachrichtigungen** aus der Gruppe **Leser und Redaktion** hinzu.
3. In den Einstellungen des Blocks können Sie die Überschrift ändern. Der Standardtext des Aufrufs lautet **Wir benachrichtigen Sie, sobald ein neuer Artikel erscheint.**
4. Öffnen Sie die Website in einem gewöhnlichen Browserfenster und schalten Sie die Benachrichtigungen selbst ein. Nach der Veröffentlichung des nächsten Artikels prüfen Sie, dass sie ankommen.

## Wie es der Leser sieht

Der Block enthält einen kurzen Text und die Schaltfläche **Benachrichtigungen einschalten**.

1. Der Leser klickt auf die Schaltfläche. Der Browser fragt, ob er der Website Benachrichtigungen erlaubt.
2. Nach der Erlaubnis zeigt der Block **Benachrichtigungen sind in diesem Browser eingeschaltet.** und die Schaltfläche ändert sich in **Benachrichtigungen ausschalten**.
3. Ausschalten lassen sie sich jederzeit mit derselben Schaltfläche oder in den Einstellungen des Browsers.

Weiteres Verhalten:

- In einem Browser, der Benachrichtigungen nicht unterstützt, bleibt der Block verborgen.
- Wenn der Leser die Benachrichtigungen im Browser blockiert hat, rät ihm der Block: **Benachrichtigungen dieser Website sind in Ihrem Browser blockiert. Erlauben Sie sie in den Website-Einstellungen neben der Adressleiste.**
- Das Einschalten gilt für einen Browser auf einem Gerät. Auf dem Telefon und auf dem Computer schaltet der Leser sie getrennt ein.
- Auf iPhone und iPad funktionieren Benachrichtigungen einer Website nur, wenn der Leser die Website zum Home-Bildschirm hinzufügt. Das ist eine Einschränkung des Systems, nicht von phpRS.

## Was gesendet wird und wann

Die Benachrichtigung geht von selbst nach der Veröffentlichung eines Artikels hinaus – sofort beim Veröffentlichen und in dem Augenblick, in dem ein geplanter Artikel erscheint. Sie enthält:

- den Titel des Artikels,
- den Anfang des Vorspanns (höchstens 160 Zeichen),
- das Hauptbild des Artikels, sofern er eines hat,
- das Website-Icon,
- einen Link zum Artikel; ein Klick auf die Benachrichtigung öffnet ihn.

Regeln:

- Jeder Artikel wird einmal gemeldet. Eine spätere Bearbeitung des veröffentlichten Artikels sendet keine neue Benachrichtigung.
- Nicht gemeldet werden Artikel mit der Option zum Ausschluss von Suchmaschinen (noindex) und Artikel, deren Veröffentlichungsdatum älter als zwei Tage ist – nach einem Ausfall wird so nicht das ganze Archiv versendet.
- Gesperrte Artikel werden ebenfalls gemeldet; ein Leser ohne Zugang sieht nach dem Öffnen die Vorschau und die Aufforderung.
- Eine Benachrichtigung lässt sich nicht von Hand schreiben und eine Nachricht ohne Artikel nicht senden.
- Auf einer mehrsprachigen Website erhalten alle Empfänger Benachrichtigungen über Artikel aus allen Sprachversionen.
- Eine neue Benachrichtigung ersetzt auf dem Gerät des Lesers die vorherige, sofern er sie noch nicht weggeklickt hat. Sie stapeln sich nicht.

Versendet wird in Stapeln zu 300 Abonnements im Rahmen der [Hintergrundaufgaben](../provoz/ulohy-na-pozadi.md). Bei einer Website mit Tausenden Empfängern dauert der Versand mehrere Läufe; mit eingerichtetem cron ist er flüssiger.

## Datenschutz

Die Website speichert über ein Abonnement nur die technische Adresse, die ihr der Dienst des Browsers zugeteilt hat (Google, Mozilla, Microsoft, Apple). Sie speichert weder Name noch E-Mail noch IP-Adresse und verbindet das Abonnement nicht mit dem Leserkonto. Benachrichtigungen lassen sich nur an Adressen dieser vier Dienste senden.

Die Nachricht selbst geht ohne Inhalt hinaus. Der Browser des Lesers lädt nach dem Aufwecken Titel und Adresse der letzten Benachrichtigung von Ihrer Website. Die Dienste der Browser sehen so nicht, worüber Sie schreiben.

Ein Abonnement, das erloschen ist – der Leser hat die Benachrichtigungen beendet oder den Browser deinstalliert –, wird beim nächsten Versand von selbst gelöscht. Eine Liste der Abonnements gibt es in der Administration nicht und die Zahl der Empfänger wird nicht angezeigt.

## Wenn Benachrichtigungen nicht ankommen

- Prüfen Sie, dass die Website auf HTTPS läuft und die Erweiterung eingeschaltet ist.
- Der Block wird gar nicht angezeigt: Auf dem Server fehlt `openssl` oder `curl`, oder der Browser unterstützt keine Benachrichtigungen.
- Benachrichtigungen kommen verzögert: Die Hintergrundaufgaben werden nur bei Besuchen der Website gestartet. Richten Sie cron ein.
- Der Server muss ausgehende HTTPS-Verbindungen zu den Diensten der Browser erlauben. Manche Hostings blockieren sie.

## Siehe auch

- [Blöcke und Layout](../vzhled/bloky-a-rozvrzeni.md)
- [Hintergrundaufgaben](../provoz/ulohy-na-pozadi.md)
- [Newsletter](newsletter.md)
- [Planung und Versionen](../psani/planovani-a-revize.md)
