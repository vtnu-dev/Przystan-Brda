<?php

namespace Przystan\Rest;

/**
 * Język odpowiedzi REST. Żądanie do /wp-json/ nie ma języka strony, więc ustawiamy go
 * z parametru „lang” (pl|en): teksty (__()) i formatowanie liczb są wtedy w języku użytkownika.
 */
final class Jezyk
{
    public const JEZYKI = ['pl', 'en'];

    public static function z(mixed $wartosc): string
    {
        return in_array($wartosc, self::JEZYKI, true) ? $wartosc : 'pl';
    }

    public static function przelacz(string $jezyk): void
    {
        $locale = 'pl_PL';
        if (function_exists('PLL') && PLL()->model && ($obiekt = PLL()->model->get_language($jezyk))) {
            $locale = $obiekt->locale;
            if (isset(PLL()->curlang) || property_exists(PLL(), 'curlang')) {
                PLL()->curlang = $obiekt;
            }
        }
        if (get_locale() !== $locale) {
            switch_to_locale($locale);
        }
    }
}
