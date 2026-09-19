<?php
/**
 * Globální pomocné funkce pro šablony a layouty.
 */

declare(strict_types=1);

/** Ošetření výstupu do HTML. */
function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Překlad textu šablony do jazyka webu: t('Celý článek'), t('Strana %s z %s', 2, 5). Viz Core\Jazyk. */
function t(string $text, string|int ...$hodnoty): string
{
    return PhpRS\Core\Jazyk::t($text, ...$hodnoty);
}

/** Převod textu na URL tvar: "Příliš žluťoučký kůň" -> "prilis-zlutoucky-kun". */
function slugify(string $text, int $maxLength = 120): string
{
    $map = [
        'á' => 'a', 'ä' => 'a', 'č' => 'c', 'ď' => 'd', 'é' => 'e', 'ě' => 'e', 'í' => 'i',
        'ľ' => 'l', 'ĺ' => 'l', 'ň' => 'n', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o', 'ŕ' => 'r',
        'ř' => 'r', 'š' => 's', 'ť' => 't', 'ú' => 'u', 'ů' => 'u', 'ü' => 'u', 'ý' => 'y',
        'ž' => 'z', 'ß' => 'ss',
    ];
    $text = strtr(mb_strtolower($text), $map);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    $text = trim(substr($text, 0, $maxLength), '-');

    return $text !== '' ? $text : 'n-a';
}

/** České datum: 18. 9. 2026, volitelně s časem. */
function datum(string|\DateTimeInterface|null $value, bool $withTime = false): string
{
    if ($value === null || $value === '') {
        return '';
    }
    $dt = $value instanceof \DateTimeInterface ? $value : new \DateTimeImmutable($value);

    return $dt->format($withTime ? 'j. n. Y H:i' : 'j. n. Y');
}

/** Datum slovy: "pátek 18. září 2026". */
function datum_slovy(string|\DateTimeInterface|null $value = null): string
{
    $dny = ['neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota'];
    $mesice = [1 => 'ledna', 'února', 'března', 'dubna', 'května', 'června', 'července', 'srpna', 'září', 'října', 'listopadu', 'prosince'];
    $dt = $value instanceof \DateTimeInterface ? $value : new \DateTimeImmutable($value ?? 'now');
    // slovník jazyka může dát vlastní tvar data: klíč "datum_slovy" = formát pro date(), např. "l, F j, Y"
    $format = t('datum_slovy');
    if ($format !== 'datum_slovy') {
        return preg_replace_callback('/[A-Za-zÀ-ž]{3,}/u', fn (array $m): string => t($m[0]), $dt->format($format)) ?? $dt->format($format);
    }

    return t($dny[(int) $dt->format('w')]) . ' ' . $dt->format('j') . '. ' . t($mesice[(int) $dt->format('n')]) . ' ' . $dt->format('Y');
}
