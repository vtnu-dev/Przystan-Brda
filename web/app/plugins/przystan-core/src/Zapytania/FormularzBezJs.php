<?php

namespace Przystan\Zapytania;

/**
 * Wysyłka formularza bez JavaScriptu przez admin-post.php. Wynik (komunikat, błędy, wpisane dane)
 * wraca na stronę przez krótkotrwały transient wskazany tokenem w adresie.
 */
final class FormularzBezJs
{
    public const AKCJA = 'przystan_zapytanie';
    public const PARAM = 'zapytanie';

    public static function rejestruj(): void
    {
        add_action('admin_post_nopriv_' . self::AKCJA, [self::class, 'obsluz']);
        add_action('admin_post_' . self::AKCJA, [self::class, 'obsluz']);
    }

    public static function obsluz(): void
    {
        $raw = wp_unslash($_POST);
        $raw = is_array($raw) ? $raw : [];
        $jezyk = \Przystan\Rest\Jezyk::z($raw['lang'] ?? null);
        \Przystan\Rest\Jezyk::przelacz($jezyk);
        $wynik = Obsluga::przyjmij($raw, Obsluga::ipKlienta(), $jezyk);

        $token = bin2hex(random_bytes(8));
        set_transient('przystan_form_' . $token, [
            'ok' => $wynik['ok'],
            'komunikat' => $wynik['komunikat'],
            'bledy' => $wynik['bledy'],
            'dane' => $wynik['ok'] ? [] : $wynik['dane'],
        ], 5 * MINUTE_IN_SECONDS);

        $powrot = wp_get_referer() ?: home_url('/');
        $powrot = remove_query_arg(self::PARAM, $powrot);
        wp_safe_redirect(add_query_arg(self::PARAM, $token, $powrot) . '#formularz', 303);
        exit;
    }

    /**
     * Wynik poprzedniej wysyłki dla bieżącego żądania (albo null).
     *
     * @return array{ok: bool, komunikat: string, bledy: array<string, string>, dane: array<string, mixed>}|null
     */
    public static function wynik(): ?array
    {
        $token = isset($_GET[self::PARAM]) ? sanitize_key((string) $_GET[self::PARAM]) : '';
        if ($token === '') {
            return null;
        }
        $wynik = get_transient('przystan_form_' . $token);

        return is_array($wynik) ? $wynik : null;
    }
}
