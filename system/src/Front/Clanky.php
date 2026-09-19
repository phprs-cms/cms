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
               IF(u.jmeno = '' OR u.jmeno IS NULL, u.user, u.jmeno) AS autor_jm,
               s.soubor_cla_sab AS sablona_soubor
        FROM {clanky} c
        JOIN {topic} t ON t.idt = c.tema
        LEFT JOIN {user} u ON u.idu = c.autor
        LEFT JOIN {cla_sab} s ON s.ids = c.sablona";

    private const string VYDANE = 'c.visible = 1 AND c.datum <= NOW()';

    /**
     * @param string $zaklad cesta k instalaci ("" nebo "/magazin") - doplňuje se před adresy obrázků z media/
     * @param (\Closure(array<string, mixed>): array<string, mixed>)|null $uprava poslední úprava článku před šablonou (zamčený obsah)
     */
    public function __construct(private readonly Db $db, private readonly Settings $settings, private readonly string $zaklad = '', private readonly ?\Closure $uprava = null)
    {
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
    public function naHlavniStranku(int $strana, ?int $limit = null): array
    {
        $where = self::VYDANE . ' AND c.zobr_na_indexu = 1';
        if ($this->settings->bool('hlidat_platnost')) {
            $where .= ' AND (c.datum_pl IS NULL OR c.datum_pl > NOW())';
        }

        return $this->vypis($where, [], 'c.priority DESC, c.datum DESC, c.idc DESC', $strana, $limit);
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} */
    public function zRubriky(int $idt, int $strana, ?int $limit = null): array
    {
        return $this->vypis(self::VYDANE . ' AND c.tema = ?', [$idt], 'c.datum DESC, c.idc DESC', $strana, $limit);
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} články vydané v měsíci "RRRR-MM" */
    public function zMesice(string $mesic, int $strana): array
    {
        return $this->vypis(self::VYDANE . " AND DATE_FORMAT(c.datum, '%Y-%m') = ?", [$mesic], 'c.datum DESC, c.idc DESC', $strana);
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} */
    public function odAutora(int $idu, int $strana): array
    {
        return $this->vypis(self::VYDANE . ' AND c.autor = ?', [$idu], 'c.datum DESC, c.idc DESC', $strana);
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} */
    public function seStitkem(int $ids, int $strana): array
    {
        return $this->vypis(self::VYDANE . ' AND EXISTS (SELECT 1 FROM {clanky_stitky} cs WHERE cs.idc = c.idc AND cs.ids = ?)', [$ids], 'c.datum DESC, c.idc DESC', $strana);
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} */
    public function hledej(string $q, int $strana): array
    {
        $like = '%' . addcslashes($q, '%_\\') . '%';

        return $this->vypis(
            self::VYDANE . ' AND (c.titulek LIKE ? OR c.t_slova LIKE ? OR c.uvod LIKE ? OR c.text LIKE ?)',
            [$like, $like, $like, $like],
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
            'SELECT c.titulek, c.seo_link, c.datum FROM {clanky} c WHERE c.skupina_cl = ? AND c.idc <> ? AND ' . self::VYDANE . ' ORDER BY c.datum',
            [$clanek['skupina_cl'], $clanek['idc']],
        );
    }

    /** @return list<array<string, mixed>> */
    public function nejctenejsi(int $pocet): array
    {
        return $this->db->all(
            'SELECT c.titulek, c.seo_link, c.visit FROM {clanky} c WHERE ' . self::VYDANE . ' AND c.typ_clanku = 1 AND c.visit > 0 ORDER BY c.visit DESC LIMIT ?',
            [$pocet],
        );
    }

    /** @return array{0: list<array<string, mixed>>, 1: int} */
    private function vypis(string $where, array $params, string $order, int $strana, ?int $limit = null): array
    {
        $limit ??= $this->naStranku();
        $celkem = (int) $this->db->value("SELECT COUNT(*) FROM {clanky} c WHERE {$where}", $params);
        $clanky = $this->db->all(
            self::SELECT . " WHERE {$where} ORDER BY {$order} LIMIT ? OFFSET ?",
            [...$params, $limit, ($strana - 1) * $limit],
        );

        return [array_map($this->priprav(...), $clanky), $celkem];
    }
}
