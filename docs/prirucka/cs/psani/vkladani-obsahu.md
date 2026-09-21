# Vkládání videa a příspěvků ze sítí

Video, podcast nebo příspěvek ze sociální sítě vložíte do článku tak, že jeho adresu dáte na samostatný řádek. Žádný kód pro vložení nepotřebujete.

## Postup

1. V prohlížeči zkopírujte adresu videa nebo příspěvku.
2. V textu článku začněte nový odstavec a adresu do něj vložte. V odstavci nesmí být nic jiného.
3. Článek uložte. V editoru zůstane vidět adresa; přehrávač nebo příspěvek se z ní vytvoří až na webu. Výsledek zkontrolujete tlačítkem **Náhled**.

Adresa uprostřed věty zůstane obyčejným textem nebo odkazem.

## Podporované služby

| Služba | Jaká adresa |
|---|---|
| YouTube | video, Shorts i živé vysílání (`youtube.com/watch?v=…`, `youtu.be/…`) |
| Vimeo | adresa videa (`vimeo.com/123456789`) |
| Spotify | epizoda, pořad nebo skladba (`open.spotify.com/episode/…`) |
| X (Twitter) | jednotlivý příspěvek (`x.com/ucet/status/…`) |
| Instagram | příspěvek, reel nebo video (`instagram.com/p/…`, `/reel/…`) |
| Facebook | příspěvek, video nebo fotka (`facebook.com/…/posts/…`) |
| TikTok | video (`tiktok.com/@ucet/video/…`) |
| Mastodon | příspěvek na kterémkoli serveru (`https://server/@ucet/1234567890`) |

Adresy jiných služeb zůstanou v článku tak, jak jste je napsali.

## Načte se až po kliknutí

Čtenář na místě videa nejdřív uvidí tlačítko – **Přehrát video**, **Přehrát zvuk** nebo **Zobrazit příspěvek ze sítě** – a pod ním název služby, ze které se obsah načte. Teprve po kliknutí se obsah z cizí služby opravdu načte.

Má to dva důvody:

- **Soukromí.** Dokud čtenář neklikne, cizí služba se o jeho návštěvě nedozví.
- **Rychlost.** Stránka se nezdržuje načítáním cizích přehrávačů.

Videa z YouTube se přehrávají přes adresu `youtube-nocookie.com`. U příspěvku ze sítě zůstává pod tlačítkem odkaz **Otevřít původní příspěvek**, který funguje i bez kliknutí na tlačítko.

## Vlastní zvuk a video

Soubor MP3, M4A, OGG, WAV, MP4 nebo WebM nahrajte do Médií jako přílohu. Do článku ho pak dostanete dvěma způsoby:

- jako **odkaz ke stažení** – tlačítkem **obrázek** v liště editoru, viz [Obrázky, galerie a přílohy](obrazky-a-galerie.md),
- jako **přehrávač nad textem** – adresu souboru vložte do pole **Zvuk nebo video** v oddílu **Podcast, video, živě, recenze**, viz [Typy obsahu](typy-obsahu.md).

Vlastní soubor se přehrává přímo z vašeho webu, proto se u něj tlačítko pro načtení nezobrazuje.

## Přístupnost

Vložíte-li v režimu **HTML** vlastní rámec (`iframe`), dejte mu atribut `title` s názvem obsahu. **Kontrola přístupnosti** v editoru rámec bez názvu hlásí.

## Související

- [Editor článku](editor.md)
- [Typy obsahu](typy-obsahu.md)
