#!/usr/bin/env bash
# phpRS 3 - kouřový test: čistá instalace do dočasné kopie a průchod hlavními stránkami.
# Spouští se lokálně i v GitHub Actions. Databázi bere z proměnných prostředí:
#   DB_HOST (127.0.0.1) DB_PORT (3306) DB_NAME (phprs3_test) DB_USER (root) DB_PASS (prázdné) PORT (8099)
# Databáze DB_NAME se při testu SMAŽE a vytvoří znovu.
set -euo pipefail

KOREN="$(cd "$(dirname "$0")/.." && pwd)"
DB_HOST="${DB_HOST:-127.0.0.1}"; DB_PORT="${DB_PORT:-3306}"; DB_NAME="${DB_NAME:-phprs3_test}"; DB_USER="${DB_USER:-root}"; DB_PASS="${DB_PASS:-}"; PORT="${PORT:-8099}"
PRACE="$(mktemp -d)"; JAR="$PRACE/cookies.txt"; B="http://127.0.0.1:$PORT"; CHYB=0
uklid() { [ -n "${SERVER_PID:-}" ] && kill "$SERVER_PID" 2>/dev/null || true; rm -rf "$PRACE"; }
trap uklid EXIT

echo "== syntaxe PHP"
find "$KOREN" -name '*.php' -not -path '*/.git/*' -not -path '*/dist/*' -print0 | xargs -0 -n1 php -l > /dev/null

