<?php

namespace Przystan\Mieszkania;

/**
 * Normalizuje parametry wyszukiwarki mieszkań (z $_GET albo z REST).
 * Nieznane klucze i złe wartości są pomijane, żeby link z błędem nadal coś pokazał.
 */
final class Filtry
{
    public const STATUSY = ['wolne', 'rezerwacja', 'sprzedane'];

    public const POKOJE_MIN = 1;
    public const POKOJE_MAX = 4;
    public const PIETRO_MIN = 0;
    public const PIETRO_MAX = 5;
    public const METRAZ_MIN = 20;
    public const METRAZ_MAX = 120;

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    public static function z(array $raw): array
    {
        $filtry = [];

        $pokoje = self::listaPokoi($raw['pokoje'] ?? null);
        if ($pokoje !== []) {
            $filtry['pokoje'] = $pokoje;
        }

        $pietro = self::liczba($raw['pietro'] ?? null, self::PIETRO_MIN, self::PIETRO_MAX);
        if ($pietro !== null) {
            $filtry['pietro'] = (int) $pietro;
        }

        $min = self::liczba($raw['metraz_min'] ?? null, self::METRAZ_MIN, self::METRAZ_MAX);
        $max = self::liczba($raw['metraz_max'] ?? null, self::METRAZ_MIN, self::METRAZ_MAX);
        if ($min !== null && $max !== null && $min > $max) {
            [$min, $max] = [$max, $min];
        }
        if ($min !== null) {
            $filtry['metraz_min'] = (float) $min;
        }
        if ($max !== null) {
            $filtry['metraz_max'] = (float) $max;
        }

        $status = is_string($raw['status'] ?? null) ? $raw['status'] : '';
        $filtry['status'] = in_array($status, self::STATUSY, true) ? $status : 'wszystkie';

        foreach (['widok', 'balkon'] as $flaga) {
            if (self::prawda($raw[$flaga] ?? null)) {
                $filtry[$flaga] = true;
            }
        }

        return $filtry;
    }

    /**
     * Parametry do adresu URL (bez wartości domyślnych), np. do linku „udostępnij wynik”.
     *
     * @param  array<string, mixed>  $filtry
     * @return array<string, string>
     */
    public static function doUrl(array $filtry): array
    {
        $url = [];
        foreach ($filtry as $klucz => $wartosc) {
            if ($klucz === 'status' && $wartosc === 'wszystkie') {
                continue;
            }
            $url[$klucz] = match (true) {
                is_array($wartosc) => implode(',', $wartosc),
                is_bool($wartosc) => $wartosc ? '1' : '0',
                is_float($wartosc) => rtrim(rtrim(number_format($wartosc, 2, '.', ''), '0'), '.'),
                default => (string) $wartosc,
            };
        }

        return $url;
    }

    /** @return list<int> */
    private static function listaPokoi(mixed $wartosc): array
    {
        if (is_string($wartosc) || is_int($wartosc)) {
            $wartosc = explode(',', (string) $wartosc);
        }
        if (! is_array($wartosc)) {
            return [];
        }

        $pokoje = [];
        foreach ($wartosc as $p) {
            $p = self::liczba($p, self::POKOJE_MIN, self::POKOJE_MAX);
            if ($p !== null && floor($p) === $p) {
                $pokoje[] = (int) $p;
            }
        }
        $pokoje = array_values(array_unique($pokoje));
        sort($pokoje);

        return $pokoje;
    }

    private static function liczba(mixed $wartosc, int $min, int $max): ?float
    {
        if (! is_scalar($wartosc) || ! is_numeric(str_replace(',', '.', (string) $wartosc))) {
            return null;
        }
        $liczba = (float) str_replace(',', '.', (string) $wartosc);

        return ($liczba >= $min && $liczba <= $max) ? $liczba : null;
    }

    private static function prawda(mixed $wartosc): bool
    {
        return in_array($wartosc, [true, 1, '1', 'on', 'true', 'tak'], true);
    }
}
