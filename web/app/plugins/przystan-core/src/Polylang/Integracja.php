<?php

namespace Przystan\Polylang;

use Przystan\Mieszkania\TypTresci;
use Przystan\Ustawienia\Ustawienia;

/**
 * Wielojęzyczność przez darmowy Polylang: mieszkania są tłumaczone, a liczby i status
 * synchronizowane między PL i EN (zmiana statusu w jednym języku zmienia go w obu).
 */
final class Integracja
{
    /** Pola wspólne dla wszystkich wersji językowych mieszkania. */
    public const POLA_WSPOLNE = ['numer', 'pietro', 'pozycja', 'pokoje', 'metraz', 'cena', 'status', 'balkon_m2', 'ogrodek', 'widok_na_rzeke', 'rzut'];

    public static function rejestruj(): void
    {
        add_filter('pll_get_post_types', [self::class, 'typy'], 10, 2);
        add_filter('pll_copy_post_metas', [self::class, 'metaDoKopii'], 10, 3);
        add_action('init', [self::class, 'ciagi'], 30);
    }

    /** @param array<string, string> $typy */
    public static function typy(array $typy, bool $ustawienia): array
    {
        $typy[TypTresci::TYP] = TypTresci::TYP;

        return $typy;
    }

    /**
     * Polylang kopiuje/synchronizuje te meta przy tworzeniu tłumaczenia i każdym zapisie.
     * ACF trzyma obok wartości klucz pola (_numer), więc synchronizujemy obie wersje.
     *
     * @param  list<string>  $metas
     * @return list<string>
     */
    public static function metaDoKopii(array $metas, bool $sync, int $from): array
    {
        if (get_post_type($from) !== TypTresci::TYP) {
            return $metas;
        }
        foreach (self::POLA_WSPOLNE as $pole) {
            $metas[] = $pole;
            $metas[] = '_' . $pole;
        }

        return array_values(array_unique($metas));
    }

    /** Teksty z ustawień do przetłumaczenia w Języki → Tłumaczenia ciągów. */
    public static function ciagi(): void
    {
        if (! function_exists('pll_register_string')) {
            return;
        }
        $u = Ustawienia::wszystkie();
        pll_register_string('przystan_termin', $u['termin'], 'Przystań Brda');
        pll_register_string('przystan_adres', $u['adres'], 'Przystań Brda', true);
    }

    public static function tlumacz(string $tekst): string
    {
        return function_exists('pll__') ? pll__($tekst) : $tekst;
    }

    public static function jezyk(): string
    {
        $jezyk = function_exists('pll_current_language') ? pll_current_language('slug') : '';

        return is_string($jezyk) && $jezyk !== '' ? $jezyk : 'pl';
    }
}
