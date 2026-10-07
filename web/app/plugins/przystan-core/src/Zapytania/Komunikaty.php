<?php

namespace Przystan\Zapytania;

/**
 * Tłumaczy kody błędów walidatora na komunikaty dla użytkownika.
 */
final class Komunikaty
{
    public static function dla(string $kod): string
    {
        return match ($kod) {
            'imie_wymagane' => __('Podaj imię.', 'przystan'),
            'imie_za_krotkie' => __('Imię jest za krótkie.', 'przystan'),
            'imie_za_dlugie' => __('Imię jest za długie.', 'przystan'),
            'email_wymagany' => __('Podaj adres e-mail.', 'przystan'),
            'email_niepoprawny' => __('Sprawdź adres e-mail, np. jan@przyklad.pl.', 'przystan'),
            'telefon_niepoprawny' => __('Numer telefonu może zawierać tylko cyfry, spacje, +, - i nawiasy.', 'przystan'),
            'wiadomosc_za_dluga' => __('Wiadomość może mieć najwyżej 2000 znaków.', 'przystan'),
            'zgoda_wymagana' => __('Potrzebujemy zgody, żeby odpowiedzieć na zapytanie.', 'przystan'),
            default => __('Sprawdź to pole.', 'przystan'),
        };
    }

    /**
     * @param  array<string, string>  $bledy
     * @return array<string, string>
     */
    public static function wszystkie(array $bledy): array
    {
        return array_map([self::class, 'dla'], $bledy);
    }
}
