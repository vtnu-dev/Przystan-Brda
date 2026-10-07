<?php

namespace Przystan\Mieszkania;

/**
 * Dane jednego mieszkania jako zwykła tablica - ta sama dla szablonów, elewacji i odpowiedzi REST.
 * Czytamy meta bezpośrednio (ACF zapisuje pola pod ich nazwami), bez narzutu get_field().
 */
final class Mieszkanie
{
    /** @return array<string, mixed> */
    public static function zId(int $postId): array
    {
        $post = get_post($postId);

        return $post instanceof \WP_Post ? self::zPosta($post) : [];
    }

    /** @return array<string, mixed> */
    public static function zPosta(\WP_Post $post): array
    {
        $meta = static fn (string $klucz) => get_post_meta($post->ID, $klucz, true);

        $status = (string) $meta('status');
        if (! in_array($status, Filtry::STATUSY, true)) {
            $status = 'wolne';
        }
        $cena = (int) $meta('cena');
        $rzut = (int) $meta('rzut');

        $m = [
            'id' => $post->ID,
            'numer' => (string) ($meta('numer') ?: $post->post_title),
            'pietro' => (int) $meta('pietro'),
            'pozycja' => (int) $meta('pozycja'),
            'pokoje' => (int) $meta('pokoje'),
            'metraz' => (float) $meta('metraz'),
            'balkon_m2' => (float) $meta('balkon_m2'),
            'ogrodek' => (bool) $meta('ogrodek'),
            'widok_na_rzeke' => (bool) $meta('widok_na_rzeke'),
            'cena' => $status === 'sprzedane' ? 0 : $cena,
            'cena_tekst' => self::tekstCeny($cena, $status),
            'status' => $status,
            'status_etykieta' => self::etykietaStatusu($status),
            'url' => (string) get_permalink($post),
            'rzut_id' => $rzut,
            'rzut' => $rzut ? (string) wp_get_attachment_url($rzut) : '',
        ];

        // Gotowe teksty w języku strony, żeby JavaScript nie musiał niczego formatować ani tłumaczyć.
        $m['pietro_tekst'] = self::nazwaPietra($m['pietro']);
        $m['metraz_tekst'] = self::formatMetrazu($m['metraz']);
        $m['balkon_tekst'] = $m['balkon_m2'] > 0 ? self::formatMetrazu($m['balkon_m2']) : '-';
        /* translators: %d: liczba pokoi */
        $m['pokoje_tekst'] = sprintf(_n('%d pokój', '%d pokoje', $m['pokoje'], 'przystan'), $m['pokoje']);
        $m['opis'] = self::opisDostepny($m);

        return $m;
    }

    public static function etykietaStatusu(string $status): string
    {
        return match ($status) {
            'rezerwacja' => __('rezerwacja', 'przystan'),
            'sprzedane' => __('sprzedane', 'przystan'),
            default => __('wolne', 'przystan'),
        };
    }

    public static function tekstCeny(int $cena, string $status): string
    {
        if ($status === 'sprzedane') {
            return '';
        }
        if ($cena <= 0) {
            return __('cena na zapytanie', 'przystan');
        }

        /* translators: %s: cena w złotych, np. 612 000 */
        return sprintf(__('%s zł', 'przystan'), self::liczba($cena));
    }

    public static function formatMetrazu(float $metraz): string
    {
        /* translators: %s: powierzchnia, np. 62,4 */
        return sprintf(__('%s m²', 'przystan'), self::liczba($metraz, 1));
    }

    /**
     * Liczba w formacie języka strony: PL „670 000” i „62,7”, EN „670,000” i „62.7”.
     * Nie polegamy na number_format_i18n(), bo zależy od kompletności pakietu językowego WordPressa.
     */
    public static function liczba(float|int $liczba, int $miejsca = 0): string
    {
        $pl = \Przystan\Polylang\Integracja::jezyk() === 'pl';

        return number_format((float) $liczba, $miejsca, $pl ? ',' : '.', $pl ? "\u{00A0}" : ',');
    }

    public static function nazwaPietra(int $pietro): string
    {
        /* translators: %d: numer piętra */
        return $pietro === 0 ? __('parter', 'przystan') : sprintf(__('piętro %d', 'przystan'), $pietro);
    }

    /**
     * @param  array<string, mixed>  $m
     */
    public static function opisDostepny(array $m): string
    {
        /* translators: 1: numer, 2: liczba pokoi, 3: metraż, 4: piętro, 5: status */
        return sprintf(
            __('Mieszkanie %1$s, %2$s, %3$s, %4$s, %5$s', 'przystan'),
            $m['numer'],
            sprintf(_n('%d pokój', '%d pokoje', $m['pokoje'], 'przystan'), $m['pokoje']),
            self::formatMetrazu($m['metraz']),
            self::nazwaPietra($m['pietro']),
            $m['status_etykieta']
        );
    }
}
