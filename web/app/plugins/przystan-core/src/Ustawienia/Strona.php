<?php

namespace Przystan\Ustawienia;

/**
 * Ustawienia → Przystań Brda (Settings API). Bez ACF Pro: zwykła strona opcji WordPressa.
 */
final class Strona
{
    private const SLUG = 'przystan-ustawienia';

    public static function rejestruj(): void
    {
        add_action('admin_menu', [self::class, 'menu']);
        add_action('admin_init', [self::class, 'pola']);
        add_action('admin_post_przystan_nowy_klucz', [self::class, 'nowyKlucz']);
    }

    public static function menu(): void
    {
        add_options_page(
            __('Przystań Brda', 'przystan'),
            __('Przystań Brda', 'przystan'),
            'manage_options',
            self::SLUG,
            [self::class, 'widok'],
        );
    }

    public static function pola(): void
    {
        register_setting(self::SLUG, Ustawienia::OPCJA, [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanityzuj'],
            'default' => Ustawienia::DOMYSLNE,
        ]);

        add_settings_section('kontakt', __('Biuro sprzedaży', 'przystan'), '__return_null', self::SLUG);
        self::pole('telefon', __('Telefon', 'przystan'), 'kontakt', 'tel');
        self::pole('email', __('E-mail', 'przystan'), 'kontakt', 'email');
        self::pole('adres', __('Adres', 'przystan'), 'kontakt', 'textarea');
        self::pole('termin', __('Termin oddania', 'przystan'), 'kontakt', 'text', __('Np. „IV kwartał 2027”. Tłumaczenie: Języki → Tłumaczenia ciągów.', 'przystan'));

        add_settings_section('webhook', __('Webhook zapytań', 'przystan'), [self::class, 'opisWebhooka'], self::SLUG);
        self::pole('webhook_url', __('Adres odbiorcy', 'przystan'), 'webhook', 'url', __('Puste = zapytania tylko zapisują się w panelu.', 'przystan'));
    }

    private static function pole(string $klucz, string $etykieta, string $sekcja, string $typ, string $opis = ''): void
    {
        add_settings_field($klucz, $etykieta, static function () use ($klucz, $typ, $opis): void {
            $wartosc = Ustawienia::pobierz($klucz);
            $nazwa = Ustawienia::OPCJA . '[' . $klucz . ']';
            if ($typ === 'textarea') {
                printf('<textarea class="large-text" rows="3" id="%1$s" name="%2$s">%3$s</textarea>', esc_attr($klucz), esc_attr($nazwa), esc_textarea($wartosc));
            } else {
                printf('<input class="regular-text" type="%1$s" id="%2$s" name="%3$s" value="%4$s">', esc_attr($typ), esc_attr($klucz), esc_attr($nazwa), esc_attr($wartosc));
            }
            if ($opis !== '') {
                printf('<p class="description">%s</p>', esc_html($opis));
            }
        }, self::SLUG, $sekcja, ['label_for' => $klucz]);
    }

    public static function opisWebhooka(): void
    {
        $klucz = Ustawienia::pobierz('webhook_klucz');
        echo '<p>' . esc_html__('Każde nowe zapytanie wysyłamy jako JSON (zdarzenie zapytanie.utworzone). Nagłówek X-Przystan-Signature zawiera HMAC-SHA256 z „X-Przystan-Timestamp.treść” liczony tym kluczem.', 'przystan') . '</p>';
        printf(
            '<p><code>%1$s</code> <a class="button" href="%2$s">%3$s</a></p>',
            esc_html($klucz !== '' ? substr($klucz, 0, 8) . '…' . substr($klucz, -4) : __('brak klucza', 'przystan')),
            esc_url(wp_nonce_url(admin_url('admin-post.php?action=przystan_nowy_klucz'), 'przystan_nowy_klucz')),
            esc_html__('Wygeneruj nowy klucz', 'przystan'),
        );
    }

    /**
     * @param  mixed  $wejscie
     * @return array<string, string>
     */
    public static function sanityzuj($wejscie): array
    {
        $wejscie = is_array($wejscie) ? $wejscie : [];
        $obecne = Ustawienia::wszystkie();

        $url = esc_url_raw(trim((string) ($wejscie['webhook_url'] ?? '')), ['https', 'http']);
        $dozwolonyHttp = wp_get_environment_type() === 'development';
        if ($url !== '' && ! str_starts_with($url, 'https://') && ! $dozwolonyHttp) {
            add_settings_error(Ustawienia::OPCJA, 'webhook_https', __('Adres webhooka musi zaczynać się od https://.', 'przystan'));
            $url = $obecne['webhook_url'];
        }

        return [
            'telefon' => sanitize_text_field((string) ($wejscie['telefon'] ?? '')),
            'email' => sanitize_email((string) ($wejscie['email'] ?? '')),
            'adres' => sanitize_textarea_field((string) ($wejscie['adres'] ?? '')),
            'termin' => sanitize_text_field((string) ($wejscie['termin'] ?? '')),
            'webhook_url' => $url,
            // Klucza nie ma w formularzu - zmienia go tylko przycisk „Wygeneruj nowy klucz”.
            'webhook_klucz' => $obecne['webhook_klucz'],
        ];
    }

    public static function nowyKlucz(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('Brak uprawnień.', 'przystan'), 403);
        }
        check_admin_referer('przystan_nowy_klucz');

        $u = Ustawienia::wszystkie();
        $u['webhook_klucz'] = Ustawienia::nowyKlucz();
        update_option(Ustawienia::OPCJA, $u, false);

        wp_safe_redirect(admin_url('options-general.php?page=' . self::SLUG . '&klucz=nowy'));
        exit;
    }

    public static function widok(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        echo '<div class="wrap"><h1>' . esc_html(get_admin_page_title()) . '</h1>';
        if (isset($_GET['klucz'])) {
            echo '<div class="notice notice-success"><p>' . esc_html__('Wygenerowano nowy klucz. Przekaż go odbiorcy webhooka.', 'przystan') . '</p></div>';
        }
        echo '<form method="post" action="options.php">';
        settings_fields(self::SLUG);
        do_settings_sections(self::SLUG);
        submit_button();
        echo '</form></div>';
    }
}
