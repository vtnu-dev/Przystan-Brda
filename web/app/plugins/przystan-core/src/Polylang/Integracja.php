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
    public const POLA_WSPOLNE = ['inwestycja', 'numer', 'pietro', 'pozycja', 'pokoje', 'metraz', 'cena', 'status', 'balkon_m2', 'ogrodek', 'widok_na_rzeke'];

    public static function rejestruj(): void
    {
        add_filter('pll_get_post_types', [self::class, 'typy'], 10, 2);
        add_filter('pll_copy_post_metas', [self::class, 'metaDoKopii'], 10, 3);
        add_filter('pll_translate_post_meta', [self::class, 'tlumaczRelacje'], 10, 3);
        add_action('init', [self::class, 'ciagi'], 30);
    }

    /** @param array<string, string> $typy */
    public static function typy(array $typy, bool $ustawienia): array
    {
        $typy[TypTresci::TYP] = TypTresci::TYP;
        $typy[\Przystan\Inwestycje\TypTresci::TYP] = \Przystan\Inwestycje\TypTresci::TYP;

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
        // Strony i wpisy: teksty z pól ACF są inne w każdym języku, więc kopiujemy tylko obrazek i szablon.
        if (get_post_type($from) !== TypTresci::TYP) {
            return array_values(array_intersect($metas, ['_thumbnail_id', '_wp_page_template']));
        }

        $wspolne = [];
        foreach (self::POLA_WSPOLNE as $pole) {
            $wspolne[] = $pole;
            $wspolne[] = '_' . $pole;
        }

        return $wspolne;
    }

    /**
     * Przy synchronizacji mieszkania PL → EN pole „inwestycja” musi wskazywać angielską wersję inwestycji, nie polską.
     */
    public static function tlumaczRelacje(mixed $wartosc, string $klucz, string $jezyk): mixed
    {
        if ($klucz === 'inwestycja' && is_numeric($wartosc) && function_exists('pll_get_post')) {
            return pll_get_post((int) $wartosc, $jezyk) ?: $wartosc;
        }

        return $wartosc;
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
