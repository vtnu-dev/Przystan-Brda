<?php

namespace Przystan\Inwestycje;

/**
 * Dane inwestycji jako tablica dla szablonów i REST: status, położenie, budynek, harmonogram, liczba mieszkań.
 */
final class Inwestycja
{
    public const ETAPY = 6;

    /** @return array<string, mixed> */
    public static function zId(int $id): array
    {
        $post = $id ? get_post($id) : null;

        return $post instanceof \WP_Post && $post->post_type === TypTresci::TYP ? self::zPosta($post) : [];
    }

    /** @return array<string, mixed> */
    public static function zPosta(\WP_Post $post): array
    {
        $meta = static fn(string $k) => get_post_meta($post->ID, $k, true);
        $status = in_array($meta('status_inwestycji'), TypTresci::STATUSY, true) ? (string) $meta('status_inwestycji') : 'w_budowie';

        $etapy = [];
        for ($i = 1; $i <= self::ETAPY; $i++) {
            $nazwa = (string) $meta("etap_{$i}_nazwa");
            if ($nazwa !== '') {
                $stan = (string) $meta("etap_{$i}_stan");
                $etapy[] = [
                    'nazwa' => $nazwa,
                    'data' => (string) $meta("etap_{$i}_data"),
                    'stan' => in_array($stan, ['zrobione', 'w_toku', 'planowane'], true) ? $stan : 'planowane',
                ];
            }
        }

        [$liczba, $wolnych] = self::liczbyMieszkan($post->ID);

        return [
            'id' => $post->ID,
            'nazwa' => get_the_title($post),
            'url' => (string) get_permalink($post),
            'status' => $status,
            'status_etykieta' => self::etykietaStatusu($status),
            'lokalizacja' => (string) $meta('lokalizacja'),
            'termin' => (string) $meta('termin'),
            'kondygnacje' => (int) $meta('kondygnacje') ?: 6,
            'lokali_na_pietro' => (int) $meta('lokali_na_pietro') ?: 6,
            'nad_woda' => (bool) $meta('nad_woda'),
            'podpis_elewacji' => (string) $meta('podpis_elewacji'),
            'zdjecie' => (int) get_post_thumbnail_id($post),
            'zajawka' => get_the_excerpt($post),
            'etapy' => $etapy,
            'mieszkan' => $liczba,
            'wolnych' => $wolnych,
        ];
    }

    public static function etykietaStatusu(string $status): string
    {
        return match ($status) {
            'gotowe' => __('gotowe do odbioru', 'przystan'),
            'planowana' => __('w przygotowaniu', 'przystan'),
            default => __('w budowie', 'przystan'),
        };
    }

    /** @return array{0: int, 1: int} liczba mieszkań i wolnych */
    private static function liczbyMieszkan(int $inwestycjaId): array
    {
        $ids = get_posts([
            'post_type' => 'mieszkanie',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
            'lang' => '',
            'meta_key' => 'inwestycja',
            'meta_value' => $inwestycjaId,
        ]);
        $wolnych = 0;
        foreach ($ids as $id) {
            if (get_post_meta((int) $id, 'status', true) === 'wolne') {
                $wolnych++;
            }
        }

        return [count($ids), $wolnych];
    }
}
