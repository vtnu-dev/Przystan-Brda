<?php

namespace App\View\Composers;

use App\Elewacja;
use Przystan\Inwestycje\TypTresci as Inwestycje;
use Przystan\Mieszkania\Filtry;
use Przystan\Mieszkania\Wyszukiwarka;
use Przystan\Polylang\Integracja;
use Roots\Acorn\View\Composer;

/**
 * Strona „Mieszkania”: wybór inwestycji, filtry z adresu URL, wynik wyszukiwania (działa też bez JavaScriptu)
 * i mieszkania wybranej inwestycji do narysowania jej elewacji.
 */
class Mieszkania extends Composer
{
    protected static $views = ['template-mieszkania'];

    public function with(): array
    {
        $jezyk = Integracja::jezyk();
        $inwestycje = array_values(array_filter(Inwestycje::wszystkie($jezyk), fn($i) => $i['mieszkan'] > 0));

        $filtry = Filtry::z(wp_unslash($_GET));
        $ids = array_column($inwestycje, 'id');
        if (! isset($filtry['inwestycja']) || ! in_array($filtry['inwestycja'], $ids, true)) {
            $filtry['inwestycja'] = $ids[0] ?? 0;
        }
        $wybrana = $inwestycje[array_search($filtry['inwestycja'], $ids, true) ?: 0] ?? null;

        $wszystkie = Wyszukiwarka::szukaj(['inwestycja' => $filtry['inwestycja'], 'status' => 'wszystkie'], $jezyk);
        $wynik = Wyszukiwarka::szukaj($filtry, $jezyk);
        $bezInwestycji = $filtry;
        unset($bezInwestycji['inwestycja']);
        $aktywne = Filtry::doUrl($bezInwestycji) !== [];

        return [
            'inwestycje' => $inwestycje,
            'wybrana' => $wybrana,
            'elewacja' => $wybrana ? new Elewacja($wybrana['kondygnacje'], $wybrana['lokali_na_pietro']) : new Elewacja(),
            'filtry' => $filtry,
            'filtryAktywne' => $aktywne,
            'wszystkie' => $wszystkie,
            'wynik' => $wynik,
            'pasujace' => $aktywne ? array_column($wynik, 'id') : null,
            'restUrl' => rest_url('przystan/v1/mieszkania'),
        ];
    }
}
