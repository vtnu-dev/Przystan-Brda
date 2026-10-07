<?php

namespace Przystan\Mieszkania;

/**
 * Typ treści „Mieszkanie”: adresy /mieszkania/m-31/ (PL) i /en/apartments/m-31/ (EN),
 * kolumny i sortowanie w panelu, czyszczenie cache wyszukiwarki po zapisie.
 */
final class TypTresci
{
    public const TYP = 'mieszkanie';

    /** Bazy adresów kart mieszkań w poszczególnych językach (darmowy Polylang nie tłumaczy slugów typów treści). */
    public const BAZA_URL = ['pl' => 'mieszkania', 'en' => 'apartments'];

    public static function rejestruj(): void
    {
        add_action('init', [self::class, 'typ']);
        add_action('init', [self::class, 'regulyEn'], 20);
        add_filter('post_type_link', [self::class, 'linkEn'], 10, 2);

        add_filter('manage_' . self::TYP . '_posts_columns', [self::class, 'kolumny']);
        add_action('manage_' . self::TYP . '_posts_custom_column', [self::class, 'kolumna'], 10, 2);
        add_filter('manage_edit-' . self::TYP . '_sortable_columns', [self::class, 'sortowalne']);
        add_action('pre_get_posts', [self::class, 'sortowanieWPanelu']);

        add_action('save_post_' . self::TYP, [Wyszukiwarka::class, 'wyczysc']);
        add_action('deleted_post', [Wyszukiwarka::class, 'wyczysc']);
    }

    public static function typ(): void
    {
        register_post_type(self::TYP, [
            'labels' => [
                'name' => __('Mieszkania', 'przystan'),
                'singular_name' => __('Mieszkanie', 'przystan'),
                'add_new_item' => __('Dodaj mieszkanie', 'przystan'),
                'edit_item' => __('Edytuj mieszkanie', 'przystan'),
                'all_items' => __('Wszystkie mieszkania', 'przystan'),
                'search_items' => __('Szukaj mieszkań', 'przystan'),
                'not_found' => __('Brak mieszkań', 'przystan'),
            ],
            'public' => true,
            'has_archive' => false, // lista mieszkań to zwykła strona z wyszukiwarką
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-building',
            'menu_position' => 5,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'rewrite' => ['slug' => self::BAZA_URL['pl'], 'with_front' => false],
        ]);
    }

    /** Dodatkowa reguła dla angielskiej bazy adresu: /en/apartments/m-31/. */
    public static function regulyEn(): void
    {
        add_rewrite_rule(
            '^en/' . self::BAZA_URL['en'] . '/([^/]+)/?$',
            'index.php?post_type=' . self::TYP . '&name=$matches[1]&lang=en',
            'top'
        );
    }

    public static function linkEn(string $link, \WP_Post $post): string
    {
        if ($post->post_type !== self::TYP || ! function_exists('pll_get_post_language')) {
            return $link;
        }
        if (pll_get_post_language($post->ID) !== 'en') {
            return $link;
        }

        return home_url('/en/' . self::BAZA_URL['en'] . '/' . $post->post_name . '/');
    }

    /** @param array<string, string> $kolumny */
    public static function kolumny(array $kolumny): array
    {
        $nowe = [];
        foreach ($kolumny as $klucz => $nazwa) {
            $nowe[$klucz] = $nazwa;
            if ($klucz === 'title') {
                $nowe['pietro'] = __('Piętro', 'przystan');
                $nowe['pokoje'] = __('Pokoje', 'przystan');
                $nowe['metraz'] = __('Metraż', 'przystan');
                $nowe['cena'] = __('Cena', 'przystan');
                $nowe['status'] = __('Status', 'przystan');
            }
        }
        unset($nowe['date']);

        return $nowe;
    }

    public static function kolumna(string $kolumna, int $postId): void
    {
        $m = Mieszkanie::zId($postId);
        echo match ($kolumna) {
            'pietro' => esc_html(Mieszkanie::nazwaPietra($m['pietro'])),
            'pokoje' => esc_html((string) $m['pokoje']),
            'metraz' => esc_html(Mieszkanie::formatMetrazu($m['metraz'])),
            'cena' => esc_html($m['cena_tekst']),
            'status' => '<span class="przystan-status przystan-status--' . esc_attr($m['status']) . '">' . esc_html($m['status_etykieta']) . '</span>',
            default => '',
        };
    }

    /** @param array<string, string> $kolumny */
    public static function sortowalne(array $kolumny): array
    {
        return $kolumny + ['pietro' => 'pietro', 'pokoje' => 'pokoje', 'metraz' => 'metraz', 'cena' => 'cena', 'status' => 'status'];
    }

    public static function sortowanieWPanelu(\WP_Query $zapytanie): void
    {
        if (! is_admin() || ! $zapytanie->is_main_query() || $zapytanie->get('post_type') !== self::TYP) {
            return;
        }
        $klucz = $zapytanie->get('orderby');
        if (in_array($klucz, ['pietro', 'pokoje', 'metraz', 'cena'], true)) {
            $zapytanie->set('meta_key', $klucz);
            $zapytanie->set('orderby', 'meta_value_num');
        } elseif ($klucz === 'status') {
            $zapytanie->set('meta_key', 'status');
            $zapytanie->set('orderby', 'meta_value');
        } elseif ($klucz === '') {
            $zapytanie->set('orderby', 'title');
            $zapytanie->set('order', 'ASC');
        }
    }
}
