<?php

namespace Przystan\Zapytania;

use Przystan\Mieszkania\TypTresci as Mieszkania;
use Przystan\Polylang\Integracja;
use Przystan\Ustawienia\Ustawienia;
use Przystan\Webhook\Wysylka;

/**
 * Przyjęcie zapytania: antyspam → limit → walidacja → zapis → webhook w tle.
 * Wspólne dla REST i formularza bez JavaScriptu.
 */
final class Obsluga
{
    public const LIMIT = 5;
    public const OKNO_LIMITU = 10 * MINUTE_IN_SECONDS;

    /**
     * @param  array<string, mixed>  $raw
     * @return array{ok: bool, kod: int, komunikat: string, bledy: array<string, string>, dane: array<string, mixed>, id: int}
     */
    public static function przyjmij(array $raw, string $ip): array
    {
        $wynik = static fn(bool $ok, int $kod, string $komunikat, array $bledy = [], array $dane = [], int $id = 0) => compact('ok', 'kod', 'komunikat', 'bledy', 'dane', 'id');
        $sukces = __('Dziękujemy! Odpowiemy w ciągu jednego dnia roboczego.', 'przystan');

        // Bot wypełnił ukryte pole: udajemy sukces, nic nie zapisujemy.
        if (Antyspam::pulapka($raw)) {
            return $wynik(true, 200, $sukces);
        }

        $znacznik = is_string($raw[Antyspam::POLE_CZAS] ?? null) ? $raw[Antyspam::POLE_CZAS] : '';
        if (! Antyspam::czasOk($znacznik, time(), Ustawienia::kluczAntyspamu())) {
            return $wynik(false, 400, __('Formularz wygasł albo został wysłany zbyt szybko. Odśwież stronę i spróbuj ponownie.', 'przystan'));
        }

        $sprawdzenie = Walidator::sprawdz($raw);
        if ($sprawdzenie['bledy'] !== []) {
            return $wynik(false, 422, __('Popraw zaznaczone pola.', 'przystan'), Komunikaty::wszystkie($sprawdzenie['bledy']), $sprawdzenie['dane']);
        }

        if (! self::wLimicie($ip)) {
            return $wynik(false, 429, __('Wysłano już kilka zapytań. Spróbuj ponownie za kilka minut albo zadzwoń.', 'przystan'), [], $sprawdzenie['dane']);
        }

        $id = self::zapisz($sprawdzenie['dane']);
        if ($id === 0) {
            return $wynik(false, 500, __('Nie udało się zapisać zapytania. Zadzwoń do nas, proszę.', 'przystan'), [], $sprawdzenie['dane']);
        }

        Wysylka::zaplanuj($id);

        return $wynik(true, 201, $sukces, [], [], $id);
    }

    /** @param array<string, mixed> $dane */
    private static function zapisz(array $dane): int
    {
        $mieszkanie = $dane['mieszkanie'] > 0 && get_post_type($dane['mieszkanie']) === Mieszkania::TYP ? $dane['mieszkanie'] : 0;
        $tytul = $mieszkanie
            ? sprintf('%s - %s', get_post_meta($mieszkanie, 'numer', true) ?: get_the_title($mieszkanie), $dane['imie'])
            : sprintf('%s - %s', __('Ogólne', 'przystan'), $dane['imie']);

        $id = wp_insert_post([
            'post_type' => TypTresci::TYP,
            'post_status' => 'publish',
            'post_title' => $tytul,
            'post_content' => $dane['wiadomosc'],
            'meta_input' => [
                'imie' => $dane['imie'],
                'email' => $dane['email'],
                'telefon' => $dane['telefon'],
                'mieszkanie' => $mieszkanie,
                'jezyk' => Integracja::jezyk(),
                'zgoda_czas' => gmdate('c'),
                Wysylka::META_STAN => Wysylka::STAN_OCZEKUJE,
                Wysylka::META_PROBY => 0,
            ],
        ], true);

        return is_wp_error($id) ? 0 : (int) $id;
    }

    private static function wLimicie(string $ip): bool
    {
        $klucz = 'przystan_limit_' . md5($ip . wp_salt('nonce'));
        $licznik = (int) get_transient($klucz);
        if ($licznik >= self::LIMIT) {
            return false;
        }
        set_transient($klucz, $licznik + 1, self::OKNO_LIMITU);

        return true;
    }

    /**
     * Adres klienta. Serwer przyjmuje ruch tylko z Cloudflare (zapora), więc nagłówek
     * CF-Connecting-IP jest wiarygodny; lokalnie używamy REMOTE_ADDR.
     */
    public static function ipKlienta(): string
    {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

        return filter_var($ip, FILTER_VALIDATE_IP) ? (string) $ip : '0.0.0.0';
    }
}
