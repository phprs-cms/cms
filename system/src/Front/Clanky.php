<?php

declare(strict_types=1);

namespace PhpRS\Front;

use PhpRS\Core\Db;
use PhpRS\Core\Obrazky;
use PhpRS\Core\Settings;

/**
 * Čtení článků pro web. Na webu je vidět jen článek vydaný (visible = 1), jehož datum vydání už nastalo.
 */
final class Clanky
{
    private const string SELECT = "
        SELECT c.*, t.nazev AS tema_jm, t.seo_link AS tema_seo, t.obrazek AS tema_obr,
               NULLIF(u.jmeno, '') AS autor_jm, -- přihlašovací jméno se na webu neukazuje; bez vyplněného jména je autorem redakce
               u.pozice AS autor_pozice, u.foto AS autor_foto, u.bio AS autor_bio, u.url AS autor_url,
               s.soubor_cla_sab AS sablona_soubor
        FROM {clanky} c
        JOIN {topic} t ON t.idt = c.tema
        LEFT JOIN {user} u ON u.idu = c.autor
        LEFT JOIN {cla_sab} s ON s.ids = c.sablona";

    /**
     * Sloupce pro výpisy: bez dlouhých textů (text, shrnutí, FAQ, poznámka redakce), které výpis netiskne - u stovek
     * článků na disku je to znát. Klíče v poli zůstávají (prázdné), aby šablony nepadaly. Nový sloupec rs_clanky,
     * který má být vidět ve výpisech, je potřeba doplnit i sem.
     */
    private const string SLOUPCE_VYPISU = "c.idc, c.seo_link, c.titulek, c.uvod, '' AS text, c.obrazek, c.tema, c.autor, c.datum, c.datum_pl, c.visible, c.zobr_na_indexu,
        c.priority, c.typ_clanku, c.sablona, c.skupina_cl, c.zdroj, c.t_slova, c.noindex, c.pristup, '' AS shrnuti, '' AS faq, c.povolit_kom, c.kom, c.visit,
        c.hodnoceni, c.mn_hodnoceni, c.zmeneno, c.aktualizovano, c.jazyk, c.preklad_z, c.zive, c.medium_url, c.recenze_predmet, c.recenze_hodnoceni";

    private const string VYDANE = 'c.visible = 1 AND c.datum <= NOW()';

    /** Podmínka "vydaný článek v jazyce právě zobrazené verze webu". */
    private readonly string $vydane;

    /**
     * @param string $zaklad cesta k instalaci ("" nebo "/magazin") - doplňuje se před adresy obrázků z media/
     * @param (\Closure(array<string, mixed>): array<string, mixed>)|null $uprava poslední úprava článku před šablonou (zamčený obsah)
     */
    public function __construct(private readonly Db $db, private readonly Settings $settings, private readonly string $zaklad = '', private readonly ?\Closure $uprava = null)
    {
        $this->vydane = self::VYDANE . " AND c.jazyk = '" . \PhpRS\Core\Jazyk::sloupecWebu() . "'";
    }

    /**
     * Úprava článku před předáním šabloně: adresa hlavního obrázku z media/ dostane cestu k instalaci.
     *
     * @param array<string, mixed> $clanek
     * @return array<string, mixed>
     */
    private function priprav(array $clanek): array
    {
        if ($clanek['obrazek'] !== '' && !preg_match('#^(https?:)?/#', $clanek['obrazek'])) {
            $clanek['obrazek'] = $this->zaklad . '/' . $clanek['obrazek'];
        }
        // responzivní obrázky: hlavní obrázek i obrázky v textu dostanou srcset z variant, které vznikly při nahrání
        $clanek['obrazek_srcset'] = Obrazky::srcset(ltrim(substr($clanek['obrazek'], strlen($this->zaklad)), '/'), $this->zaklad);
        foreach (['uvod', 'text'] as $cast) {
            if (str_contains($clanek[$cast], 'media/')) {
                $clanek[$cast] = preg_replace_callback('#<img\b(?![^>]*\bsrcset=)([^>]*?)\bsrc="([^"]*?(media/\d{4}/\d{2}/[^"]+))"#i', function (array $m): string {
                    $srcset = Obrazky::srcset($m[3], $this->zaklad);

                    return $srcset === '' ? $m[0] : '<img' . $m[1] . 'src="' . $m[2] . '" srcset="' . e($srcset) . '" sizes="(max-width: 800px) 100vw, 800px"';
                }, $clanek[$cast]) ?? $clanek[$cast];
            }
        }

        return $this->uprava === null ? $clanek : ($this->uprava)($clanek);
    }

