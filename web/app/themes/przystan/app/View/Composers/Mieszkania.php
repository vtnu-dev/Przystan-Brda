<?php

namespace App\View\Composers;

use Przystan\Mieszkania\Filtry;
use Przystan\Mieszkania\Wyszukiwarka;
use Przystan\Polylang\Integracja;
use Roots\Acorn\View\Composer;

/**
 * Strona „Mieszkania”: filtry z adresu URL, wynik wyszukiwania (działa też bez JavaScriptu)
 * i wszystkie mieszkania do narysowania elewacji.
 */
class Mieszkania extends Composer
{
    protected static $views = ['template-mieszkania'];

    public function with(): array
    {
        $jezyk = Integracja::jezyk();
        $filtry = Filtry::z(wp_unslash($_GET));
        $wszystkie = Wyszukiwarka::szukaj(['status' => 'wszystkie'], $jezyk);
        $wynik = Wyszukiwarka::szukaj($filtry, $jezyk);

        $aktywne = Filtry::doUrl($filtry) !== [];

        return [
            'filtry' => $filtry,
            'filtryAktywne' => $aktywne,
            'wszystkie' => $wszystkie,
            'wynik' => $wynik,
            'pasujace' => $aktywne ? array_column($wynik, 'id') : null,
            'restUrl' => rest_url('przystan/v1/mieszkania'),
        ];
    }
}
