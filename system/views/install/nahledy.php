<?php
/**
 * Miniaturní náhledy pro instalátor: prostředí administrace a šablony webu.
 *
 * @return array<string, string> klíč => SVG
 */
$svg = fn (string $obsah): string => '<svg viewBox="0 0 240 150" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' . $obsah . '</svg>';

return [
    'retro' => $svg(
        '<rect width="240" height="150" fill="#fff"/>'
        . '<rect x="50" y="8" width="140" height="26" fill="#AAD5FF" stroke="#0000FF" stroke-width="2"/>'
        . '<g fill="#0000FF"><rect x="58" y="14" width="18" height="3"/><rect x="82" y="14" width="18" height="3"/><rect x="106" y="14" width="18" height="3"/><rect x="130" y="14" width="18" height="3"/><rect x="154" y="14" width="18" height="3"/><rect x="58" y="24" width="18" height="3"/><rect x="82" y="24" width="18" height="3"/><rect x="106" y="24" width="18" height="3"/></g>'
        . '<rect y="40" width="240" height="8" fill="#AAD5FF"/><rect y="48" width="240" height="2" fill="#0000FF"/>'
        . '<text x="120" y="98" text-anchor="middle" font-family="Verdana,Arial" font-size="30" font-weight="bold" font-style="italic" fill="#1F4FE0">phpRS</text>'
        . '<rect x="70" y="108" width="100" height="12" rx="6" fill="#D7EBFF" stroke="#0000FF"/>'
    ),
    '2026' => $svg(
        '<rect width="240" height="150" fill="#F6F7F9"/>'
        . '<rect width="58" height="150" fill="#fff"/><rect x="58" width="1" height="150" fill="#E4E7EC"/>'
        . '<rect x="8" y="9" width="26" height="6" rx="2" fill="#1F4FE0"/>'
        . '<rect x="6" y="28" width="46" height="10" rx="3" fill="#EEF2FF"/><g fill="#CDD2DB"><rect x="10" y="46" width="34" height="4" rx="2"/><rect x="10" y="58" width="38" height="4" rx="2"/><rect x="10" y="70" width="30" height="4" rx="2"/><rect x="10" y="82" width="36" height="4" rx="2"/></g>'
        . '<rect x="59" width="181" height="22" fill="#fff"/><rect x="59" y="22" width="181" height="1" fill="#E4E7EC"/><rect x="190" y="7" width="40" height="8" rx="4" fill="#F0F2F5"/>'
        . '<rect x="72" y="34" width="50" height="7" rx="2" fill="#14171F"/><rect x="192" y="32" width="36" height="11" rx="3" fill="#1F4FE0"/>'
        . '<g fill="#fff" stroke="#E4E7EC"><rect x="72" y="52" width="46" height="28" rx="4"/><rect x="126" y="52" width="46" height="28" rx="4"/><rect x="180" y="52" width="46" height="28" rx="4"/><rect x="72" y="90" width="154" height="48" rx="4"/></g>'
        . '<g fill="#E4E7EC"><rect x="80" y="102" width="138" height="1"/><rect x="80" y="114" width="138" height="1"/><rect x="80" y="126" width="138" height="1"/></g>'
    ),
    'default' => $svg(
        '<rect width="240" height="150" fill="#fff"/>'
        . '<rect x="14" y="12" width="80" height="9" rx="1" fill="#1B1B1F"/><rect y="30" width="240" height="2" fill="#0A2FC4"/>'
        . '<g fill="#DADDE5"><rect x="14" y="44" width="40" height="3"/><rect x="14" y="54" width="34" height="3"/><rect x="14" y="64" width="38" height="3"/><rect x="186" y="44" width="40" height="3"/><rect x="186" y="54" width="36" height="3"/><rect x="186" y="64" width="40" height="3"/></g>'
        . '<rect x="68" y="44" width="70" height="7" fill="#1B1B1F"/><g fill="#C9CCD6"><rect x="68" y="58" width="104" height="3"/><rect x="68" y="66" width="96" height="3"/></g>'
        . '<rect x="68" y="86" width="84" height="7" fill="#1B1B1F"/><g fill="#C9CCD6"><rect x="68" y="100" width="104" height="3"/><rect x="68" y="108" width="90" height="3"/></g>'
    ),
    'classic-newspaper' => $svg(
        '<rect width="240" height="150" fill="#FDFCF9"/>'
        . '<text x="120" y="24" text-anchor="middle" font-family="Georgia,serif" font-size="17" font-weight="bold" fill="#121212">The Daily Magazín</text>'
        . '<rect x="14" y="32" width="212" height="1" fill="#121212"/><rect x="14" y="35" width="212" height="1" fill="#121212"/>'
        . '<rect x="14" y="46" width="120" height="9" fill="#121212"/><rect x="14" y="59" width="96" height="9" fill="#121212"/>'
        . '<g fill="#B9B6AE"><rect x="14" y="76" width="120" height="3"/><rect x="14" y="84" width="112" height="3"/><rect x="14" y="92" width="118" height="3"/></g>'
        . '<rect x="144" y="44" width="1" height="92" fill="#D9D6CE"/>'
        . '<rect x="154" y="46" width="72" height="40" fill="#D9D6CE"/><rect x="154" y="92" width="64" height="6" fill="#121212"/><g fill="#B9B6AE"><rect x="154" y="104" width="72" height="3"/><rect x="154" y="112" width="60" height="3"/></g>'
        . '<rect x="14" y="106" width="120" height="1" fill="#D9D6CE"/><rect x="14" y="114" width="54" height="6" fill="#121212"/><rect x="78" y="114" width="54" height="6" fill="#121212"/>'
    ),
    'modern-magazine' => $svg(
        '<rect width="240" height="150" fill="#fff"/>'
        . '<rect width="240" height="20" fill="#000"/><rect x="12" y="6" width="40" height="8" fill="#fff"/><g fill="#8A8A8A"><rect x="150" y="8" width="20" height="4"/><rect x="178" y="8" width="20" height="4"/><rect x="206" y="8" width="20" height="4"/></g>'
        . '<rect x="12" y="30" width="216" height="62" fill="#1A1A1A"/><rect x="22" y="58" width="130" height="10" fill="#fff"/><rect x="22" y="73" width="96" height="10" fill="#fff"/><rect x="22" y="44" width="30" height="5" fill="#FF2D6F"/>'
        . '<g fill="#E6E6E6"><rect x="12" y="102" width="66" height="28"/><rect x="87" y="102" width="66" height="28"/><rect x="162" y="102" width="66" height="28"/></g>'
        . '<g fill="#000"><rect x="12" y="135" width="56" height="6"/><rect x="87" y="135" width="50" height="6"/><rect x="162" y="135" width="58" height="6"/></g>'
    ),
];
