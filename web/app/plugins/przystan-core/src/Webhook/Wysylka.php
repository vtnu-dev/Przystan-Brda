<?php

namespace Przystan\Webhook;

use Przystan\Mieszkania\Mieszkanie;
use Przystan\Ustawienia\Ustawienia;
use Przystan\Zapytania\TypTresci;

/**
 * Wysyłka zapytania do zewnętrznego systemu (CRM, n8n, Make...) w tle przez WP-Cron.
 * Formularz nie czeka na odbiorcę; nieudane próby są ponawiane po 1, 5 i 30 minutach.
 */
final class Wysylka
{
    public const HOOK = 'przystan_webhook';
    public const ZDARZENIE = 'zapytanie.utworzone';
    public const ZDARZENIE_POWIADOMIENIE = 'powiadomienie.zapis';

    public const META_STAN = '_webhook_stan';
    public const META_PROBY = '_webhook_proby';
    public const META_KOD = '_webhook_kod';
    public const META_CZAS = '_webhook_czas';

    public const STAN_OCZEKUJE = 'oczekuje';
    public const STAN_WYSLANO = 'wyslano';
    public const STAN_PONOWIENIE = 'ponowienie';
    public const STAN_BLAD = 'blad';
    public const STAN_BEZ_ADRESU = 'bez_adresu';

    /** Opóźnienia kolejnych ponowień w sekundach. */
    public const PONOWIENIA = [60, 300, 1800];

    public static function rejestruj(): void
    {
        add_action(self::HOOK, [self::class, 'wyslij']);
    }

    public static function zaplanuj(int $zapytanieId, int $opoznienie = 0): void
    {
        wp_schedule_single_event(time() + $opoznienie, self::HOOK, [$zapytanieId]);
    }

    /** @return array<string, mixed> */
    public static function tresc(int $zapytanieId): array
    {
        $post = get_post($zapytanieId);
        $meta = static fn(string $k) => (string) get_post_meta($zapytanieId, $k, true);
        $mieszkanieId = (int) $meta('mieszkanie');
        $mieszkanie = $mieszkanieId ? Mieszkanie::zId($mieszkanieId) : null;
        $inwestycjaId = (int) $meta('inwestycja');

        return [
            'zdarzenie' => self::zdarzenie($zapytanieId),
            'inwestycja' => $inwestycjaId ? ['nazwa' => get_the_title($inwestycjaId), 'url' => (string) get_permalink($inwestycjaId)] : null,
            'id' => $zapytanieId,
            'utworzono' => $post ? get_post_time('c', true, $post) : gmdate('c'),
            'jezyk' => $meta('jezyk'),
            'klient' => [
                'imie' => $meta('imie'),
                'email' => $meta('email'),
                'telefon' => $meta('telefon'),
            ],
            'wiadomosc' => $post ? $post->post_content : '',
            'zgoda_rodo' => $meta('zgoda_czas'),
            'mieszkanie' => $mieszkanie ? [
                'numer' => $mieszkanie['numer'],
                'pietro' => $mieszkanie['pietro'],
                'pokoje' => $mieszkanie['pokoje'],
                'metraz' => $mieszkanie['metraz'],
                'cena' => $mieszkanie['cena'],
                'status' => $mieszkanie['status'],
                'url' => $mieszkanie['url'],
            ] : null,
            'panel_url' => admin_url('post.php?post=' . $zapytanieId . '&action=edit'),
        ];
    }

    public static function zdarzenie(int $zapytanieId): string
    {
        return get_post_meta($zapytanieId, 'rodzaj', true) === 'powiadomienie' ? self::ZDARZENIE_POWIADOMIENIE : self::ZDARZENIE;
    }

    public static function wyslij(int $zapytanieId): void
    {
        if (get_post_type($zapytanieId) !== TypTresci::TYP) {
            return;
        }

        $url = Ustawienia::pobierz('webhook_url');
        if ($url === '') {
            update_post_meta($zapytanieId, self::META_STAN, self::STAN_BEZ_ADRESU);

            return;
        }

        $tresc = (string) wp_json_encode(self::tresc($zapytanieId), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $czas = time();

        $odpowiedz = wp_remote_post($url, [
            'timeout' => 5,
            'redirection' => 0,
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'User-Agent' => 'PrzystanBrda/' . PRZYSTAN_CORE_VERSION . '; ' . home_url(),
                'X-Przystan-Event' => self::zdarzenie($zapytanieId),
                'X-Przystan-Delivery' => $zapytanieId . '-' . $czas,
                'X-Przystan-Timestamp' => (string) $czas,
                'X-Przystan-Signature' => Podpis::podpisz($tresc, $czas, Ustawienia::pobierz('webhook_klucz')),
            ],
            'body' => $tresc,
        ]);

        $kod = is_wp_error($odpowiedz) ? 0 : (int) wp_remote_retrieve_response_code($odpowiedz);
        $proby = (int) get_post_meta($zapytanieId, self::META_PROBY, true) + 1;

        update_post_meta($zapytanieId, self::META_PROBY, $proby);
        update_post_meta($zapytanieId, self::META_KOD, $kod);
        update_post_meta($zapytanieId, self::META_CZAS, gmdate('c', $czas));

        if ($kod >= 200 && $kod < 300) {
            update_post_meta($zapytanieId, self::META_STAN, self::STAN_WYSLANO);

            return;
        }

        $ponowienie = self::PONOWIENIA[$proby - 1] ?? null;
        if ($ponowienie === null) {
            update_post_meta($zapytanieId, self::META_STAN, self::STAN_BLAD);

            return;
        }

        update_post_meta($zapytanieId, self::META_STAN, self::STAN_PONOWIENIE);
        self::zaplanuj($zapytanieId, $ponowienie);
    }

    /** Ręczne ponowienie z panelu: zeruje licznik prób i wysyła od razu. */
    public static function ponowRecznie(int $zapytanieId): void
    {
        wp_clear_scheduled_hook(self::HOOK, [$zapytanieId]);
        update_post_meta($zapytanieId, self::META_PROBY, 0);
        update_post_meta($zapytanieId, self::META_STAN, self::STAN_OCZEKUJE);
        self::zaplanuj($zapytanieId);
    }

    public static function etykieta(string $stan): string
    {
        return match ($stan) {
            self::STAN_WYSLANO => __('wysłano', 'przystan'),
            self::STAN_PONOWIENIE => __('ponowienie zaplanowane', 'przystan'),
            self::STAN_BLAD => __('błąd', 'przystan'),
            self::STAN_BEZ_ADRESU => __('webhook wyłączony', 'przystan'),
            default => __('oczekuje', 'przystan'),
        };
    }
}
