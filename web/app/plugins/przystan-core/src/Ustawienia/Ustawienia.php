<?php

namespace Przystan\Ustawienia;

/**
 * Odczyt ustawień inwestycji (jedna opcja w bazie, tablica).
 */
final class Ustawienia
{
    public const OPCJA = 'przystan_ustawienia';

    public const DOMYSLNE = [
        'telefon' => '',
        'email' => '',
        'adres' => '',
        'termin' => '',
        'webhook_url' => '',
        'webhook_klucz' => '',
    ];

    /** @return array<string, string> */
    public static function wszystkie(): array
    {
        $zapisane = get_option(self::OPCJA, []);

        return array_merge(self::DOMYSLNE, is_array($zapisane) ? array_map('strval', $zapisane) : []);
    }

    public static function pobierz(string $klucz): string
    {
        return self::wszystkie()[$klucz] ?? '';
    }

    public static function zapewnijKlucz(): void
    {
        $u = self::wszystkie();
        if ($u['webhook_klucz'] === '') {
            $u['webhook_klucz'] = self::nowyKlucz();
            update_option(self::OPCJA, $u, false);
        }
    }

    public static function nowyKlucz(): string
    {
        return bin2hex(random_bytes(32));
    }

    /** Klucz do podpisywania znacznika czasu w formularzu (z soli WordPressa, niezależny od webhooka). */
    public static function kluczAntyspamu(): string
    {
        return hash_hmac('sha256', 'przystan-antyspam', wp_salt('nonce'));
    }
}
