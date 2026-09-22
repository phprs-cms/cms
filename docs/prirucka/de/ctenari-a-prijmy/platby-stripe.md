# Zahlungen mit Stripe

Mit dem Dienst Stripe bezahlt der Leser sein Abonnement selbst mit Karte, und die Website schaltet es ihm von selbst ein, verlängert es und lässt es nach einer Kündigung auslaufen. Sie müssen weder den Kontoauszug überwachen noch etwas von Hand eintragen.

Die Zahlung läuft auf den Seiten von Stripe ab, nicht auf Ihrer Website. phpRS sieht die Kartennummer nie und speichert sie nicht. Das Eintragen eines Abonnements von Hand aus [Gesperrte Inhalte und Abonnement](zamceny-obsah.md) funktioniert neben den Zahlungen weiter – es eignet sich für Zahlungen per Überweisung oder ein Geschenkabonnement.

Sie brauchen die Erweiterung **Leser und gesperrter Inhalt**, ein Konto bei [stripe.com](https://stripe.com) und eine Website unter einer Adresse mit `https://`. Unter **Einstellungen → Allgemein** muss die **Adresse der Website** richtig ausgefüllt sein – aus ihr werden die Adressen gebildet, auf die Stripe den Leser nach der Zahlung zurückschickt.

## Wie es funktioniert

1. Ein angemeldeter Leser klickt in seinem Konto auf **Monatlich abonnieren** oder **Jährlich abonnieren**.
2. Die Website leitet ihn auf die Zahlungsseite von Stripe weiter (Checkout). Dort gibt er die Karte ein und bezahlt.
3. Stripe sendet Ihrer Website eine signierte Nachricht (Webhook), dass die Zahlung erfolgt ist. Die Website prüft die Nachricht und trägt dem Leser das Abonnement ein.
4. Vor dem Ende des Zeitraums bucht Stripe die nächste Zahlung selbst ab und die Website verlängert das Abonnement.
5. Mit der Schaltfläche **Abonnement verwalten** gelangt der Leser ins Kundenportal von Stripe. Dort ändert er die Karte, lädt Belege herunter oder kündigt das Abonnement.

Ein Abonnement wird nur nach einer geprüften Nachricht von Stripe eingetragen, nie nach dem, was der Browser des Lesers sendet. Der Leser kehrt nach dem Bezahlen deshalb mit der Meldung auf die Website zurück, dass das Abonnement in Kürze aktiviert wird – die Nachricht von Stripe trifft gewöhnlich innerhalb weniger Sekunden ein.

Die Schaltfläche **Abonnement abschließen** bei gesperrten Artikeln führt bei eingeschalteten Zahlungen ins Leserkonto. Ein nicht angemeldeter Leser meldet sich zuerst an oder registriert sich (die Registrierung ist weiterhin ohne Passwort, per Link aus der E-Mail), die Schaltflächen für die Zahlung findet er gleich nach der Anmeldung. Die Adresse aus dem Feld **Wo man ein Abonnement bekommt** wird in diesem Fall nicht verwendet.

## Einrichtung Schritt für Schritt

Probieren Sie alles zuerst im Testmodus von Stripe aus (siehe den Abschnitt Probelauf unten). Das Vorgehen ist in beiden Modi gleich, es unterscheiden sich nur die Schlüssel.

### 1. Produkt und Preise

1. Öffnen Sie in Stripe **Product catalog** und legen Sie ein Produkt an, zum Beispiel „Abonnement des Magazins“.
2. Fügen Sie ihm einen wiederkehrenden Preis (**Recurring**) mit dem Zeitraum **Monthly** hinzu, gegebenenfalls einen zweiten mit dem Zeitraum **Yearly**. Einer von beiden genügt.
3. Kopieren Sie bei jedem Preis seine ID (**Copy price ID**). Sie beginnt mit `price_`.

Preise, Währung, Mehrwertsteuer und Probezeitraum verwalten Sie nur in Stripe. phpRS legt keine Preise an und ändert keine – den Betrag berechnet Stripe nach dem Preis, dessen ID Sie eingeben.

### 2. Eingeschränkter Schlüssel

Legen Sie unter **Developers → API keys** einen eingeschränkten Schlüssel an (**Create restricted key**). Er beginnt mit `rk_`. Geben Sie ihm nur diese Rechte, alle übrigen lassen Sie auf **None**:

| Berechtigung in Stripe | Stufe | Wozu sie dient |
|---|---|---|
| **Checkout Sessions** | Write | Anlegen der Zahlung für das Abonnement |
| **Customer portal** | Write | Link in die Abonnementverwaltung |
| **Subscriptions** | Read | Laden des Status nach der Rückkehr aus der Abonnementverwaltung |

Die Website macht nur diese drei Aufrufe. Sollte Stripe einen davon wegen eines fehlenden Rechts ablehnen, sieht der Leser eine allgemeine Entschuldigung und den genauen Namen des fehlenden Rechts finden Sie unter **Einstellungen → Systemstatus** im Fehlerprotokoll. Verwenden lässt sich auch der vollständige geheime Schlüssel (`sk_…`), aber der eingeschränkte ist sicherer: Sollte er abfließen, lassen sich damit weder Geld erstatten noch Kundendaten lesen.

### 3. Webhook

1. Öffnen Sie in der Administration von phpRS **Einstellungen → Leser und Zahlungen**, Abschnitt **Zahlungen mit Stripe**, und kopieren Sie die **Webhook-Adresse**. Sie hat die Form `https://www.example.de/platba/stripe`.
2. Öffnen Sie in Stripe **Developers → Webhooks**, wählen Sie **Add endpoint** und fügen Sie die Adresse ein.
3. Aktivieren Sie genau diese Ereignisse:
   - `checkout.session.completed`
   - `invoice.paid`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
4. Kopieren Sie nach dem Speichern das **Signing secret**. Es beginnt mit `whsec_`.

### 4. Kundenportal

Aktivieren Sie das Portal unter **Settings → Billing → Customer portal**. Erlauben Sie darin mindestens die Kündigung des Abonnements und die Änderung der Zahlungsmethode. Wir empfehlen die Kündigung **zum Ende des Zeitraums**: Der Leser liest dann zu Ende, was er bezahlt hat. Ohne aktiviertes Portal endet die Schaltfläche **Abonnement verwalten** mit einer Entschuldigung.

### 5. Ausfüllen in phpRS

Füllen Sie unter **Einstellungen → Leser und Zahlungen** im Abschnitt **Zahlungen mit Stripe** aus:

| Feld | Was einzugeben ist |
|---|---|
| **Geheimer Schlüssel** | der eingeschränkte Schlüssel `rk_…` (oder `sk_…`) |
| **Webhook-Geheimnis** | `whsec_…` aus Schritt 3 |
| **Monatspreis**, **Jahrespreis** | die Preis-IDs `price_…`; leeres Feld = der Zeitraum wird nicht angeboten |
| **Beschreibung des Monatspreises**, **Beschreibung des Jahrespreises** | Text an der Schaltfläche, zum Beispiel „4,90 € pro Monat“; freiwillig |

Schlüssel und Geheimnis werden nach dem Speichern nicht mehr ausgegeben – im Feld sehen Sie nur die letzten vier Zeichen. Einen Wert, der nicht die Form eines Schlüssels, eines Geheimnisses oder einer Preis-ID hat, speichert die Website nicht und sagt es. Die Zahlungen sind eingeschaltet, sobald Schlüssel, Geheimnis und mindestens ein Preis ausgefüllt sind; das Etikett bei der Überschrift des Teils ändert sich in **eingeschaltet**. Ausschalten lassen sich die Zahlungen, indem Sie beim Feld **Geheimer Schlüssel** das Häkchen bei **Gespeicherten Schlüssel entfernen** setzen und speichern.

Die Beschreibung des Preises ist nur Text. Den tatsächlichen Betrag bestimmt der Preis in Stripe – wenn Sie ihn dort ändern, passen Sie auch die Beschreibung an.

## Probelauf

Stripe hat einen Testmodus mit eigenen Schlüsseln (`rk_test_…`, `sk_test_…`), eigenen Preisen und eigenem Webhook. Richten Sie zuerst alles darin ein:

1. Schalten Sie Stripe in den Testmodus und gehen Sie die Schritte 1–5 mit Testwerten durch.
2. Registrieren Sie sich auf der Website als Leser und klicken Sie auf **Monatlich abonnieren**.
3. Geben Sie auf der Zahlungsseite die Testkarte `4242 4242 4242 4242`, ein beliebiges künftiges Ablaufdatum und einen beliebigen CVC-Code ein.
4. Nach der Rückkehr sollte im Leserkonto binnen Kurzem **Abo bis** mit Datum erscheinen und unter **Leser → Leser** das Etikett **Stripe: zahlt**.
5. Probieren Sie **Abonnement verwalten** und die Kündigung aus – das Etikett ändert sich in **Stripe: verlängert sich nicht**.

Wenn das Abonnement nicht eingeschaltet wird, sehen Sie in Stripe unter **Developers → Webhooks** nach der Zustellung der Nachrichten. Die Antwort 400 bedeutet ein falsches Webhook-Geheimnis (oder ein Geheimnis aus dem anderen Modus), die Antwort 404 eine falsche Adresse. Für den Echtbetrieb tauschen Sie danach Schlüssel, Geheimnis und Preis-IDs gegen die echten aus – Test- und Echtwerte lassen sich nicht mischen.

## Was danach geschieht

| Situation | Was Stripe tut | Was die Website tut |
|---|---|---|
| Reguläre Zahlung des nächsten Zeitraums | bucht den Betrag ab und sendet `invoice.paid` | verschiebt **Abo bis** auf das Ende des bezahlten Zeitraums und einen Tag darüber hinaus; trägt die Zahlung ein |
| Die Zahlung geht nicht durch | versucht es einige Tage lang erneut und schreibt dem Leser (je nach Einstellung in Stripe) | Etikett **Stripe: Zahlung fehlgeschlagen**; der Leser sieht im Konto die Aufforderung, die Karte zu prüfen; der Zugang endet mit dem Tag **Abo bis** |
| Der Leser kündigt das Abonnement | lässt es bis zum Ende des Zeitraums auslaufen | Etikett **Stripe: verlängert sich nicht**; der Zugang bleibt bis zum Ende des bezahlten Zeitraums |
| Das Abonnement endet | sendet `customer.subscription.deleted` | Etikett **Stripe: gekündigt**; der Leser kann erneut abonnieren |

Das Datum **Abo bis** verkürzt die Website nie: Es gilt das spätere von dem Datum, das die Zahlung eingetragen hat, und dem Datum, das Sie von Hand eingetragen haben. Der zusätzliche Tag deckt die Zeit zwischen dem Ende des Zeitraums und der Abbuchung der nächsten Zahlung ab. In Stripe erstattetes Geld (Refund) nimmt den Zugang nicht von selbst weg – beenden Sie das Abonnement des Lesers gegebenenfalls von Hand unter **Leser → Leser**.

Die Website trägt nur ein Abonnement ein, das der Leser über die Schaltfläche auf der Website abgeschlossen hat. Ein von Hand in Stripe angelegtes Abonnement und andere Verkäufe über dasselbe Stripe-Konto haben auf den Zugang der Leser keinen Einfluss.

Wer schon über Stripe zahlt, sieht die Schaltflächen für ein neues Abonnement nicht, er kann also kein zweites neben dem ersten abschließen. Ein Konto mit laufendem Abonnement lässt sich nicht löschen, solange der Leser es nicht kündigt.

### Löschen eines Lesers

phpRS ruft beim Löschen Stripe nicht auf. Wenn Sie in der Administration einen Leser mit laufendem Abonnement löschen (oder seine Daten über **Datenschutz und Cookies → Anfrage eines Lesers zu personenbezogenen Daten**), weist die Website Sie darauf hin, dass das Abonnement in Stripe weiterläuft. Kündigen Sie es in Stripe beim jeweiligen Kunden, sonst werden ihm weitere Zahlungen abgebucht.

## Übersicht in der Administration

- **Leser → Leser** zeigt bei jedem Leser das Datum des Abonnements und darunter den Status aus Stripe (**zahlt**, **verlängert sich nicht**, **Zahlung fehlgeschlagen**, **gekündigt**) oder den Hinweis **manuell eingetragen**. Die Kachel **Zahlen über Stripe** ist die Zahl der laufenden Abonnements.
- **Leser → Einnahmen** ergänzt auf der Karte **Abonnement** die Zahl der über Stripe Zahlenden und die Summe der Zahlungen der letzten 30 Tage, getrennt für jede Währung.

## Was die Website speichert

| Wo | Was |
|---|---|
| beim Leser | die ID des Kunden und des Abonnements in Stripe, den Status des Abonnements, das Datum **Abo bis** |
| Tabelle der Zahlungen | die ID der Nachricht von Stripe, Leser, Betrag, Währung, Datum |

Nicht gespeichert werden Kartennummer, Rechnungsadresse oder irgendeine andere Zahlungsangabe – sie bleiben bei Stripe. Die Auskunft über die personenbezogenen Daten eines Lesers enthält auch seine Zahlungen (Datum, Betrag, Währung). Nach der Löschung eines Lesers bleiben die Einträge über Zahlungen wegen der Buchhaltung erhalten, aber ohne Bezug zur Person. Die Daten des Kunden in Stripe werden von hier aus nicht gelöscht; löschen Sie sie dort.

Schlüssel, Webhook-Geheimnis und Zahlungen sind nicht Teil des [Exports der Website](../zaciname/import-z-wordpressu.md). In der Sicherung der Datenbank sind sie enthalten, wie die übrigen Einstellungen – bewahren Sie Sicherungen deshalb sicher auf.

## Steuern und Belege

phpRS stellt keine Rechnungen aus, berechnet keine Mehrwertsteuer und kümmert sich nicht um die Erfassung von Umsätzen. Belege über Zahlungen sendet Stripe den Lesern und macht sie ihnen zugänglich (einzustellen unter **Settings → Billing**), die Mehrwertsteuer kann Stripe Tax berechnen. Für die richtige Besteuerung, die Geschäftsbedingungen, die Belehrung über die automatische Verlängerung und die Bearbeitung von Reklamationen sind Sie als Herausgeber verantwortlich. Seine Gebühren zieht Stripe von jeder Zahlung ab; ihre Höhe finden Sie in seiner Preisliste.

## Siehe auch

- [Gesperrte Inhalte und Abonnement](zamceny-obsah.md)
- [Leserkonten](ucty-ctenaru.md)
- [Unterstützung und Einnahmen](podpora-a-prijmy.md)
- [Webanalyse und Datenschutz](../seo-a-ai/mereni-a-soukromi.md)
