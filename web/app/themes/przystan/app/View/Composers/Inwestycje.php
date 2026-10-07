<?php

namespace App\View\Composers;

use App\Elewacja;
use Przystan\Inwestycje\Inwestycja;
use Przystan\Inwestycje\TypTresci;
use Przystan\Mieszkania\Wyszukiwarka;
use Przystan\Polylang\Integracja;
use Roots\Acorn\View\Composer;

/**
 * Lista inwestycji i strona jednej inwestycji (harmonogram, elewacja, wpisy dziennika budowy).
 */
class Inwestycje extends Composer
{
    protected static $views = ['archive-inwestycja', 'single-inwestycja'];

    public function with(): array
    {
        $jezyk = Integracja::jezyk();

        if (! is_singular(TypTresci::TYP)) {
            return ['inwestycje' => TypTresci::wszystkie($jezyk)];
        }

        $inw = Inwestycja::zPosta(get_post());
        $mieszkania = $inw['mieszkan'] > 0 ? Wyszukiwarka::szukaj(['inwestycja' => $inw['id'], 'status' => 'wszystkie'], $jezyk) : [];

        return [
            'inw' => $inw,
            'mieszkania' => $mieszkania,
            'elewacja' => new Elewacja($inw['kondygnacje'], $inw['lokali_na_pietro']),
            'wpisy' => get_posts([
                'post_type' => 'post',
                'posts_per_page' => 3,
                'no_found_rows' => true,
                'lang' => $jezyk,
                'meta_key' => 'inwestycja',
                'meta_value' => $inw['id'],
            ]),
            'inne' => array_values(array_filter(TypTresci::wszystkie($jezyk), fn($i) => $i['id'] !== $inw['id'])),
        ];
    }
}
