<?php

namespace Przystan\Strony;

use Przystan\Mieszkania\TypTresci as Mieszkania;

/**
 * Pola ACF stron. Darmowy ACF nie ma repeatera, więc powtarzalne elementy mają stałą liczbę miejsc
 * (4 liczby, 3 atuty, 6 punktów w okolicy) - prostsze dla redaktora i wystarczające dla jednej inwestycji.
 */
final class Pola
{
    public const SZABLON_OKOLICA = 'template-okolica.blade.php';
    public const SZABLON_KONTAKT = 'template-kontakt.blade.php';
    public const SZABLON_MIESZKANIA = 'template-mieszkania.blade.php';

    public static function rejestruj(): void
    {
        add_action('acf/include_fields', [self::class, 'grupy']);
    }

    public static function grupy(): void
    {
        if (! function_exists('acf_add_local_field_group')) {
            return;
        }

        self::seo();
        self::glowna();
        self::okolica();
        self::kontakt();
    }

    /** @param array<string, mixed> $dodatkowe */
    private static function pole(string $grupa, string $nazwa, string $etykieta, string $typ, array $dodatkowe = []): array
    {
        return array_merge([
            'key' => "field_przystan_{$grupa}_{$nazwa}",
            'name' => $nazwa,
            'label' => $etykieta,
            'type' => $typ,
        ], $dodatkowe);
    }

    private static function zakladka(string $grupa, string $nazwa, string $etykieta): array
    {
        return ['key' => "field_przystan_{$grupa}_tab_{$nazwa}", 'label' => $etykieta, 'type' => 'tab', 'placement' => 'top'];
    }

    private static function seo(): void
    {
        $lokalizacje = [];
        foreach (['page', 'post', Mieszkania::TYP] as $typ) {
            $lokalizacje[] = [['param' => 'post_type', 'operator' => '==', 'value' => $typ]];
        }

        acf_add_local_field_group([
            'key' => 'group_przystan_seo',
            'title' => __('SEO', 'przystan'),
            'position' => 'side',
            'menu_order' => 50,
            'location' => $lokalizacje,
            'fields' => [
                self::pole('seo', 'opis_seo', __('Opis w wynikach wyszukiwania', 'przystan'), 'textarea', [
                    'rows' => 4,
                    'maxlength' => 160,
                    'instructions' => __('Do 160 znaków. Puste = zajawka lub początek treści.', 'przystan'),
                ]),
            ],
        ]);
    }

