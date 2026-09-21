#!/usr/bin/env python3
"""Doplní překlady do slovníku phpRS 3 a správně je escapuje (apostrof v překladu jinak rozbije PHP soubor).

Použití: tools/slovnik.py system/jazyky/admin-en.php < radky    (řádek = "česky|překlad")
Existující klíče přeskočí; položky shodné s češtinou nezapisuje.
"""
import sys

def php(text: str) -> str:
    return "'" + text.replace('\\', '\\\\').replace("'", "\\'") + "'"

cesta = sys.argv[1]
obsah = open(cesta, encoding='utf-8').read()
nove = ''
for radek in sys.stdin.read().splitlines():
    if '|' not in radek:
        continue
    cesky, preklad = radek.split('|', 1)
    if cesky == preklad or (php(cesky) + ' =>') in obsah:
        continue
    nove += '    ' + php(cesky) + ' => ' + php(preklad) + ',\n'
konec = obsah.rindex('];')
open(cesta, 'w', encoding='utf-8').write(obsah[:konec] + nove + obsah[konec:])
print(f'{cesta}: doplněno {nove.count(chr(10))} položek')
