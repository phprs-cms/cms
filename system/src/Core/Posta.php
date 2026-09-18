<?php

declare(strict_types=1);

namespace PhpRS\Core;

/**
 * Odesílání e-mailů funkcí mail() - funguje na běžném hostingu bez nastavování.
 * Odesílatelem je E-mail redakce z Nastavení.
 */
final class Posta
{
    /** @param array<string, string> $hlavicky další hlavičky (např. List-Unsubscribe) */
    public static function odesli(Settings $web, string $komu, string $predmet, string $text, string $html = '', array $hlavicky = []): bool
    {
        $od = $web->get('email_webu');
        if ($od === '' || !function_exists('mail') || filter_var($komu, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        $jmeno = '=?UTF-8?B?' . base64_encode($web->get('nazev_webu')) . '?=';
        $h = ['From' => "{$jmeno} <{$od}>", 'MIME-Version' => '1.0'] + $hlavicky;
        if ($html === '') {
            $h['Content-Type'] = 'text/plain; charset=utf-8';
            $telo = $text;
        } else {
            $hranice = 'phprs-' . bin2hex(random_bytes(8));
            $h['Content-Type'] = 'multipart/alternative; boundary="' . $hranice . '"';
            $telo = "--{$hranice}\r\nContent-Type: text/plain; charset=utf-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($text))
                . "--{$hranice}\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html)) . "--{$hranice}--\r\n";
        }
        $radky = [];
        foreach ($h as $nazev => $hodnota) {
            $radky[] = $nazev . ': ' . str_replace(["\r", "\n"], '', $hodnota);
        }

        return @mail($komu, '=?UTF-8?B?' . base64_encode($predmet) . '?=', $telo, implode("\r\n", $radky));
    }
}