echo "== čistá databáze a kopie projektu"
MYSQL=(mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER"); [ -n "$DB_PASS" ] && MYSQL+=(-p"$DB_PASS")
"${MYSQL[@]}" -e "DROP DATABASE IF EXISTS \`$DB_NAME\`; CREATE DATABASE \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci"
mkdir "$PRACE/web" && (cd "$KOREN" && git ls-files -z --cached --others --exclude-standard | while IFS= read -r -d '' s; do if [ -e "$s" ]; then printf '%s\0' "$s"; fi; done | tar --null -T - -cf - | tar -xf - -C "$PRACE/web") # soubory smazané a ještě nezapsané do gitu se nekopírují
mkdir -p "$PRACE/web/media" "$PRACE/web/storage/log" "$PRACE/web/storage/cache"
(cd "$PRACE/web" && exec php -S "127.0.0.1:$PORT" system/dev-router.php > "$PRACE/server.log" 2>&1) & SERVER_PID=$!
for i in $(seq 1 30); do curl -s -o /dev/null "$B/install.php" && break; sleep 0.3; done

over() { # over <popis> <očekávaný kód> <adresa> [hledaný text]
  local kod; kod=$(curl -s -b "$JAR" -c "$JAR" -A "Mozilla/5.0 test" -o "$PRACE/odpoved" -w '%{http_code}' "$B$3")
  if [ "$kod" != "$2" ] || grep -qE 'Fatal error|Warning:|Deprecated:|Notice:' "$PRACE/odpoved" || { [ -n "${4:-}" ] && ! grep -q "$4" "$PRACE/odpoved"; }; then
    echo "  CHYBA  $1 ($3): kód $kod, čekal jsem $2${4:+, text „$4“}"; CHYB=$((CHYB+1))
  else echo "  ok     $1"; fi
}

POSLEDNI=$(ls "$KOREN"/system/sql/migrace/*.sql | sed 's/.*\/\([0-9]*\)-.*/\1/' | sort -n | tail -1 | sed 's/^0*//')
grep -q "const PHPRS_VERZE_DB = $POSLEDNI;" "$KOREN/system/bootstrap.php" && echo "  ok     PHPRS_VERZE_DB odpovídá poslední migraci ($POSLEDNI)" || { echo "  CHYBA  PHPRS_VERZE_DB v system/bootstrap.php neodpovídá poslední migraci ($POSLEDNI)"; CHYB=$((CHYB+1)); }

echo "== jednotkové testy"
php "$KOREN/tools/testy.php" || CHYB=$((CHYB+1))

echo "== instalace"
HESLO="Test-$(date +%s)-heslo"
curl -s -o "$PRACE/odpoved" -X POST "$B/install.php" --data-urlencode "db_host=$DB_HOST" -d "db_port=$DB_PORT" -d "db_name=$DB_NAME" -d "db_user=$DB_USER" --data-urlencode "db_password=$DB_PASS" -d db_prefix=rs_ \
  --data-urlencode "nazev_webu=Testovací magazín" -d user=admin -d jmeno=Tester -d email= --data-urlencode "password=$HESLO" --data-urlencode "password2=$HESLO" -d layout=classic-newspaper
grep -q "Hotovo, magazín běží" "$PRACE/odpoved" || { echo "  CHYBA  instalace selhala"; sed 's/<[^>]*>//g' "$PRACE/odpoved" | grep -v '^\s*$' | head -20; exit 1; }
echo "  ok     instalace"
[ ! -f "$PRACE/web/install.php" ] && echo "  ok     instalátor se po sobě smazal" || { echo "  CHYBA  install.php po instalaci zůstal na místě"; CHYB=$((CHYB+1)); }

echo "== web"
over "hlavní stránka" 200 / "Testovací magazín"
over "článek" 200 /clanek/vitejte-v-phprs-3 "Vítejte"
over "rubrika" 200 /rubrika/aktuality
over "hledání" 200 "/hledani?q=phpRS"
for u in /rss.xml /feed.json /sitemap.xml /sitemap-news.xml /robots.txt /llms.txt /clanek/vitejte-v-phprs-3.md; do over "$u" 200 "$u"; done
over "neexistující stránka" 404 /tohle-neexistuje
over "system/ není přístupný" 403 /system/sql/schema.sql
over "config.php není přístupný" 403 /config.php
# nastavená šablona, která ve složce layout/ není (zrušená vestavěná „default“ před migrací, smazaná vlastní): web se vykreslí výchozí šablonou
"${MYSQL[@]}" "$DB_NAME" -e "UPDATE rs_config SET hodnota='default' WHERE promenna='layout'"
over "chybějící šablona – web běží na výchozí" 200 / "layout/classic-newspaper/style.css"
for l in minimal classic-newspaper modern-magazine; do
  "${MYSQL[@]}" "$DB_NAME" -e "UPDATE rs_config SET hodnota='$l' WHERE promenna='layout'"; over "šablona $l" 200 / "layout/$l/style.css"; over "šablona $l – článek" 200 /clanek/vitejte-v-phprs-3
done

echo "== administrace"
over "zapomenuté heslo – formulář" 200 "/admin.php?akce=heslo" "Poslat odkaz"
over "zapomenuté heslo – neplatný odkaz" 400 "/admin.php?akce=heslo&token=$(printf 'a%.0s' $(seq 1 64))" "Odkaz už neplatí"
over "bez přihlášení je jen login" 200 /admin.php "Heslo"
TOKEN=$(grep -o 'name="_csrf" value="[a-f0-9]*"' "$PRACE/odpoved" | head -1 | sed 's/.*value="//;s/"//')
kod=$(curl -s -b "$JAR" -c "$JAR" -o /dev/null -w '%{http_code}' -X POST "$B/admin.php" -d "_csrf=$TOKEN" -d user=admin -d password=spatne-heslo-123); [ "$kod" = 401 ] && echo "  ok     špatné heslo odmítnuto" || { echo "  CHYBA  špatné heslo: $kod"; CHYB=$((CHYB+1)); }
kod=$(curl -s -b "$JAR" -c "$JAR" -o /dev/null -w '%{http_code}' -X POST "$B/admin.php" -d user=admin --data-urlencode "password=$HESLO"); [ "$kod" = 400 ] && echo "  ok     POST bez CSRF odmítnut" || { echo "  CHYBA  CSRF: $kod"; CHYB=$((CHYB+1)); }
curl -s -b "$JAR" -c "$JAR" -o /dev/null -X POST "$B/admin.php" -d "_csrf=$TOKEN" -d user=admin --data-urlencode "password=$HESLO"
"${MYSQL[@]}" "$DB_NAME" -e "INSERT INTO rs_config VALUES ('rozsireni','novinky,komentare,ankety,statistika,presmerovani,reklama,newsletter,ctenari,push,asistent,jazyky') ON DUPLICATE KEY UPDATE hodnota=VALUES(hodnota)"
over "přehled" 200 /admin.php "Přehled"
for m in clanky "clanky&akce=novy" "clanky&akce=kalendar" "clanky&akce=titulni" "clanky&akce=odkazy" intergal topic stitky stranky news comment ankety stat reklama newsletter ctenari vzhled "bloky&schema=1" users presmerovani protokol; do over "modul $m" 200 "/admin.php?modul=$m"; done
over "rozšíření (samostatná položka nabídky)" 200 "/admin.php?modul=rozsireni" "Rozšíření"
for z in zakladni vzhled seo mereni cookies posta zalohy stav; do over "nastavení/$z" 200 "/admin.php?modul=config&zalozka=$z"; done
over "účet čtenáře" 200 /ctenar "Jsem tu poprvé"
"${MYSQL[@]}" "$DB_NAME" -e "UPDATE rs_clanky SET pristup = 1; INSERT INTO rs_config VALUES ('zamek_odstavcu','0') ON DUPLICATE KEY UPDATE hodnota='0'"
curl -s -o "$PRACE/odpoved" "$B/clanek/vitejte-v-phprs-3"; grep -q "rs-zamek" "$PRACE/odpoved" && echo "  ok     zamčený článek ukazuje výzvu" || { echo "  CHYBA  zamčený článek je vidět bez přihlášení"; CHYB=$((CHYB+1)); }
curl -s -o "$PRACE/odpoved" "$B/clanek/vitejte-v-phprs-3.md"; grep -q "admin.php" "$PRACE/odpoved" && { echo "  CHYBA  zamčený text uniká přes .md"; CHYB=$((CHYB+1)); } || echo "  ok     zamčený text neuniká přes .md"
kod=$(curl -s -H "Cookie: phprs_ctenar=1.9999999999.podvrh" "$B/clanek/vitejte-v-phprs-3"); grep -q "rs-zamek" <<< "$kod" && echo "  ok     podvržená cookie čtenáře zámek neodemkne" || { echo "  CHYBA  podvržená cookie čtenáře odemkla článek"; CHYB=$((CHYB+1)); }
"${MYSQL[@]}" "$DB_NAME" -e "UPDATE rs_clanky SET pristup = 0"
for u in /sw.js /push.json /manifest.webmanifest; do over "$u" 200 "$u"; done
kod=$(curl -s -o /dev/null -w '%{http_code}' -X POST -H 'Content-Type: application/json' -d '{"endpoint":"https://utocnik.example/x"}' "$B/push/odber"); [ "$kod" = 400 ] && echo "  ok     Web Push odmítne cizí adresu odběru" || { echo "  CHYBA  Web Push přijal cizí adresu: $kod"; CHYB=$((CHYB+1)); }
"${MYSQL[@]}" "$DB_NAME" -e "INSERT INTO rs_config VALUES ('jazyky_dalsi','en') ON DUPLICATE KEY UPDATE hodnota='en'"
over "anglická verze webu" 200 /en/ 'lang="en"'
kod=$(curl -s -o /dev/null -w '%{http_code}' "$B/en/clanek/vitejte-v-phprs-3"); [ "$kod" = 301 ] && echo "  ok     článek jiné jazykové verze přesměruje" || { echo "  CHYBA  jazykové přesměrování: $kod"; CHYB=$((CHYB+1)); }
over "neznámý modul" 403 "/admin.php?modul=neexistuje"

# neúspěšná validace článku musí vrátit formulář s hláškou, ne chybu 500 (dřív padala na chybějícím klíči)
curl -s -b "$JAR" -c "$JAR" -o "$PRACE/odpoved" "$B/admin.php?modul=clanky&akce=novy"
TOKEN=$(grep -o 'name="_csrf" value="[a-f0-9]*"' "$PRACE/odpoved" | head -1 | sed 's/.*value="//;s/"//')
kod=$(curl -s -b "$JAR" -c "$JAR" -o "$PRACE/odpoved" -w '%{http_code}' -X POST "$B/admin.php?modul=clanky&akce=uloz" -d "_csrf=$TOKEN" -d idc=0 -d titulek= -d tema=1)
[ "$kod" = 200 ] && grep -q 'name="titulek"' "$PRACE/odpoved" && echo "  ok     chyba ve formuláři článku vrátí formulář" || { echo "  CHYBA  validace článku: kód $kod"; CHYB=$((CHYB+1)); }

# oprávnění podle rubriky: redaktor omezený na jinou rubriku článek z první rubriky nevidí ani neotevře
"${MYSQL[@]}" "$DB_NAME" -e "INSERT INTO rs_topic (nazev, seo_link, popis) VALUES ('Jen pro test', 'jen-pro-test', '')"
RUB=$("${MYSQL[@]}" "$DB_NAME" -N -e "SELECT idt FROM rs_topic WHERE seo_link = 'jen-pro-test'")
CLANEK=$("${MYSQL[@]}" "$DB_NAME" -N -e "SELECT idc FROM rs_clanky ORDER BY idc LIMIT 1")
curl -s -b "$JAR" -c "$JAR" -o /dev/null -X POST "$B/admin.php?modul=users&akce=uloz" -d "_csrf=$TOKEN" -d idu=0 -d jmeno=Omezeny -d user=omezeny --data-urlencode "password=$HESLO" -d admin=1 -d "rubriky[]=$RUB"
JAR2="$PRACE/jar2"
TOKEN2=$(curl -s -c "$JAR2" "$B/admin.php" | grep -o 'name="_csrf" value="[a-f0-9]*"' | head -1 | sed 's/.*value="//;s/"//')
curl -s -b "$JAR2" -c "$JAR2" -o /dev/null -X POST "$B/admin.php" -d "_csrf=$TOKEN2" -d user=omezeny --data-urlencode "password=$HESLO"
kod=$(curl -s -b "$JAR2" -o "$PRACE/odpoved" -w '%{http_code}' "$B/admin.php?modul=clanky")
[ "$kod" = 200 ] && ! grep -q "akce=edit&amp;id=$CLANEK\"" "$PRACE/odpoved" && echo "  ok     omezený redaktor nevidí články cizí rubriky" || { echo "  CHYBA  oprávnění podle rubriky – výpis: kód $kod"; CHYB=$((CHYB+1)); }
kod=$(curl -s -b "$JAR2" -o /dev/null -w '%{http_code}' "$B/admin.php?modul=clanky&akce=edit&id=$CLANEK")
[ "$kod" = 404 ] && echo "  ok     omezený redaktor cizí článek neotevře" || { echo "  CHYBA  oprávnění podle rubriky – úprava: kód $kod"; CHYB=$((CHYB+1)); }
over "vizuální editor bloků" 200 "/?upravit=1" "rs-nastaveni"
# úprava článku přímo na webu: odkaz a formulář jen pro přihlášenou redakci
over "úprava na místě – odkaz pro redakci" 200 /clanek/vitejte-v-phprs-3 "rs-upravit-zde"
over "úprava na místě – formulář" 200 "/clanek/vitejte-v-phprs-3?upravit=text" "rs-upravit-text"
curl -s -o "$PRACE/odpoved" "$B/clanek/vitejte-v-phprs-3?upravit=text"; grep -q "rs-upravit" "$PRACE/odpoved" && { echo "  CHYBA  úprava na místě je vidět bez přihlášení"; CHYB=$((CHYB+1)); } || echo "  ok     úprava na místě jen pro přihlášené"
kod=$(curl -s -o "$PRACE/odpoved" -w '%{http_code}' "$B/?upravit=1"); grep -q "rs-nastaveni" "$PRACE/odpoved" && { echo "  CHYBA  vizuální editor je vidět bez přihlášení"; CHYB=$((CHYB+1)); } || echo "  ok     vizuální editor jen pro přihlášené"

if [ -s "$PRACE/web/storage/log/chyby.log" ]; then echo "== záznam chyb aplikace:"; cat "$PRACE/web/storage/log/chyby.log"; CHYB=$((CHYB+1)); fi
echo; [ "$CHYB" -eq 0 ] && echo "VŠE V POŘÁDKU" || { echo "NALEZENO CHYB: $CHYB"; exit 1; }