    public function naStranku(): int
    {
        return max(1, $this->settings->int('pocet_clanku'));
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} články a jejich celkový počet */
    public function naHlavniStranku(int $strana, ?int $limit = null, bool $sTextem = false): array
    {
        $where = $this->vydane . ' AND c.zobr_na_indexu = 1';
        if ($this->settings->bool('hlidat_platnost')) {
            $where .= ' AND (c.datum_pl IS NULL OR c.datum_pl > NOW())';
        }

        return $this->vypis($where, [], 'c.priority DESC, c.datum DESC, c.idc DESC', $strana, $limit, $sTextem);
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} */
    public function zRubriky(int $idt, int $strana, ?int $limit = null): array
    {
        return $this->vypis($this->vydane . ' AND c.tema = ?', [$idt], 'c.datum DESC, c.idc DESC', $strana, $limit);
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} články vydané v měsíci "RRRR-MM" */
    public function zMesice(string $mesic, int $strana): array
    {
        // rozsah dat místo DATE_FORMAT(): dotaz může použít index nad datem
        $od = new \DateTimeImmutable($mesic . '-01');

        return $this->vypis($this->vydane . ' AND c.datum >= ? AND c.datum < ?', [$od->format('Y-m-d'), $od->modify('+1 month')->format('Y-m-d')], 'c.datum DESC, c.idc DESC', $strana);
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} */
    public function odAutora(int $idu, int $strana): array
    {
        return $this->vypis($this->vydane . ' AND c.autor = ?', [$idu], 'c.datum DESC, c.idc DESC', $strana);
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} */
    public function seStitkem(int $ids, int $strana): array
    {
        return $this->vypis($this->vydane . ' AND EXISTS (SELECT 1 FROM {clanky_stitky} cs WHERE cs.idc = c.idc AND cs.ids = ?)', [$ids], 'c.datum DESC, c.idc DESC', $strana);
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} */
    public function hledej(string $q, int $strana): array
    {
        // fulltextový index ft_clanky: každé slovo od 3 znaků jako "+slovo*" (všechna slova, libovolná koncovka);
        // titulek se navíc hledá přes LIKE, aby šla najít i krátká slova a části slov
        $like = '%' . addcslashes($q, '%_\\') . '%';
        $slova = array_filter(preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($q)) ?: [], fn (string $s): bool => mb_strlen($s) >= 3);
        if ($slova === []) {
            return $this->vypis($this->vydane . ' AND c.titulek LIKE ?', [$like], 'c.datum DESC, c.idc DESC', $strana);
        }
        $dotaz = implode(' ', array_map(fn (string $s): string => '+' . $s . '*', array_slice($slova, 0, 8)));

        return $this->vypis(
            // text zamčených článků se neprohledává - jinak by šel po kouskách vyčíst z výsledků hledání
            $this->vydane . ' AND ((c.pristup = 0 AND MATCH(c.titulek, c.uvod, c.text, c.t_slova) AGAINST (? IN BOOLEAN MODE)) OR c.titulek LIKE ? OR (c.pristup > 0 AND c.uvod LIKE ?))',
            [$dotaz, $like, $like],
            'c.datum DESC, c.idc DESC',
            $strana,
        );
    }

    /** @return array<string, mixed>|null */
    public function podleSeo(string $seo, bool $iNevydany = false): ?array
    {
        $clanek = $this->db->one(self::SELECT . ' WHERE c.seo_link = ?' . ($iNevydany ? '' : ' AND ' . self::VYDANE), [$seo]);

        return $clanek === null ? null : $this->priprav($clanek);
    }

    /**
     * Ostatní články ze stejné skupiny souvisejících článků.
     *
     * @param array<string, mixed> $clanek
     * @return list<array<string, mixed>>
     */
    public function zeSkupiny(array $clanek): array
    {
        if ($clanek['skupina_cl'] === null) {
            return [];
        }

        return $this->db->all(
            'SELECT c.titulek, c.seo_link, c.datum FROM {clanky} c WHERE c.skupina_cl = ? AND c.idc <> ? AND ' . $this->vydane . ' ORDER BY c.datum',
            [$clanek['skupina_cl'], $clanek['idc']],
        );
    }

    /**
     * Podobné články: nejdřív podle počtu společných štítků, potom novější ze stejné rubriky.
     *
     * @param array<string, mixed> $clanek
     * @return list<array<string, mixed>>
     */
    public function podobne(array $clanek, int $pocet = 4): array
    {
        return $this->db->all(
            'SELECT c.titulek, c.seo_link, c.datum, COUNT(cs.ids) AS shoda
             FROM {clanky} c LEFT JOIN {clanky_stitky} cs ON cs.idc = c.idc AND cs.ids IN (SELECT ids FROM {clanky_stitky} WHERE idc = ?)
             WHERE ' . $this->vydane . ' AND c.idc <> ? AND c.typ_clanku = 1 AND (c.tema = ? OR cs.ids IS NOT NULL) AND c.datum > NOW() - INTERVAL 2 YEAR
             GROUP BY c.idc, c.titulek, c.seo_link, c.datum ORDER BY shoda DESC, c.datum DESC LIMIT ?',
            [$clanek['idc'], $clanek['idc'], $clanek['tema'], $pocet],
        );
    }

    /** @return list<array<string, mixed>> */
    public function nejctenejsi(int $pocet): array
    {
        return $this->db->all(
            'SELECT c.titulek, c.seo_link, c.visit FROM {clanky} c WHERE ' . $this->vydane . ' AND c.typ_clanku = 1 AND c.visit > 0 ORDER BY c.visit DESC LIMIT ?',
            [$pocet],
        );
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} */
    private function vypis(string $where, array $params, string $order, int $strana, ?int $limit = null, bool $sTextem = false): array
    {
        // pevný počet (bloky, RSS, kanály) = nikdo nestránkuje, celkový počet se nepočítá
        $celkem = $limit !== null ? 0 : (int) $this->db->value("SELECT COUNT(*) FROM {clanky} c WHERE {$where}", $params);
        $limit ??= $this->naStranku();
        $strana = max(1, min($strana, 100000));
        $clanky = $this->db->all(
            ($sTextem ? self::SELECT : str_replace('SELECT c.*,', 'SELECT ' . self::SLOUPCE_VYPISU . ',', self::SELECT)) . " WHERE {$where} ORDER BY {$order} LIMIT ? OFFSET ?",
            [...$params, $limit, ($strana - 1) * $limit],
        );

        return [array_map($this->priprav(...), $clanky), $celkem];
    }
}