    private static function glowna(): void
    {
        $g = 'glowna';
        $pola = [
            self::zakladka($g, 'hero', __('Hero', 'przystan')),
            self::pole($g, 'hero_nadtytul', __('Nadtytuł', 'przystan'), 'text'),
            self::pole($g, 'hero_naglowek', __('Nagłówek', 'przystan'), 'textarea', ['rows' => 2, 'instructions' => __('Nowa linia = łamanie wiersza.', 'przystan')]),
            self::pole($g, 'hero_wstep', __('Wstęp', 'przystan'), 'textarea', ['rows' => 3]),
            self::pole($g, 'hero_zdjecie', __('Zdjęcie', 'przystan'), 'image', ['return_format' => 'id', 'preview_size' => 'medium']),
            self::zakladka($g, 'liczby', __('Liczby', 'przystan')),
        ];
        for ($i = 1; $i <= 4; $i++) {
            $pola[] = self::pole($g, "liczba_{$i}_wartosc", sprintf(__('Liczba %d: wartość', 'przystan'), $i), 'text', ['wrapper' => ['width' => '30']]);
            $pola[] = self::pole($g, "liczba_{$i}_opis", sprintf(__('Liczba %d: opis', 'przystan'), $i), 'text', ['wrapper' => ['width' => '70']]);
        }
        $pola[] = self::zakladka($g, 'atuty', __('Atuty', 'przystan'));
        for ($i = 1; $i <= 3; $i++) {
            $pola[] = self::pole($g, "atut_{$i}_tytul", sprintf(__('Atut %d: tytuł', 'przystan'), $i), 'text', ['wrapper' => ['width' => '35']]);
            $pola[] = self::pole($g, "atut_{$i}_opis", sprintf(__('Atut %d: opis', 'przystan'), $i), 'textarea', ['rows' => 3, 'wrapper' => ['width' => '45']]);
            $pola[] = self::pole($g, "atut_{$i}_zdjecie", sprintf(__('Atut %d: zdjęcie', 'przystan'), $i), 'image', ['return_format' => 'id', 'preview_size' => 'thumbnail', 'wrapper' => ['width' => '20']]);
        }
        $pola[] = self::zakladka($g, 'okolica', __('Okolica', 'przystan'));
        $pola[] = self::pole($g, 'okolica_naglowek', __('Nagłówek', 'przystan'), 'text');
        $pola[] = self::pole($g, 'okolica_wstep', __('Wstęp', 'przystan'), 'textarea', ['rows' => 3]);
        $pola[] = self::pole($g, 'okolica_zdjecie', __('Zdjęcie', 'przystan'), 'image', ['return_format' => 'id', 'preview_size' => 'medium']);

        acf_add_local_field_group([
            'key' => 'group_przystan_glowna',
            'title' => __('Strona główna', 'przystan'),
            'location' => [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']]],
            'hide_on_screen' => ['the_content'],
            'fields' => $pola,
        ]);
    }

    private static function okolica(): void
    {
        $g = 'okolica';
        $pola = [
            self::pole($g, 'wstep', __('Wstęp', 'przystan'), 'textarea', ['rows' => 3]),
            self::zakladka($g, 'punkty', __('Punkty w okolicy', 'przystan')),
        ];
        for ($i = 1; $i <= 6; $i++) {
            $pola[] = self::pole($g, "punkt_{$i}_nazwa", sprintf(__('Punkt %d: nazwa', 'przystan'), $i), 'text', ['wrapper' => ['width' => '50']]);
            $pola[] = self::pole($g, "punkt_{$i}_minuty", sprintf(__('Punkt %d: minuty', 'przystan'), $i), 'number', ['min' => 1, 'max' => 60, 'wrapper' => ['width' => '25']]);
            $pola[] = self::pole($g, "punkt_{$i}_jak", sprintf(__('Punkt %d: jak', 'przystan'), $i), 'select', ['wrapper' => ['width' => '25'], 'choices' => [
                'pieszo' => __('pieszo', 'przystan'),
                'rowerem' => __('rowerem', 'przystan'),
                'tramwajem' => __('tramwajem', 'przystan'),
            ]]);
        }
        $pola[] = self::zakladka($g, 'standard', __('Standard', 'przystan'));
        $pola[] = self::pole($g, 'standard_naglowek', __('Nagłówek', 'przystan'), 'text');
        $pola[] = self::pole($g, 'standard_lista', __('Lista (jedna pozycja w wierszu)', 'przystan'), 'textarea', ['rows' => 8]);
        for ($i = 1; $i <= 3; $i++) {
            $pola[] = self::pole($g, "zdjecie_{$i}", sprintf(__('Zdjęcie %d', 'przystan'), $i), 'image', ['return_format' => 'id', 'preview_size' => 'thumbnail', 'wrapper' => ['width' => '33']]);
        }

        acf_add_local_field_group([
            'key' => 'group_przystan_okolica',
            'title' => __('Okolica i standard', 'przystan'),
            'location' => [[['param' => 'page_template', 'operator' => '==', 'value' => self::SZABLON_OKOLICA]]],
            'fields' => $pola,
        ]);
    }

    private static function kontakt(): void
    {
        $g = 'kontakt';
        acf_add_local_field_group([
            'key' => 'group_przystan_kontakt',
            'title' => __('Kontakt', 'przystan'),
            'location' => [[['param' => 'page_template', 'operator' => '==', 'value' => self::SZABLON_KONTAKT]]],
            'fields' => [
                self::pole($g, 'wstep', __('Wstęp', 'przystan'), 'textarea', ['rows' => 3]),
                self::pole($g, 'godziny', __('Godziny biura sprzedaży (jedna pozycja w wierszu)', 'przystan'), 'textarea', ['rows' => 4]),
            ],
        ]);
    }
}
