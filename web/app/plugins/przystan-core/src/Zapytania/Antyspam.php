<?php

namespace Przystan\Zapytania;

/**
 * Ochrona formularza bez zewnętrznych skryptów (bez CAPTCHA):
 * pole-pułapka niewidoczne dla ludzi + podpisany czas wyświetlenia formularza.
 */
final class Antyspam
{
    public const POLE_PULAPKA = 'strona_www';
    public const POLE_CZAS = 'formularz_od';
    public const MIN_SEKUND = 3;
    public const MAX_SEKUND = 7200;

    public static function znacznik(int $teraz, string $klucz): string
    {
        return $teraz . '.' . hash_hmac('sha256', (string) $teraz, $klucz);
    }

    public static function czasOk(
        string $znacznik,
        int $teraz,
        string $klucz,
        int $min = self::MIN_SEKUND,
        int $max = self::MAX_SEKUND,
    ): bool {
        $czesci = explode('.', $znacznik, 2);
        if (count($czesci) !== 2 || ! ctype_digit($czesci[0])) {
            return false;
        }
        [$czas, $podpis] = $czesci;
        if (! hash_equals(hash_hmac('sha256', $czas, $klucz), $podpis)) {
            return false;
        }
        $minelo = $teraz - (int) $czas;

        return $minelo >= $min && $minelo <= $max;
    }

    /** @param array<string, mixed> $raw */
    public static function pulapka(array $raw): bool
    {
        $wartosc = $raw[self::POLE_PULAPKA] ?? '';

        return is_scalar($wartosc) && trim((string) $wartosc) !== '';
    }
}
