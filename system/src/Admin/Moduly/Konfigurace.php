<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Modul;
use PhpRS\Core\Response;
use PhpRS\Core\Settings;

/**
 * Konfigurace systému - základní nastavení webu (tabulka rs_config).
 */
final class Konfigurace extends Modul
{
    public const string IDENT = 'config';
    public const string NAZEV = 'Konfigurace';
    public const bool JEN_ADMIN = true;

    private const array ZASKRTAVACI = ['hlidat_platnost', 'povolit_komentare'];

    protected function akceVypis(): Response
    {
        $hodnoty = [];
        foreach (array_keys(Settings::DEFAULTS) as $klic) {
            $hodnoty[$klic] = $this->app->settings()->get($klic);
        }

        return $this->view('vypis', 'Konfigurace systému', [
            'hodnoty' => $hodnoty,
            'layouty' => \PhpRS\Front\Layouty::seznam(),
            'prostredi' => \PhpRS\Admin\Kernel::PROSTREDI,
            'ankety' => $this->db->pairs('SELECT ida, titulek FROM {ankety} WHERE zobrazit = 1 ORDER BY ida DESC'),
        ]);
    }

    protected function akceUloz(): Response
    {
        if (!$this->request->isPost()) {
            return $this->zpet();
        }
        $r = $this->request;
        $nastaveni = $this->app->settings();

        foreach (['nazev_webu', 'popis_webu', 'klicova_slova', 'email_webu'] as $klic) {
            $nastaveni->set($klic, $r->post($klic));
        }
        foreach (self::ZASKRTAVACI as $klic) {
            $nastaveni->set($klic, $r->postBool($klic) ? '1' : '0');
        }
        $nastaveni->set('pocet_clanku', (string) max(1, min(100, $r->postInt('pocet_clanku', 7))));
        $nastaveni->set('pocet_novinek', (string) max(0, min(50, $r->postInt('pocet_novinek', 3))));
        $nastaveni->set('aktivni_anketa', (string) $r->postInt('aktivni_anketa'));

        if (isset(\PhpRS\Admin\Kernel::PROSTREDI[$r->post('prostredi_admin')])) {
            $nastaveni->set('prostredi_admin', $r->post('prostredi_admin'));
        }

        $layout = $r->post('layout');
        $layouty = \PhpRS\Front\Layouty::seznam();
        if (isset($layouty[$layout]) && $layout !== $nastaveni->get('layout')) {
            // nová šablona webu přináší i rozvržení stránky, které jí sluší; změnit ho lze v Úpravě bloků
            $nastaveni->set('layout', $layout);
            $nastaveni->set('rozvrzeni', $layouty[$layout]['rozvrzeni']);
        }

        return $this->zpet('Nastavení bylo uloženo.');
    }
}
