<?php

namespace Przystan\Mieszkania;

/**
 * Pola ACF mieszkania, rejestrowane w kodzie (wersjonowane w git, bez klikania w panelu).
 */
final class Pola
{
    public static function rejestruj(): void
    {
        add_action('acf/include_fields', [self::class, 'grupa']);
    }

    public static function grupa(): void
    {
        if (! function_exists('acf_add_local_field_group')) {
            return;
        }

        $pole = static fn (string $nazwa, string $etykieta, string $typ, array $dodatkowe = []) => array_merge([
            'key' => 'field_przystan_m_' . $nazwa,
            'name' => $nazwa,
            'label' => $etykieta,
            'type' => $typ,
        ], $dodatkowe);

        acf_add_local_field_group([
            'key' => 'group_przystan_mieszkanie',
            'title' => __('Dane mieszkania', 'przystan'),
            'position' => 'acf_after_title',
            'style' => 'default',
            'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => TypTresci::TYP]]],
            'fields' => [
                $pole('numer', __('Numer lokalu', 'przystan'), 'text', ['required' => 1, 'wrapper' => ['width' => '25'], 'placeholder' => 'M-31']),
                $pole('pietro', __('Piętro', 'przystan'), 'number', ['required' => 1, 'min' => Filtry::PIETRO_MIN, 'max' => Filtry::PIETRO_MAX, 'wrapper' => ['width' => '25'], 'instructions' => __('0 = parter', 'przystan')]),
                $pole('pozycja', __('Pozycja na elewacji', 'przystan'), 'number', ['required' => 1, 'min' => 1, 'max' => 6, 'wrapper' => ['width' => '25'], 'instructions' => __('1-6, od lewej', 'przystan')]),
                $pole('status', __('Status', 'przystan'), 'select', ['required' => 1, 'wrapper' => ['width' => '25'], 'choices' => [
                    'wolne' => __('wolne', 'przystan'),
                    'rezerwacja' => __('rezerwacja', 'przystan'),
                    'sprzedane' => __('sprzedane', 'przystan'),
                ], 'default_value' => 'wolne']),
                $pole('pokoje', __('Pokoje', 'przystan'), 'number', ['required' => 1, 'min' => Filtry::POKOJE_MIN, 'max' => Filtry::POKOJE_MAX, 'wrapper' => ['width' => '25']]),
                $pole('metraz', __('Metraż (m²)', 'przystan'), 'number', ['required' => 1, 'min' => Filtry::METRAZ_MIN, 'max' => Filtry::METRAZ_MAX, 'step' => '0.1', 'wrapper' => ['width' => '25']]),
                $pole('cena', __('Cena (zł)', 'przystan'), 'number', ['min' => 0, 'step' => 1000, 'wrapper' => ['width' => '25'], 'instructions' => __('Puste = „cena na zapytanie”', 'przystan')]),
                $pole('balkon_m2', __('Balkon / taras (m²)', 'przystan'), 'number', ['min' => 0, 'step' => '0.1', 'wrapper' => ['width' => '25']]),
                $pole('ogrodek', __('Ogródek', 'przystan'), 'true_false', ['ui' => 1, 'wrapper' => ['width' => '25']]),
                $pole('widok_na_rzeke', __('Widok na rzekę', 'przystan'), 'true_false', ['ui' => 1, 'wrapper' => ['width' => '25']]),
                $pole('rzut', __('Rzut mieszkania', 'przystan'), 'image', ['return_format' => 'id', 'preview_size' => 'medium', 'wrapper' => ['width' => '50']]),
            ],
        ]);
    }
}
