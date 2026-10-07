<?php

namespace App\View\Composers;

use App\Strony;
use Przystan\Polylang\Integracja;
use Przystan\Ustawienia\Ustawienia;
use Roots\Acorn\View\Composer;

/**
 * Dane wspólne dla wszystkich widoków: kontakt z ustawień, języki, adresy głównych stron.
 */
class App extends Composer
{
    protected static $views = ['*'];

    public function with(): array
    {
        return [
            'siteName' => get_bloginfo('name', 'display'),
            'kontakt' => $this->kontakt(),
            'jezyki' => $this->jezyki(),
            'jezyk' => Integracja::jezyk(),
            'urlGlowna' => Strony::strefaGlowna(),
            'urlMieszkania' => Strony::url(Strony::MIESZKANIA),
            'urlKontakt' => Strony::url(Strony::KONTAKT),
            'urlOkolica' => Strony::url(Strony::OKOLICA),
            'urlUlubione' => Strony::url(Strony::POROWNANIE),
        ];
    }

    /** @return array<string, string> */
    private function kontakt(): array
    {
        $u = Ustawienia::wszystkie();

        return [
            'telefon' => $u['telefon'],
            'telefon_href' => 'tel:' . preg_replace('/[^0-9+]/', '', $u['telefon']),
            'email' => $u['email'],
            'adres' => Integracja::tlumacz($u['adres']),
            'termin' => Integracja::tlumacz($u['termin']),
        ];
    }

    /**
     * Przełącznik języka z Polylang (tylko języki z tłumaczeniem bieżącej strony, bez pustych linków).
     *
     * @return list<array{kod: string, nazwa: string, url: string, aktywny: bool}>
     */
    private function jezyki(): array
    {
        if (! function_exists('pll_the_languages')) {
            return [];
        }
        $lista = pll_the_languages(['raw' => 1, 'hide_if_no_translation' => 0, 'hide_if_empty' => 0]);

        return array_values(array_map(fn($j) => [
            'kod' => strtoupper((string) $j['slug']),
            'nazwa' => (string) $j['name'],
            'url' => (string) $j['url'],
            'aktywny' => (bool) $j['current_lang'],
            'locale' => str_replace('_', '-', (string) $j['locale']),
        ], is_array($lista) ? $lista : []));
    }
}
