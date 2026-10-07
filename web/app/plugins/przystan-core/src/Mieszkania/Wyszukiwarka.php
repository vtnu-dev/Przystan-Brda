<?php

namespace Przystan\Mieszkania;

/**
 * Wyszukiwarka mieszkań: jedna logika dla endpointu REST i szablonu PHP (wersja bez JavaScriptu).
 */
final class Wyszukiwarka
{
    public const GRUPA_CACHE = 'przystan_mieszkania';

    /**
     * Argumenty WP_Query dla znormalizowanych filtrów (zob. Filtry::z). Czysta funkcja, testowana bez WordPressa.
     *
     * @param  array<string, mixed>  $filtry
     * @return array<string, mixed>
     */
    public static function argumenty(array $filtry, string $jezyk): array
    {
        $meta = [
            'relation' => 'AND',
            // Nazwane klauzule służą do sortowania: piętro, potem pozycja w rzędzie.
            'pietro_sort' => ['key' => 'pietro', 'type' => 'NUMERIC'],
            'pozycja_sort' => ['key' => 'pozycja', 'type' => 'NUMERIC'],
        ];

        if (isset($filtry['inwestycja'])) {
            $meta[] = ['key' => 'inwestycja', 'value' => $filtry['inwestycja'], 'compare' => '=', 'type' => 'NUMERIC'];
        }
        if (! empty($filtry['pokoje'])) {
            $meta[] = ['key' => 'pokoje', 'value' => $filtry['pokoje'], 'compare' => 'IN', 'type' => 'NUMERIC'];
        }
        if (isset($filtry['pietro'])) {
            $meta[] = ['key' => 'pietro', 'value' => $filtry['pietro'], 'compare' => '=', 'type' => 'NUMERIC'];
        }
        if (isset($filtry['metraz_min'])) {
            $meta[] = ['key' => 'metraz', 'value' => $filtry['metraz_min'], 'compare' => '>=', 'type' => 'DECIMAL(6,2)'];
        }
        if (isset($filtry['metraz_max'])) {
            $meta[] = ['key' => 'metraz', 'value' => $filtry['metraz_max'], 'compare' => '<=', 'type' => 'DECIMAL(6,2)'];
        }
        if (isset($filtry['status']) && $filtry['status'] !== 'wszystkie') {
            $meta[] = ['key' => 'status', 'value' => $filtry['status'], 'compare' => '='];
        }
        if (! empty($filtry['widok'])) {
            $meta[] = ['key' => 'widok_na_rzeke', 'value' => '1', 'compare' => '='];
        }
        if (! empty($filtry['balkon'])) {
            $meta[] = ['key' => 'balkon_m2', 'value' => 0, 'compare' => '>', 'type' => 'DECIMAL(6,2)'];
        }

        $args = [
            'post_type' => 'mieszkanie',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'lang' => $jezyk,
            'meta_query' => $meta,
            'orderby' => ['pietro_sort' => 'ASC', 'pozycja_sort' => 'ASC'],
        ];
        if (! empty($filtry['ids'])) {
            $args['post__in'] = $filtry['ids'];
        }

        return $args;
    }

    /** @param array<string, mixed> $filtry */
    public static function kluczCache(array $filtry, string $jezyk): string
    {
        ksort($filtry);

        return 'przystan_m_' . md5($jezyk . '|' . serialize($filtry));
    }

    /**
     * Wykonuje wyszukiwanie. Wynik (tablice gotowe do JSON) trzymamy w cache,
     * który czyści zapis dowolnego mieszkania (Wyszukiwarka::wyczysc).
     *
     * @param  array<string, mixed>  $filtry
     * @return list<array<string, mixed>>
     */
    public static function szukaj(array $filtry, string $jezyk): array
    {
        $wersja = (int) get_option('przystan_mieszkania_wersja', 1);
        $klucz = self::kluczCache($filtry, $jezyk) . '_v' . $wersja;

        $wynik = get_transient($klucz);
        if (is_array($wynik)) {
            return $wynik;
        }

        $zapytanie = new \WP_Query(self::argumenty($filtry, $jezyk));
        $wynik = array_map([Mieszkanie::class, 'zPosta'], $zapytanie->posts);

        set_transient($klucz, $wynik, HOUR_IN_SECONDS);

        return $wynik;
    }

    /**
     * Unieważnia wszystkie zapamiętane wyniki naraz (podbicie wersji zamiast kasowania wielu transientów).
     */
    public static function wyczysc(): void
    {
        update_option('przystan_mieszkania_wersja', (int) get_option('przystan_mieszkania_wersja', 1) + 1, false);
    }
}
