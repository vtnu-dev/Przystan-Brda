<?php

namespace Przystan\Rest;

use Przystan\Mieszkania\Filtry;
use Przystan\Mieszkania\Wyszukiwarka;

/**
 * GET /wp-json/przystan/v1/mieszkania - wyszukiwarka dla elewacji i listy (bez przeładowania strony).
 */
final class MieszkaniaController
{
    public const PRZESTRZEN = 'przystan/v1';

    public static function rejestruj(): void
    {
        add_action('rest_api_init', [self::class, 'trasy']);
    }

    public static function trasy(): void
    {
        register_rest_route(self::PRZESTRZEN, '/mieszkania', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [self::class, 'lista'],
            'permission_callback' => '__return_true', // dane publiczne, takie same jak na stronie
            'args' => [
                'pokoje' => [
                    'type' => 'array',
                    'items' => ['type' => 'integer', 'minimum' => Filtry::POKOJE_MIN, 'maximum' => Filtry::POKOJE_MAX],
                    'maxItems' => 4,
                ],
                'pietro' => ['type' => 'integer', 'minimum' => Filtry::PIETRO_MIN, 'maximum' => Filtry::PIETRO_MAX],
                'metraz_min' => ['type' => 'number', 'minimum' => Filtry::METRAZ_MIN, 'maximum' => Filtry::METRAZ_MAX],
                'metraz_max' => ['type' => 'number', 'minimum' => Filtry::METRAZ_MIN, 'maximum' => Filtry::METRAZ_MAX],
                'status' => ['type' => 'string', 'enum' => [...Filtry::STATUSY, 'wszystkie'], 'default' => 'wszystkie'],
                'widok' => ['type' => 'boolean'],
                'balkon' => ['type' => 'boolean'],
                'lang' => ['type' => 'string', 'enum' => ['pl', 'en'], 'default' => 'pl'],
            ],
        ]);
    }

    public static function lista(\WP_REST_Request $zadanie): \WP_REST_Response
    {
        $filtry = Filtry::z($zadanie->get_params());
        $jezyk = Jezyk::z($zadanie->get_param('lang'));
        Jezyk::przelacz($jezyk);

        $mieszkania = Wyszukiwarka::szukaj($filtry, $jezyk);

        $odpowiedz = new \WP_REST_Response([
            'liczba' => count($mieszkania),
            'filtry' => Filtry::doUrl($filtry),
            'mieszkania' => $mieszkania,
        ]);
        $odpowiedz->header('Cache-Control', 'public, max-age=60');

        return $odpowiedz;
    }
}
