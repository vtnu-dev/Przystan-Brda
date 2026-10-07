<?php

use Przystan\Webhook\Podpis;
use Przystan\Zapytania\Antyspam;

it('podpisuje treść webhooka HMAC-SHA256 razem ze znacznikiem czasu', function () {
    $podpis = Podpis::podpisz('{"a":1}', 1791400000, 'tajny');

    expect($podpis)->toBe('sha256=' . hash_hmac('sha256', '1791400000.{"a":1}', 'tajny'))
        ->and(Podpis::sprawdz($podpis, '{"a":1}', 1791400000, 'tajny'))->toBeTrue()
        ->and(Podpis::sprawdz($podpis, '{"a":2}', 1791400000, 'tajny'))->toBeFalse()
        ->and(Podpis::sprawdz($podpis, '{"a":1}', 1791400001, 'tajny'))->toBeFalse();
});

it('akceptuje formularz wypełniony po co najmniej 3 sekundach', function () {
    $znacznik = Antyspam::znacznik(1000, 'klucz');

    expect(Antyspam::czasOk($znacznik, 1002, 'klucz'))->toBeFalse()
        ->and(Antyspam::czasOk($znacznik, 1003, 'klucz'))->toBeTrue()
        ->and(Antyspam::czasOk($znacznik, 1000 + 7201, 'klucz'))->toBeFalse();
});

it('odrzuca podrobiony znacznik czasu', function () {
    $znacznik = Antyspam::znacznik(1000, 'klucz');
    [$czas] = explode('.', $znacznik);

    expect(Antyspam::czasOk('500.' . str_repeat('0', 64), 1010, 'klucz'))->toBeFalse()
        ->and(Antyspam::czasOk($czas, 1010, 'klucz'))->toBeFalse()
        ->and(Antyspam::czasOk('', 1010, 'klucz'))->toBeFalse()
        ->and(Antyspam::czasOk($znacznik, 1010, 'inny'))->toBeFalse();
});

it('wykrywa wypełnioną pułapkę', function () {
    expect(Antyspam::pulapka(['strona_www' => 'http://spam']))->toBeTrue()
        ->and(Antyspam::pulapka(['strona_www' => '']))->toBeFalse()
        ->and(Antyspam::pulapka([]))->toBeFalse();
});
