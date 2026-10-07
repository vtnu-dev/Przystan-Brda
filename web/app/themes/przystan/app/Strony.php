<?php

namespace App;

/**
 * Odnajduje strony po szablonie w bieżącym języku (np. „Mieszkania” po PL i EN),
 * żeby linki w motywie nie zależały od sluga ani ID wpisanego na sztywno.
 */
final class Strony
{
    public const MIESZKANIA = 'template-mieszkania.blade.php';
    public const OKOLICA = 'template-okolica.blade.php';
    public const KONTAKT = 'template-kontakt.blade.php';

    /** @var array<string, int> */
    private static array $cache = [];

    public static function id(string $szablon): int
    {
        $jezyk = function_exists('pll_current_language') ? (string) pll_current_language('slug') : '';
        $klucz = $szablon . '|' . $jezyk;

        if (! isset(self::$cache[$klucz])) {
            $args = [
                'post_type' => 'page',
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'fields' => 'ids',
                'no_found_rows' => true,
                'meta_key' => '_wp_page_template',
                'meta_value' => $szablon,
            ];
            if ($jezyk !== '') {
                $args['lang'] = $jezyk;
            }
            $ids = get_posts($args);
            self::$cache[$klucz] = (int) ($ids[0] ?? 0);
        }

        return self::$cache[$klucz];
    }

    public static function url(string $szablon): string
    {
        $id = self::id($szablon);

        return $id ? (string) get_permalink($id) : home_url('/');
    }

    public static function strefaGlowna(): string
    {
        return function_exists('pll_home_url') ? pll_home_url() : home_url('/');
    }
}
