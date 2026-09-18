<?php

declare(strict_types=1);

namespace PhpRS\Core;

/**
 * Příjem nahraných obrázků: ověření, zmenšení na rozumnou velikost, náhled, uložení do media/RRRR/MM/.
 * Obrázek se vždy znovu zakóduje přes GD - tím zmizí EXIF (poloha z mobilu) i případný podstrčený kód.
 */
final class Obrazky
{
    public const int MAX_STRANA = 2000;
    public const int NAHLED_STRANA = 640;
    private const int MAX_BAJTU = 20 * 1024 * 1024;
    private const int MAX_PIXELU = 50_000_000;

    private const array TYPY = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];

    /**
     * @param array<string, mixed> $file položka z $_FILES
     * @return array{obr_poloha:string, obr_width:int, obr_height:int, obr_vel:int, nahl_poloha:string, nahl_width:int, nahl_height:int, nazev:string}
     * @throws \RuntimeException s českou hláškou pro uživatele
     */
    public static function uloz(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file((string) $file['tmp_name'])) {
            throw new \RuntimeException(match ($file['error'] ?? 0) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Soubor je větší, než server dovoluje nahrát (' . ini_get('upload_max_filesize') . ').',
                default => 'Soubor se nepodařilo nahrát.',
            });
        }
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('Na serveru chybí rozšíření GD pro práci s obrázky.');
        }
        $tmp = (string) $file['tmp_name'];
        $info = @getimagesize($tmp);
        if ($info === false || !isset(self::TYPY[$info[2]])) {
            throw new \RuntimeException('Povolené jsou jen obrázky JPG, PNG, WebP a GIF.');
        }
        if (filesize($tmp) > self::MAX_BAJTU || $info[0] * $info[1] > self::MAX_PIXELU) {
            throw new \RuntimeException('Obrázek je příliš velký (nejvýše 20 MB a 50 megapixelů).');
        }

        $pripona = self::TYPY[$info[2]];
        $nazev = pathinfo((string) ($file['name'] ?? 'obrazek'), PATHINFO_FILENAME);
        $slozka = 'media/' . date('Y/m');
        if (!is_dir(PHPRS_ROOT . '/' . $slozka) && !mkdir(PHPRS_ROOT . '/' . $slozka, 0775, true)) {
            throw new \RuntimeException('Nelze vytvořit složku ' . $slozka . ' - zkontrolujte práva k zápisu.');
        }
        $zaklad = $slozka . '/' . slugify($nazev, 60) . '-' . bin2hex(random_bytes(3));

        if ($pripona === 'gif') {
            // GIF může být animovaný - ukládá se beze změny, náhled je první snímek
            $cil = $zaklad . '.gif';
            if (!move_uploaded_file($tmp, PHPRS_ROOT . '/' . $cil)) {
                throw new \RuntimeException('Soubor se nepodařilo uložit.');
            }
            $obr = imagecreatefromgif(PHPRS_ROOT . '/' . $cil);
            [$w, $h] = [$info[0], $info[1]];
        } else {
            $obr = @imagecreatefromstring((string) file_get_contents($tmp));
            if ($obr === false) {
                throw new \RuntimeException('Obrázek je poškozený a nelze ho zpracovat.');
            }
            $obr = self::otocPodleExif($obr, $tmp, $pripona);
            $obr = self::zmensi($obr, self::MAX_STRANA);
            [$w, $h] = [imagesx($obr), imagesy($obr)];
            $cil = $zaklad . '.' . $pripona;
            self::zapis($obr, PHPRS_ROOT . '/' . $cil, $pripona);
        }

        $nahled = self::zmensi($obr, self::NAHLED_STRANA);
        $nahledPripona = $pripona === 'gif' ? 'png' : $pripona;
        $nahledCil = $zaklad . '-nahled.' . $nahledPripona;
        self::zapis($nahled, PHPRS_ROOT . '/' . $nahledCil, $nahledPripona);

        return [
            'obr_poloha' => $cil, 'obr_width' => $w, 'obr_height' => $h, 'obr_vel' => (int) filesize(PHPRS_ROOT . '/' . $cil),
            'nahl_poloha' => $nahledCil, 'nahl_width' => imagesx($nahled), 'nahl_height' => imagesy($nahled),
            'nazev' => mb_substr(trim(str_replace(['_', '-'], ' ', $nazev)), 0, 150),
        ];
    }

    /** Smaže soubory obrázku; cesty mimo media/ ignoruje. */
    public static function smaz(string ...$cesty): void
    {
        foreach ($cesty as $cesta) {
            if (preg_match('#^media/\d{4}/\d{2}/[a-z0-9-]+\.(jpg|png|webp|gif)$#', $cesta) && is_file(PHPRS_ROOT . '/' . $cesta)) {
                unlink(PHPRS_ROOT . '/' . $cesta);
            }
        }
    }

    private static function zmensi(\GdImage $obr, int $maxStrana): \GdImage
    {
        [$w, $h] = [imagesx($obr), imagesy($obr)];
        if (max($w, $h) <= $maxStrana) {
            return $obr;
        }
        $pomer = $maxStrana / max($w, $h);
        $novy = imagecreatetruecolor(max(1, (int) round($w * $pomer)), max(1, (int) round($h * $pomer)));
        imagealphablending($novy, false);
        imagesavealpha($novy, true);
        imagecopyresampled($novy, $obr, 0, 0, 0, 0, imagesx($novy), imagesy($novy), $w, $h);

        return $novy;
    }

    private static function zapis(\GdImage $obr, string $soubor, string $pripona): void
    {
        $ok = match ($pripona) {
            'jpg' => imagejpeg($obr, $soubor, 85),
            'webp' => imagewebp($obr, $soubor, 85),
            default => (function () use ($obr, $soubor): bool {
                imagesavealpha($obr, true);

                return imagepng($obr, $soubor, 7);
            })(),
        };
        if (!$ok) {
            throw new \RuntimeException('Obrázek se nepodařilo uložit - zkontrolujte práva ke složce media/.');
        }
    }

    /** Fotky z mobilu bývají uložené naležato s příznakem otočení v EXIF. */
    private static function otocPodleExif(\GdImage $obr, string $soubor, string $pripona): \GdImage
    {
        if ($pripona !== 'jpg' || !function_exists('exif_read_data')) {
            return $obr;
        }
        $uhel = match ((int) (@exif_read_data($soubor)['Orientation'] ?? 1)) {
            3 => 180, 6 => 270, 8 => 90, default => 0,
        };

        return $uhel === 0 ? $obr : (imagerotate($obr, $uhel, 0) ?: $obr);
    }
}
