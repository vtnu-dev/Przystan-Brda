<?php

namespace App\View\Composers;

use Przystan\Mieszkania\Mieszkanie as Dane;
use Przystan\Mieszkania\Wyszukiwarka;
use Przystan\Polylang\Integracja;
use Roots\Acorn\View\Composer;

/**
 * Karta mieszkania: dane, sąsiednie mieszkania na tym samym piętrze, elewacja z podświetleniem.
 */
class Mieszkanie extends Composer
{
    protected static $views = ['single-mieszkanie'];

    public function with(): array
    {
        $m = Dane::zPosta(get_post());
        $wszystkie = Wyszukiwarka::szukaj(['status' => 'wszystkie'], Integracja::jezyk());

        $naPietrze = array_values(array_filter(
            $wszystkie,
            fn($inne) => $inne['pietro'] === $m['pietro'] && $inne['id'] !== $m['id'] && $inne['status'] !== 'sprzedane',
        ));

        return [
            'm' => $m,
            'wszystkie' => $wszystkie,
            'naPietrze' => array_slice($naPietrze, 0, 3),
        ];
    }
}
