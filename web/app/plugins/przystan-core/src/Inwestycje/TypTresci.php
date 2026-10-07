<?php

namespace Przystan\Inwestycje;

/**
 * Typ treści „Inwestycja”: lista pod /inwestycje/ (EN /en/developments/), strona każdej inwestycji.
 * Mieszkania i wpisy dziennika budowy wskazują inwestycję polem „inwestycja”.
 */
final class TypTresci
{
    public const TYP = 'inwestycja';

    /** Bazy adresów w językach (darmowy Polylang nie tłumaczy slugów typów treści). */
    public const BAZA_URL = ['pl' => 'inwestycje', 'en' => 'developments'];

    public const STATUSY = ['w_budowie', 'gotowe', 'planowana'];

    public static function rejestruj(): void
    {
        add_action('init', [self::class, 'typ']);
        add_action('init', [self::class, 'regulyEn'], 20);
        add_filter('post_type_link', [self::class, 'linkEn'], 10, 2);
        add_filter('post_type_archive_link', [self::class, 'linkArchiwumEn'], 10, 2);
        add_action('save_post_' . self::TYP, [\Przystan\Mieszkania\Wyszukiwarka::class, 'wyczysc']);
    }

    public static function typ(): void
    {
        register_post_type(self::TYP, [
            'labels' => [
                'name' => __('Inwestycje', 'przystan'),
                'singular_name' => __('Inwestycja', 'przystan'),
                'add_new_item' => __('Dodaj inwestycję', 'przystan'),
                'edit_item' => __('Edytuj inwestycję', 'przystan'),
                'all_items' => __('Wszystkie inwestycje', 'przystan'),
                'not_found' => __('Brak inwestycji', 'przystan'),
            ],
            'public' => true,
            'has_archive' => self::BAZA_URL['pl'],
            'query_var' => false, // ?inwestycja=ID to filtr wyszukiwarki, nie zapytanie o wpis
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-location-alt',
            'menu_position' => 4,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
            'rewrite' => ['slug' => self::BAZA_URL['pl'], 'with_front' => false],
        ]);
    }

    public static function regulyEn(): void
    {
        $baza = self::BAZA_URL['en'];
        add_rewrite_rule("^en/{$baza}/?$", 'index.php?post_type=' . self::TYP . '&lang=en', 'top');
        add_rewrite_rule("^en/{$baza}/([^/]+)/?$", 'index.php?post_type=' . self::TYP . '&name=$matches[1]&lang=en', 'top');
    }

    public static function linkEn(string $link, \WP_Post $post): string
    {
        if ($post->post_type !== self::TYP || ! function_exists('pll_get_post_language') || pll_get_post_language($post->ID) !== 'en') {
            return $link;
        }

        return home_url('/en/' . self::BAZA_URL['en'] . '/' . $post->post_name . '/');
    }

    public static function linkArchiwumEn(string $link, string $typ): string
    {
        if ($typ !== self::TYP || ! function_exists('pll_current_language') || pll_current_language() !== 'en') {
            return $link;
        }

        return home_url('/en/' . self::BAZA_URL['en'] . '/');
    }

    /**
     * Inwestycje w bieżącym języku, w kolejności z panelu (pole „Kolejność”).
     *
     * @return list<array<string, mixed>>
     */
    public static function wszystkie(string $jezyk): array
    {
        $posty = get_posts([
            'post_type' => self::TYP,
            'post_status' => 'publish',
            'posts_per_page' => 20,
            'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
            'lang' => $jezyk,
            'no_found_rows' => true,
        ]);

        return array_map([Inwestycja::class, 'zPosta'], $posty);
    }
}
