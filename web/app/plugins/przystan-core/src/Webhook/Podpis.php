<?php

namespace Przystan\Webhook;

/**
 * Podpis webhooka: HMAC-SHA256 z "znacznik_czasu.treść". Odbiorca liczy to samo swoim kluczem
 * i porównuje z nagłówkiem X-Przystan-Signature; znacznik czasu chroni przed powtórką starej wiadomości.
 */
final class Podpis
{
    public static function podpisz(string $tresc, int $znacznikCzasu, string $klucz): string
    {
        return 'sha256=' . hash_hmac('sha256', $znacznikCzasu . '.' . $tresc, $klucz);
    }

    public static function sprawdz(string $podpis, string $tresc, int $znacznikCzasu, string $klucz): bool
    {
        return hash_equals(self::podpisz($tresc, $znacznikCzasu, $klucz), $podpis);
    }
}
