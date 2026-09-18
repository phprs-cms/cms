-- phpRS 3.0 - struktura databáze
--
-- Názvy tabulek a sloupců jsou česky a vycházejí z původního phpRS (rs_clanky.titulek, rs_topic.nazev...).
-- Jde o nový systém: data ze starého phpRS 2 se nepřevádějí.
-- Předpona "rs_" se při instalaci nahradí předponou z config.php.
-- InnoDB s cizími klíči, utf8mb4, hesla přes password_hash().
--
-- Tento soubor je vždy úplné aktuální schéma pro novou instalaci. Každá změna se zároveň zapisuje
-- jako migrace do system/sql/migrace/NNNN-popis.sql, aby se stávající weby aktualizovaly samy.

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------------
-- Autoři (uživatelé administrace)
-- ---------------------------------------------------------------------------
CREATE TABLE rs_user (
    idu            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user           VARCHAR(40)  NOT NULL,                 -- přihlašovací jméno
    password       VARCHAR(255) NOT NULL,                 -- password_hash()
    jmeno          VARCHAR(100) NOT NULL DEFAULT '',
    email          VARCHAR(190) NOT NULL DEFAULT '',
    url            VARCHAR(255) NOT NULL DEFAULT '',
    admin          TINYINT UNSIGNED NOT NULL DEFAULT 0,   -- 0 autor, 1 redaktor, 2 admin (jako v 2.8)
    pravo_vydavat  BOOL NOT NULL DEFAULT 0,
    blokovat       BOOL NOT NULL DEFAULT 0,
    pocet_chyb     SMALLINT UNSIGNED NOT NULL DEFAULT 0,  -- neúspěšná přihlášení v řadě
    prostredi      VARCHAR(10)  NOT NULL DEFAULT '',      -- vzhled administrace: retro | 2026; prázdné = výchozí z konfigurace
    posledni_login DATETIME NULL,
    PRIMARY KEY (idu),
    UNIQUE KEY uq_user (user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Přístup uživatele k modulu administrace
CREATE TABLE rs_user_prava (
    fk_id_user   INT UNSIGNED NOT NULL,
    ident_modulu VARCHAR(30)  NOT NULL,
    PRIMARY KEY (fk_id_user, ident_modulu),
    CONSTRAINT fk_prava_user FOREIGN KEY (fk_id_user) REFERENCES rs_user (idu) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Nadřízený vidí a edituje články podřízeného
CREATE TABLE rs_vazby_prava (
    fk_id_nadrizeny INT UNSIGNED NOT NULL,
    fk_id_podrizeny INT UNSIGNED NOT NULL,
    PRIMARY KEY (fk_id_nadrizeny, fk_id_podrizeny),
    CONSTRAINT fk_vazby_nad FOREIGN KEY (fk_id_nadrizeny) REFERENCES rs_user (idu) ON DELETE CASCADE,
    CONSTRAINT fk_vazby_pod FOREIGN KEY (fk_id_podrizeny) REFERENCES rs_user (idu) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- ---------------------------------------------------------------------------
-- Konfigurace
-- ---------------------------------------------------------------------------
CREATE TABLE rs_config (
    promenna VARCHAR(60) NOT NULL,
    hodnota  TEXT NOT NULL,
    PRIMARY KEY (promenna)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE rs_levely (
    idl          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nazev_levelu VARCHAR(60) NOT NULL,
    hodnota      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    zakladni     BOOL NOT NULL DEFAULT 0,                 -- základní level nejde smazat
    PRIMARY KEY (idl)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Šablony článků: soubor layout/<layout>/cla_<soubor>.php
CREATE TABLE rs_cla_sab (
    ids            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nazev_cla_sab  VARCHAR(60) NOT NULL,
    soubor_cla_sab VARCHAR(60) NOT NULL,
    PRIMARY KEY (ids)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- ---------------------------------------------------------------------------
-- Rubriky a články
-- ---------------------------------------------------------------------------
CREATE TABLE rs_topic (
    idt       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nazev     VARCHAR(100) NOT NULL,
    seo_link  VARCHAR(120) NOT NULL,
    popis     TEXT NOT NULL,
    obrazek   VARCHAR(255) NOT NULL DEFAULT '',
    id_predka INT UNSIGNED NULL,                          -- nadřazená rubrika (strom)
    hodnost   SMALLINT UNSIGNED NOT NULL DEFAULT 100,     -- pořadí mezi sourozenci, vyšší = výš
    zobrazit  BOOL NOT NULL DEFAULT 1,
    PRIMARY KEY (idt),
    UNIQUE KEY uq_topic_seo (seo_link),
    CONSTRAINT fk_topic_predek FOREIGN KEY (id_predka) REFERENCES rs_topic (idt) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Skupiny souvisejících článků (seriály)
CREATE TABLE rs_skup_cl (
    ids        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nazev_skup VARCHAR(150) NOT NULL,
    PRIMARY KEY (ids)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE rs_ankety (
    ida       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titulek   VARCHAR(150) NOT NULL,
    otazka    TEXT NOT NULL,
    datum     DATETIME NOT NULL,
    kdo       INT UNSIGNED NULL,
    zobrazit  BOOL NOT NULL DEFAULT 1,
    uzavrena  BOOL NOT NULL DEFAULT 0,
    PRIMARY KEY (ida),
    CONSTRAINT fk_ankety_kdo FOREIGN KEY (kdo) REFERENCES rs_user (idu) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE rs_odpovedi (
    ido       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    anketa    INT UNSIGNED NOT NULL,
    odpoved   VARCHAR(255) NOT NULL,
    pocitadlo INT UNSIGNED NOT NULL DEFAULT 0,
    poradi    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (ido),
    CONSTRAINT fk_odpovedi_anketa FOREIGN KEY (anketa) REFERENCES rs_ankety (ida) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE rs_clanky (
    idc            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    seo_link       VARCHAR(160) NOT NULL,
    titulek        VARCHAR(255) NOT NULL,
    uvod           MEDIUMTEXT NOT NULL,
    text           MEDIUMTEXT NOT NULL,
    obrazek        VARCHAR(255) NOT NULL DEFAULT '',      -- nové ve 3.0: hlavní obrázek článku
    tema           INT UNSIGNED NOT NULL,                 -- rubrika
    autor          INT UNSIGNED NULL,
    datum          DATETIME NOT NULL,                     -- datum vydání (i budoucí)
    datum_pl       DATETIME NULL,                         -- datum stažení z hlavní stránky
    visible        BOOL NOT NULL DEFAULT 0,               -- "Vydat článek"
    zobr_na_indexu BOOL NOT NULL DEFAULT 1,
    priority       TINYINT UNSIGNED NOT NULL DEFAULT 0,
    typ_clanku     TINYINT UNSIGNED NOT NULL DEFAULT 1,   -- 1 dlouhý (náhled + celý), 2 krátký
    sablona        INT UNSIGNED NULL,
    level_clanku   INT UNSIGNED NULL,
    skupina_cl     INT UNSIGNED NULL,
    anketa_cl      INT UNSIGNED NULL,
    zdroj          VARCHAR(255) NOT NULL DEFAULT '',
    t_slova        VARCHAR(500) NOT NULL DEFAULT '',      -- klíčová slova
    seo_titulek    VARCHAR(255) NOT NULL DEFAULT '',      -- vlastní <title>, prázdné = titulek článku
    seo_popis      VARCHAR(320) NOT NULL DEFAULT '',      -- vlastní meta description, prázdné = z perexu
    noindex        BOOL NOT NULL DEFAULT 0,
    shrnuti        TEXT NULL,                             -- blok "Ve zkratce": jeden bod na řádek
    faq            TEXT NULL,                             -- otázky a odpovědi: otázka, pod ní odpověď, prázdný řádek
    povolit_kom    BOOL NOT NULL DEFAULT 1,
    kom            INT UNSIGNED NOT NULL DEFAULT 0,       -- počet komentářů
    visit          INT UNSIGNED NOT NULL DEFAULT 0,       -- počet přečtení
    hodnoceni      INT UNSIGNED NOT NULL DEFAULT 0,       -- součet známek
    mn_hodnoceni   INT UNSIGNED NOT NULL DEFAULT 0,       -- počet hlasů
    zmeneno        DATETIME NULL,
    PRIMARY KEY (idc),
    UNIQUE KEY uq_clanky_seo (seo_link),
    KEY ix_clanky_index (visible, zobr_na_indexu, priority, datum),
    KEY ix_clanky_tema (tema, visible, datum),
    KEY ix_clanky_autor (autor),
    FULLTEXT KEY ft_clanky (titulek, uvod, text, t_slova),
    CONSTRAINT fk_clanky_tema    FOREIGN KEY (tema)         REFERENCES rs_topic (idt),
    CONSTRAINT fk_clanky_autor   FOREIGN KEY (autor)        REFERENCES rs_user (idu)    ON DELETE SET NULL,
    CONSTRAINT fk_clanky_sablona FOREIGN KEY (sablona)      REFERENCES rs_cla_sab (ids) ON DELETE SET NULL,
    CONSTRAINT fk_clanky_level   FOREIGN KEY (level_clanku) REFERENCES rs_levely (idl)  ON DELETE SET NULL,
    CONSTRAINT fk_clanky_skupina FOREIGN KEY (skupina_cl)   REFERENCES rs_skup_cl (ids) ON DELETE SET NULL,
    CONSTRAINT fk_clanky_anketa  FOREIGN KEY (anketa_cl)    REFERENCES rs_ankety (ida)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE rs_komentare (
    idk        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    clanek     INT UNSIGNED NOT NULL,                     -- rs_clanky.idc (v 2.8 to byl link)
    reakce_na  INT UNSIGNED NULL,
    datum      DATETIME NOT NULL,
    titulek    VARCHAR(150) NOT NULL DEFAULT '',
    obsah      TEXT NOT NULL,
    od         VARCHAR(60) NOT NULL,
    od_mail    VARCHAR(190) NOT NULL DEFAULT '',
    od_ip      VARCHAR(45) NOT NULL DEFAULT '',
    zobrazit   BOOL NOT NULL DEFAULT 1,
    PRIMARY KEY (idk),
    KEY ix_komentare_clanek (clanek, datum),
    KEY ix_komentare_stav (zobrazit, datum),
    CONSTRAINT fk_komentare_clanek FOREIGN KEY (clanek)    REFERENCES rs_clanky (idc)    ON DELETE CASCADE,
    CONSTRAINT fk_komentare_reakce FOREIGN KEY (reakce_na) REFERENCES rs_komentare (idk) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- ---------------------------------------------------------------------------
-- Galerie obrázků
-- ---------------------------------------------------------------------------
CREATE TABLE rs_imggal_sekce (
    ids   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nazev VARCHAR(100) NOT NULL,
    PRIMARY KEY (ids)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE rs_imggal_obr (
    ido         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    vlastnik    INT UNSIGNED NULL,
    sekce       INT UNSIGNED NULL,                         -- složka
    nazev       VARCHAR(150) NOT NULL DEFAULT '',          -- slouží i jako alternativní text (alt)
    popis       VARCHAR(500) NOT NULL DEFAULT '',          -- popisek pod obrázkem
    obr_poloha  VARCHAR(255) NOT NULL,                     -- cesta od kořene webu: media/2026/09/foto.jpg
    obr_width   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    obr_height  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    obr_vel     INT UNSIGNED NOT NULL DEFAULT 0,           -- velikost souboru v bajtech
    nahl_poloha VARCHAR(255) NOT NULL DEFAULT '',
    nahl_width  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    nahl_height SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    datum       DATETIME NOT NULL,
    PRIMARY KEY (ido),
    KEY ix_imggal_datum (datum),
    KEY ix_imggal_sekce (sekce),
    CONSTRAINT fk_imggal_sekce FOREIGN KEY (sekce) REFERENCES rs_imggal_sekce (ids) ON DELETE SET NULL,
    CONSTRAINT fk_imggal_vlastnik FOREIGN KEY (vlastnik) REFERENCES rs_user (idu) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- ---------------------------------------------------------------------------
-- Novinky a bloky
-- ---------------------------------------------------------------------------
CREATE TABLE rs_news (
    idn       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titulek   VARCHAR(150) NOT NULL,
    informace TEXT NOT NULL,
    datum     DATETIME NOT NULL,
    PRIMARY KEY (idn),
    KEY ix_news_datum (datum)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE rs_bloky (
    idb          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nazev        VARCHAR(100) NOT NULL,
    obsah        MEDIUMTEXT NOT NULL,                     -- HTML běžného bloku
    typ          TINYINT UNSIGNED NOT NULL DEFAULT 1,     -- vzhled: 1 běžný, 2 podbarvený, 3 zvýrazněný nadpis, 4 v rámečku, 5 bez nadpisu
    hodnost      SMALLINT UNSIGNED NOT NULL DEFAULT 100,  -- pořadí v zóně, vyšší = výš (nastavuje se přetažením)
    sys_funkce   VARCHAR(30) NOT NULL DEFAULT '',         -- '' běžný blok; ank, nov, rub, kal, hlb nebo zkratka plug-inu
    data_sys     VARCHAR(255) NOT NULL DEFAULT '',
    zobrazit     BOOL NOT NULL DEFAULT 1,
    zobrazit_kde TINYINT UNSIGNED NOT NULL DEFAULT 0,     -- 0 všude, 1 jen hlavní stránka, 2 všude mimo ni
    zona         VARCHAR(20) NOT NULL DEFAULT 'prava',    -- hlavicka, leva, nad, pod, prava, paticka
    level_blok   INT UNSIGNED NULL,
    PRIMARY KEY (idb),
    KEY ix_bloky_zona (zona, hodnost),
    CONSTRAINT fk_bloky_level   FOREIGN KEY (level_blok) REFERENCES rs_levely (idl) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Ochrana proti opakování akce ze stejné IP (přihlášení, hlasování v anketě, hodnocení, komentáře)
CREATE TABLE rs_kontrola_ip (
    idk       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ip_adresa VARCHAR(45) NOT NULL,
    typ       VARCHAR(20) NOT NULL,
    cil       INT UNSIGNED NOT NULL DEFAULT 0,
    cas       DATETIME NOT NULL,
    PRIMARY KEY (idk),
    KEY ix_kontrola (typ, cil, ip_adresa, cas)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Ve kterých článcích je obrázek použitý (přepočítá se při uložení článku)
CREATE TABLE rs_imggal_pouziti (
    ido INT UNSIGNED NOT NULL,
    idc INT UNSIGNED NOT NULL,
    PRIMARY KEY (ido, idc),
    KEY ix_pouziti_clanek (idc),
    CONSTRAINT fk_pouziti_obr FOREIGN KEY (ido) REFERENCES rs_imggal_obr (ido) ON DELETE CASCADE,
    CONSTRAINT fk_pouziti_clanek FOREIGN KEY (idc) REFERENCES rs_clanky (idc) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- ---------------------------------------------------------------------------
-- Štítky článků, historie verzí článku a statické stránky (O nás, Kontakt...).
CREATE TABLE rs_stitky (
    ids      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nazev    VARCHAR(80) NOT NULL,
    seo_link VARCHAR(100) NOT NULL,
    PRIMARY KEY (ids),
    UNIQUE KEY uq_stitky_seo (seo_link)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
CREATE TABLE rs_clanky_stitky (
    idc INT UNSIGNED NOT NULL,
    ids INT UNSIGNED NOT NULL,
    PRIMARY KEY (idc, ids),
    KEY ix_clanky_stitky_stitek (ids),
    CONSTRAINT fk_cs_clanek FOREIGN KEY (idc) REFERENCES rs_clanky (idc) ON DELETE CASCADE,
    CONSTRAINT fk_cs_stitek FOREIGN KEY (ids) REFERENCES rs_stitky (ids) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
CREATE TABLE rs_clanky_revize (
    idr     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    idc     INT UNSIGNED NOT NULL,
    datum   DATETIME NOT NULL,
    kdo     INT UNSIGNED NULL,
    titulek VARCHAR(255) NOT NULL,
    uvod    MEDIUMTEXT NOT NULL,
    text    MEDIUMTEXT NOT NULL,
    PRIMARY KEY (idr),
    KEY ix_revize_clanek (idc, datum),
    CONSTRAINT fk_revize_clanek FOREIGN KEY (idc) REFERENCES rs_clanky (idc) ON DELETE CASCADE,
    CONSTRAINT fk_revize_kdo FOREIGN KEY (kdo) REFERENCES rs_user (idu) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
CREATE TABLE rs_stranky (
    ids      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    seo_link VARCHAR(120) NOT NULL,
    titulek  VARCHAR(200) NOT NULL,
    popis    VARCHAR(300) NOT NULL DEFAULT '',           -- meta description
    text     MEDIUMTEXT NOT NULL,
    zobrazit BOOL NOT NULL DEFAULT 1,
    v_menu   BOOL NOT NULL DEFAULT 1,                     -- odkaz v patičce / navigaci webu
    poradi   SMALLINT UNSIGNED NOT NULL DEFAULT 100,
    zmeneno  DATETIME NULL,
    PRIMARY KEY (ids),
    UNIQUE KEY uq_stranky_seo (seo_link)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- ---------------------------------------------------------------------------
-- Přesměrování a evidence souhlasů
CREATE TABLE rs_presmerovani (
    idp       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    z_adresy  VARCHAR(255) NOT NULL,                     -- cesta na webu bez úvodního lomítka: clanek/stara-adresa
    na_adresu VARCHAR(255) NOT NULL,                     -- cesta na webu, nebo celá adresa https://...
    pocet     INT UNSIGNED NOT NULL DEFAULT 0,           -- kolikrát bylo přesměrování použito
    vytvoreno DATETIME NOT NULL,
    PRIMARY KEY (idp),
    UNIQUE KEY uq_presmerovani (z_adresy)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
CREATE TABLE rs_souhlasy (
    ids         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_souhlasu CHAR(32) NOT NULL,                       -- náhodný identifikátor uložený v cookie návštěvníka
    cas         DATETIME NOT NULL,
    kategorie   VARCHAR(60) NOT NULL,                    -- "analytika,marketing" nebo "nic"
    PRIMARY KEY (ids),
    KEY ix_souhlasy_cas (cas),
    KEY ix_souhlasy_id (id_souhlasu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- ---------------------------------------------------------------------------
-- Statistika bez cookies
CREATE TABLE rs_stat_dny (
    den       DATE NOT NULL,
    navstevy  INT UNSIGNED NOT NULL DEFAULT 0,            -- unikátní návštěvníci dne
    zobrazeni INT UNSIGNED NOT NULL DEFAULT 0,            -- zobrazené stránky
    PRIMARY KEY (den)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
-- Otisk návštěvníka = hash(IP + prohlížeč + denní sůl). Druhý den už nejde spojit s předchozím; starší řádky se mažou.
CREATE TABLE rs_stat_navstevnici (
    den   DATE NOT NULL,
    otisk CHAR(32) NOT NULL,
    PRIMARY KEY (den, otisk)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
CREATE TABLE rs_stat_clanky (
    den   DATE NOT NULL,
    idc   INT UNSIGNED NOT NULL,
    pocet INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (den, idc),
    KEY ix_stat_clanky_idc (idc),
    CONSTRAINT fk_stat_clanek FOREIGN KEY (idc) REFERENCES rs_clanky (idc) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
CREATE TABLE rs_stat_zdroje (
    den   DATE NOT NULL,
    zdroj VARCHAR(100) NOT NULL,                          -- doména, ze které návštěvník přišel
    pocet INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (den, zdroj)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- ---------------------------------------------------------------------------
-- Reklamní systém: bannery a reklamní kódy přiřazené k pozicím na webu.
CREATE TABLE rs_reklama (
    idr           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nazev         VARCHAR(150) NOT NULL,                  -- interní název (klient, kampaň)
    pozice        VARCHAR(40) NOT NULL,                   -- sloupec | hlavicka | pod-clankem | paticka
    typ           VARCHAR(10) NOT NULL DEFAULT 'obrazek', -- obrazek | kod
    obrazek       VARCHAR(255) NOT NULL DEFAULT '',
    cil_url       VARCHAR(500) NOT NULL DEFAULT '',
    kod           MEDIUMTEXT NULL,                        -- HTML/JS reklamní sítě
    platna_od     DATETIME NULL,
    platna_do     DATETIME NULL,
    aktivni       BOOL NOT NULL DEFAULT 1,
    vaha          TINYINT UNSIGNED NOT NULL DEFAULT 1,    -- vyšší = zobrazuje se častěji
    max_zobrazeni INT UNSIGNED NULL,                      -- strop kampaně, NULL = bez omezení
    zobrazeni     INT UNSIGNED NOT NULL DEFAULT 0,
    kliky         INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (idr),
    KEY ix_reklama_pozice (pozice, aktivni)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
