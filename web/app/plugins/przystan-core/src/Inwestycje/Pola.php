<?php

namespace Przystan\Inwestycje;

/**
 * Pola ACF inwestycji (w kodzie) oraz pole „inwestycja” we wpisach dziennika budowy.
 */
final class Pola
{
    public static function rejestruj(): void
    {
        add_action('acf/include_fields', [self::class, 'grupy']);
    }

    public static function grupy(): void
    {
        if (! function_exists('acf_add_local_field_group')) {
            return;
        }

        $pole = static fn(string $nazwa, string $etykieta, string $typ, array $dodatkowe = []) => array_merge([
            'key' => 'field_przystan_inw_' . $nazwa,
            'name' => $nazwa,
            'label' => $etykieta,
            'type' => $typ,
        ], $dodatkowe);

        $pola = [
            ['key' => 'field_przystan_inw_tab_dane', 'label' => __('Dane', 'przystan'), 'type' => 'tab'],
            $pole('status_inwestycji', __('Status', 'przystan'), 'select', ['required' => 1, 'wrapper' => ['width' => '34'], 'choices' => [
                'w_budowie' => __('w budowie', 'przystan'),
                'gotowe' => __('gotowe do odbioru', 'przystan'),
                'planowana' => __('w przygotowaniu', 'przystan'),
            ]]),
            $pole('lokalizacja', __('Lokalizacja', 'przystan'), 'text', ['wrapper' => ['width' => '33'], 'placeholder' => 'Bydgoszcz, Okole']),
            $pole('termin', __('Termin oddania / startu sprzedaży', 'przystan'), 'text', ['wrapper' => ['width' => '33']]),
            $pole('kondygnacje', __('Kondygnacje (z parterem)', 'przystan'), 'number', ['min' => 1, 'max' => 12, 'default_value' => 6, 'wrapper' => ['width' => '50'], 'instructions' => __('Do rysunku elewacji.', 'przystan')]),
            $pole('lokali_na_pietro', __('Lokali na piętro', 'przystan'), 'number', ['min' => 1, 'max' => 10, 'default_value' => 6, 'wrapper' => ['width' => '50']]),
            $pole('nad_woda', __('Budynek nad wodą', 'przystan'), 'true_false', ['ui' => 1, 'wrapper' => ['width' => '50'], 'instructions' => __('Linie wody pod elewacją.', 'przystan')]),
            $pole('podpis_elewacji', __('Podpis pod elewacją', 'przystan'), 'text', ['wrapper' => ['width' => '50'], 'placeholder' => 'Brda']),
            ['key' => 'field_przystan_inw_tab_etapy', 'label' => __('Harmonogram', 'przystan'), 'type' => 'tab'],
        ];
        for ($i = 1; $i <= Inwestycja::ETAPY; $i++) {
            $pola[] = $pole("etap_{$i}_nazwa", sprintf(__('Etap %d: nazwa', 'przystan'), $i), 'text', ['wrapper' => ['width' => '50']]);
            $pola[] = $pole("etap_{$i}_data", sprintf(__('Etap %d: kiedy', 'przystan'), $i), 'text', ['wrapper' => ['width' => '25'], 'placeholder' => 'IV kw. 2026']);
            $pola[] = $pole("etap_{$i}_stan", sprintf(__('Etap %d: stan', 'przystan'), $i), 'select', ['wrapper' => ['width' => '25'], 'choices' => [
                'zrobione' => __('zrobione', 'przystan'),
                'w_toku' => __('w toku', 'przystan'),
                'planowane' => __('planowane', 'przystan'),
            ]]);
        }

        acf_add_local_field_group([
            'key' => 'group_przystan_inwestycja',
            'title' => __('Inwestycja', 'przystan'),
            'position' => 'acf_after_title',
            'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => TypTresci::TYP]]],
            'fields' => $pola,
        ]);

        acf_add_local_field_group([
            'key' => 'group_przystan_wpis_inwestycja',
            'title' => __('Dziennik budowy', 'przystan'),
            'position' => 'side',
            'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'post']]],
            'fields' => [
                $pole('inwestycja', __('Której inwestycji dotyczy wpis', 'przystan'), 'post_object', ['post_type' => [TypTresci::TYP], 'return_format' => 'id', 'allow_null' => 1, 'ui' => 1]),
            ],
        ]);
    }
}
