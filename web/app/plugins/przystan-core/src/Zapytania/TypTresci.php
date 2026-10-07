<?php

namespace Przystan\Zapytania;

use Przystan\Webhook\Wysylka;

/**
 * Zapytania z formularza: niepubliczny typ treści widoczny tylko w panelu,
 * z kolumną stanu webhooka i akcją „Wyślij ponownie”.
 */
final class TypTresci
{
    public const TYP = 'zapytanie';
    private const AKCJA_PONOW = 'przystan_ponow_webhook';

    public static function rejestruj(): void
    {
        add_action('init', [self::class, 'typ']);
        add_filter('manage_' . self::TYP . '_posts_columns', [self::class, 'kolumny']);
        add_action('manage_' . self::TYP . '_posts_custom_column', [self::class, 'kolumna'], 10, 2);
        add_filter('post_row_actions', [self::class, 'akcjeWiersza'], 10, 2);
        add_action('admin_post_' . self::AKCJA_PONOW, [self::class, 'ponow']);
        add_action('add_meta_boxes_' . self::TYP, [self::class, 'skrzynka']);
        add_action('admin_notices', [self::class, 'komunikat']);
    }

    public static function typ(): void
    {
        register_post_type(self::TYP, [
            'labels' => [
                'name' => __('Zapytania', 'przystan'),
                'singular_name' => __('Zapytanie', 'przystan'),
                'edit_item' => __('Zapytanie', 'przystan'),
                'all_items' => __('Wszystkie zapytania', 'przystan'),
                'not_found' => __('Brak zapytań', 'przystan'),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => false,
            'menu_icon' => 'dashicons-email-alt',
            'menu_position' => 6,
            'supports' => ['title'],
            'capability_type' => 'post',
            'capabilities' => ['create_posts' => 'do_not_allow'], // powstają tylko z formularza
            'map_meta_cap' => true,
        ]);
    }

    /** @param array<string, string> $kolumny */
    public static function kolumny(array $kolumny): array
    {
        return [
            'cb' => $kolumny['cb'] ?? '',
            'title' => __('Zapytanie', 'przystan'),
            'email' => __('E-mail', 'przystan'),
            'telefon' => __('Telefon', 'przystan'),
            'webhook' => __('Webhook', 'przystan'),
            'date' => $kolumny['date'] ?? __('Data', 'przystan'),
        ];
    }

    public static function kolumna(string $kolumna, int $id): void
    {
        switch ($kolumna) {
            case 'email':
                $email = (string) get_post_meta($id, 'email', true);
                printf('<a href="mailto:%1$s">%2$s</a>', esc_attr($email), esc_html($email));
                break;
            case 'telefon':
                echo esc_html((string) get_post_meta($id, 'telefon', true));
                break;
            case 'webhook':
                $stan = (string) get_post_meta($id, Wysylka::META_STAN, true);
                $proby = (int) get_post_meta($id, Wysylka::META_PROBY, true);
                $kod = (int) get_post_meta($id, Wysylka::META_KOD, true);
                echo esc_html(Wysylka::etykieta($stan));
                if ($proby > 0) {
                    /* translators: 1: liczba prób, 2: kod HTTP */
                    printf('<br><small>%s</small>', esc_html(sprintf(__('prób: %1$d, ostatni kod: %2$s', 'przystan'), $proby, $kod ?: '-')));
                }
                break;
        }
    }

    /** @param array<string, string> $akcje */
    public static function akcjeWiersza(array $akcje, \WP_Post $post): array
    {
        if ($post->post_type !== self::TYP) {
            return $akcje;
        }
        unset($akcje['inline hide-if-no-js']);

        if (current_user_can('edit_post', $post->ID)) {
            $url = wp_nonce_url(
                admin_url('admin-post.php?action=' . self::AKCJA_PONOW . '&id=' . $post->ID),
                self::AKCJA_PONOW . '_' . $post->ID,
            );
            $akcje['ponow'] = sprintf('<a href="%s">%s</a>', esc_url($url), esc_html__('Wyślij ponownie', 'przystan'));
        }

        return $akcje;
    }

    public static function ponow(): void
    {
        $id = isset($_GET['id']) ? absint($_GET['id']) : 0;
        check_admin_referer(self::AKCJA_PONOW . '_' . $id);
        if (get_post_type($id) !== self::TYP || ! current_user_can('edit_post', $id)) {
            wp_die(esc_html__('Brak uprawnień.', 'przystan'), 403);
        }

        Wysylka::ponowRecznie($id);

        wp_safe_redirect(admin_url('edit.php?post_type=' . self::TYP . '&ponowiono=1'));
        exit;
    }

    public static function komunikat(): void
    {
        $ekran = get_current_screen();
        if ($ekran && $ekran->id === 'edit-' . self::TYP && isset($_GET['ponowiono'])) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Webhook zaplanowany do ponownej wysyłki.', 'przystan') . '</p></div>';
        }
    }

    public static function skrzynka(): void
    {
        add_meta_box('przystan_zapytanie', __('Treść zapytania', 'przystan'), [self::class, 'widokSkrzynki'], self::TYP, 'normal', 'high');
    }

    public static function widokSkrzynki(\WP_Post $post): void
    {
        $meta = static fn(string $k) => (string) get_post_meta($post->ID, $k, true);
        $mieszkanie = (int) $meta('mieszkanie');
        $wiersze = [
            __('Imię', 'przystan') => esc_html($meta('imie')),
            __('E-mail', 'przystan') => sprintf('<a href="mailto:%1$s">%2$s</a>', esc_attr($meta('email')), esc_html($meta('email'))),
            __('Telefon', 'przystan') => esc_html($meta('telefon') ?: '-'),
            __('Rodzaj', 'przystan') => esc_html($meta('rodzaj') === 'powiadomienie' ? __('powiadomienie o starcie sprzedaży', 'przystan') : __('zapytanie', 'przystan')),
            __('Inwestycja', 'przystan') => $meta('inwestycja') ? esc_html(get_the_title((int) $meta('inwestycja'))) : '-',
            __('Mieszkanie', 'przystan') => $mieszkanie ? sprintf('<a href="%1$s">%2$s</a>', esc_url((string) get_edit_post_link($mieszkanie)), esc_html(get_the_title($mieszkanie))) : '-',
            __('Język', 'przystan') => esc_html(strtoupper($meta('jezyk'))),
            __('Zgoda RODO', 'przystan') => esc_html($meta('zgoda_czas')),
            __('Wiadomość', 'przystan') => nl2br(esc_html($post->post_content ?: '-')),
            __('Webhook', 'przystan') => esc_html(Wysylka::etykieta($meta(Wysylka::META_STAN))),
        ];

        echo '<table class="widefat striped"><tbody>';
        foreach ($wiersze as $etykieta => $wartosc) {
            // $wartosc jest już escapowana wyżej.
            printf('<tr><th style="width:160px">%1$s</th><td>%2$s</td></tr>', esc_html($etykieta), $wartosc); // phpcs:ignore WordPress.Security.EscapeOutput
        }
        echo '</tbody></table>';
    }
}
