<?php

/**
 * Lokalny odbiornik do testu webhooka: sprawdza podpis i zapisuje ostatnie żądanie do pliku
 * przystan-webhook.json w katalogu tymczasowym systemu.
 * Uruchomienie: PRZYSTAN_KLUCZ=<klucz z ustawień> php -S localhost:8899 scripts/odbiornik-webhooka.php
 */
$tresc = (string) file_get_contents('php://input');
$czas = (int) ($_SERVER['HTTP_X_PRZYSTAN_TIMESTAMP'] ?? 0);
$podpis = (string) ($_SERVER['HTTP_X_PRZYSTAN_SIGNATURE'] ?? '');
$oczekiwany = 'sha256=' . hash_hmac('sha256', $czas . '.' . $tresc, (string) getenv('PRZYSTAN_KLUCZ'));
$ok = hash_equals($oczekiwany, $podpis) && abs(time() - $czas) < 300;

$plik = sys_get_temp_dir() . '/przystan-webhook.json';
file_put_contents($plik, json_encode([
    'podpis_ok' => $ok,
    'zdarzenie' => $_SERVER['HTTP_X_PRZYSTAN_EVENT'] ?? '',
    'tresc' => json_decode($tresc, true),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

http_response_code($ok ? 200 : 401);
echo $ok ? 'ok' : 'zly podpis';
