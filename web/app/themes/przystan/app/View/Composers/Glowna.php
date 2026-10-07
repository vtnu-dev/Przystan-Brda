<?php

namespace App\View\Composers;

use Przystan\Mieszkania\Wyszukiwarka;
use Przystan\Polylang\Integracja;
use Roots\Acorn\View\Composer;

/**
 * Strona główna: pola ACF sekcji + mieszkania do elewacji + ostatnie wpisy dziennika budowy.
 */
class Glowna extends Composer
{
    protected static $views = ['front-page'];

    public function with(): array
    {
        $pole = fn(string $nazwa) => function_exists('get_field') ? get_field($nazwa) : null;

        $liczby = [];
        for ($i = 1; $i <= 4; $i++) {
            if ($pole("liczba_{$i}_wartosc")) {
                $liczby[] = ['wartosc' => $pole("liczba_{$i}_wartosc"), 'opis' => $pole("liczba_{$i}_opis")];
            }
        }

        $atuty = [];
        for ($i = 1; $i <= 3; $i++) {
            if ($pole("atut_{$i}_tytul")) {
                $atuty[] = ['tytul' => $pole("atut_{$i}_tytul"), 'opis' => $pole("atut_{$i}_opis"), 'zdjecie' => (int) $pole("atut_{$i}_zdjecie")];
            }
        }

        $mieszkania = Wyszukiwarka::szukaj(['status' => 'wszystkie'], Integracja::jezyk());

        return [
            'hero' => [
                'nadtytul' => $pole('hero_nadtytul'),
                'naglowek' => (string) $pole('hero_naglowek'),
                'wstep' => $pole('hero_wstep'),
                'zdjecie' => (int) $pole('hero_zdjecie'),
            ],
            'liczby' => $liczby,
            'atuty' => $atuty,
            'okolica' => [
                'naglowek' => $pole('okolica_naglowek'),
                'wstep' => $pole('okolica_wstep'),
                'zdjecie' => (int) $pole('okolica_zdjecie'),
            ],
            'mieszkania' => $mieszkania,
            'wolnych' => count(array_filter($mieszkania, fn($m) => $m['status'] === 'wolne')),
            'wpisy' => get_posts([
                'post_type' => 'post',
                'posts_per_page' => 3,
                'no_found_rows' => true,
                'ignore_sticky_posts' => true,
                'lang' => Integracja::jezyk(),
            ]),
        ];
    }
}
