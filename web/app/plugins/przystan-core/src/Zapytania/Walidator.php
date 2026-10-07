<?php

namespace Przystan\Zapytania;

/**
 * Walidacja formularza zapytania. Zwraca oczyszczone dane i kody błędów
 * (tłumaczone na komunikaty w warstwie WordPressa: Komunikaty::dla()).
 */
final class Walidator
{
    public const IMIE_MIN = 2;
    public const IMIE_MAX = 80;
    public const WIADOMOSC_MAX = 2000;

    /**
     * @param  array<string, mixed>  $raw
     * @return array{dane: array<string, mixed>, bledy: array<string, string>}
     */
    public static function sprawdz(array $raw): array
    {
        $dane = [
            'imie' => self::tekst($raw['imie'] ?? ''),
            'email' => strtolower(self::tekst($raw['email'] ?? '')),
            'telefon' => self::tekst($raw['telefon'] ?? ''),
            'wiadomosc' => self::tekstWielowierszowy($raw['wiadomosc'] ?? ''),
            'mieszkanie' => max(0, (int) ($raw['mieszkanie'] ?? 0)),
            'zgoda' => in_array($raw['zgoda'] ?? null, [true, 1, '1', 'on', 'tak'], true),
        ];

        $bledy = [];

        $dlugosc = mb_strlen($dane['imie']);
        if ($dlugosc === 0) {
            $bledy['imie'] = 'imie_wymagane';
        } elseif ($dlugosc < self::IMIE_MIN) {
            $bledy['imie'] = 'imie_za_krotkie';
        } elseif ($dlugosc > self::IMIE_MAX) {
            $bledy['imie'] = 'imie_za_dlugie';
        }

        if ($dane['email'] === '') {
            $bledy['email'] = 'email_wymagany';
        } elseif (filter_var($dane['email'], FILTER_VALIDATE_EMAIL) === false) {
            $bledy['email'] = 'email_niepoprawny';
        }

        if ($dane['telefon'] !== '' && ! preg_match('/^[0-9 +()\-]{7,20}$/', $dane['telefon'])) {
            $bledy['telefon'] = 'telefon_niepoprawny';
        }

        if (mb_strlen($dane['wiadomosc']) > self::WIADOMOSC_MAX) {
            $bledy['wiadomosc'] = 'wiadomosc_za_dluga';
        }

        if (! $dane['zgoda']) {
            $bledy['zgoda'] = 'zgoda_wymagana';
        }

        return ['dane' => $dane, 'bledy' => $bledy];
    }

    private static function tekst(mixed $wartosc): string
    {
        if (! is_scalar($wartosc)) {
            return '';
        }
        $tekst = strip_tags((string) $wartosc);
        $tekst = preg_replace('/[\r\n\t]+/', ' ', $tekst) ?? '';

        return trim(preg_replace('/\s{2,}/u', ' ', $tekst) ?? '');
    }

    private static function tekstWielowierszowy(mixed $wartosc): string
    {
        if (! is_scalar($wartosc)) {
            return '';
        }
        $tekst = str_replace("\r\n", "\n", strip_tags((string) $wartosc));

        return trim($tekst);
    }
}
