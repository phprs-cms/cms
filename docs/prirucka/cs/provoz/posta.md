# Pošta

Web posílá potvrzení odběru newsletteru a registrace čtenářů, odkazy pro nastavení hesla, newslettery, upozornění na komentáře a redakční upozornění. Všechno se nastavuje v **Nastavení → Pošta**.

## Způsob odesílání

| Způsob | Kdy ho použít |
| --- | --- |
| **Server hostingu** | Funguje hned, nic se nenastavuje. Zprávy ale u řady hostingů padají do spamu. Stačí pro malý web bez newsletteru. |
| **Vlastní SMTP server** | Zprávy odcházejí z ověřené schránky. Doporučeno vždy, když posíláte newsletter nebo registrujete čtenáře. |

## Nastavení SMTP

Údaje najdete u poskytovatele schránky – u hostingu, Google Workspace, Seznamu nebo u služby pro hromadnou poštu (Brevo, Mailgun, Amazon SES…).

- **SMTP server** – například `smtp.vasedomena.cz`.
- **Zabezpečení** – **STARTTLS, port 587** je nejběžnější; **SSL/TLS, port 465** u starších služeb. Volbu „žádné“ použijte jen pro server ve vlastní síti.
- **Uživatel** a **Heslo** – u Gmailu a Seznamu zadejte „heslo pro aplikace“, ne heslo k účtu. Heslo se ukládá jen na vašem webu a do formuláře se už nikdy nevypíše; prázdné pole znamená „beze změny“.

Po uložení klepněte na **Odeslat zkušební e-mail na adresu redakce**. Zkouška používá uložené hodnoty, takže nejdřív ukládejte, potom zkoušejte.

## Odesílatel a odpovědi

V oddílu **Odesílatel a odpovědi** nastavíte adresu, která bude u zpráv jako odesílatel (prázdné pole = e-mail redakce), a adresu pro odpovědi.
Adresa odesílatele by měla patřit doméně, ze které smí váš SMTP server posílat – jinak zprávy skončí ve spamu nebo je příjemce odmítne.

## Aby pošta nekončila ve spamu

U domény odesílatele mějte v DNS nastavené záznamy **SPF** a **DKIM** podle návodu poskytovatele pošty a ideálně i **DMARC**. Bez nich velcí poskytovatelé (Gmail, Seznam, Outlook) hromadnou poštu omezují nebo odmítají.

## Fronta a opakování

Zpráva, kterou se nepodaří odeslat, se nezahodí: systém ji zkouší znovu za **5 minut, 30 minut, 2 hodiny a 12 hodin**. Opakování zajišťují [úlohy na pozadí](ulohy-na-pozadi.md).
Přehled **Poslední zprávy** ukazuje čas, příjemce, předmět a stav (*odesláno*, *čeká na další pokus*, *neodesláno*). Obsah zpráv se neuchovává a záznamy se po 30 dnech mažou.

## Nejčastější potíže

- **Zkušební e-mail nedorazil** – podívejte se do přehledu Poslední zprávy. Stav *neodesláno* znamená chybu spojení nebo přihlášení; *odesláno* znamená, že zprávu převzal server a hledat je potřeba ve spamu příjemce.
- **Přihlášení k SMTP selhává** – ověřte, že používáte heslo pro aplikace a správnou kombinaci portu a zabezpečení.
- **Hosting blokuje odchozí spojení** – některé sdílené hostingy povolují SMTP jen na vlastní servery. Použijte schránku u téhož hostingu.
