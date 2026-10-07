<?php
/**
 * Lokalny odbiornik do testu webhooka: sprawdza podpis i zapisuje żądanie do pliku.
 * Uruchomienie: PRZYSTAN_KLUCZ=<klucz z ustawień> php -S localhost:8899 scripts/odbiornik-webhooka.php
 */
$tresc = (string) file_get_contents('php://input');
$czas = (int) ($_SERVER['HTTP_X_PRZYSTAN_TIMESTAMP'] ?? 0);
$podpis = (string) ($_SERVER['HTTP_X_PRZYSTAN_SIGNATURE'] ?? '');
$oczekiwany = 'sha256=' . hash_hmac('sha256', $czas . '.' . $tresc, (string) getenv('PRZYSTAN_KLUCZ'));
$ok = hash_equals($oczekiwany, $podpis) && abs(time() - $czas) < 300;

file_put_contents(__DIR__ . '/../_robocze/odbiornik/ostatni.json', json_encode([
    'podpis_ok' => $ok,
    'zdarzenie' => $_SERVER['HTTP_X_PRZYSTAN_EVENT'] ?? '',
    'tresc' => json_decode($tresc, true),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

http_response_code($ok ? 200 : 401);
echo $ok ? 'ok' : 'zly podpis';
