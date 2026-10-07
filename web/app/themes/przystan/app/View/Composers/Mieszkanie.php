<?php

namespace App\View\Composers;

use App\Elewacja;
use Przystan\Inwestycje\Inwestycja;
use Przystan\Mieszkania\Mieszkanie as Dane;
use Przystan\Mieszkania\Wyszukiwarka;
use Przystan\Polylang\Integracja;
use Roots\Acorn\View\Composer;

/**
 * Karta mieszkania: dane, inwestycja, sąsiednie mieszkania na tym samym piętrze, elewacja z podświetleniem.
 */
class Mieszkanie extends Composer
{
    protected static $views = ['single-mieszkanie'];

    public function with(): array
    {
        $m = Dane::zPosta(get_post());
        $inwestycja = Inwestycja::zId($m['inwestycja']);
        $wszystkie = Wyszukiwarka::szukaj(['inwestycja' => $m['inwestycja'], 'status' => 'wszystkie'], Integracja::jezyk());

        $naPietrze = array_values(array_filter(
            $wszystkie,
            fn($inne) => $inne['pietro'] === $m['pietro'] && $inne['id'] !== $m['id'] && $inne['status'] !== 'sprzedane',
        ));

        return [
            'm' => $m,
            'inwestycja' => $inwestycja,
            'elewacja' => $inwestycja ? new Elewacja($inwestycja['kondygnacje'], $inwestycja['lokali_na_pietro']) : new Elewacja(),
            'wszystkie' => $wszystkie,
            'naPietrze' => array_slice($naPietrze, 0, 3),
        ];
    }
}
