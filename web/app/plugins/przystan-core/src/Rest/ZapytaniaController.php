<?php

namespace Przystan\Rest;

use Przystan\Zapytania\Obsluga;

/**
 * POST /wp-json/przystan/v1/zapytania - wysyłka formularza bez przeładowania strony.
 * Walidacja i zapis są wspólne z wersją bez JavaScriptu (FormularzBezJs).
 */
final class ZapytaniaController
{
    public static function rejestruj(): void
    {
        add_action('rest_api_init', [self::class, 'trasy']);
    }

    public static function trasy(): void
    {
        register_rest_route(MieszkaniaController::PRZESTRZEN, '/zapytania', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [self::class, 'utworz'],
            // Formularz jest publiczny (jak każdy formularz kontaktowy); przed nadużyciem chronią
            // pułapka, podpisany czas wypełnienia i limit zapytań z jednego adresu IP.
            'permission_callback' => '__return_true',
        ]);
    }

    public static function utworz(\WP_REST_Request $zadanie): \WP_REST_Response
    {
        $jezyk = Jezyk::z($zadanie->get_param('lang'));
        Jezyk::przelacz($jezyk);
        $wynik = Obsluga::przyjmij($zadanie->get_params(), Obsluga::ipKlienta(), $jezyk);

        return new \WP_REST_Response([
            'ok' => $wynik['ok'],
            'komunikat' => $wynik['komunikat'],
            'bledy' => $wynik['bledy'],
        ], $wynik['kod']);
    }
}
