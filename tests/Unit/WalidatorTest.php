<?php

use Przystan\Zapytania\Walidator;

function poprawne(array $zmiany = []): array
{
    return array_merge([
        'imie' => '  Anna Kowalska ',
        'email' => 'anna@example.com',
        'telefon' => '+48 600 100 200',
        'wiadomosc' => 'Czy mieszkanie ma komórkę lokatorską?',
        'mieszkanie' => '31',
        'zgoda' => '1',
    ], $zmiany);
}

it('przepuszcza poprawne dane i je czyści', function () {
    $wynik = Walidator::sprawdz(poprawne());

    expect($wynik['bledy'])->toBe([])
        ->and($wynik['dane'])->toBe([
            'imie' => 'Anna Kowalska',
            'email' => 'anna@example.com',
            'telefon' => '+48 600 100 200',
            'wiadomosc' => 'Czy mieszkanie ma komórkę lokatorską?',
            'mieszkanie' => 31,
            'zgoda' => true,
            'inwestycja' => 0,
            'rodzaj' => 'zapytanie',
        ]);
});

it('zgłasza brak wymaganych pól', function () {
    $wynik = Walidator::sprawdz([]);

    expect($wynik['bledy'])->toBe([
        'imie' => 'imie_wymagane',
        'email' => 'email_wymagany',
        'zgoda' => 'zgoda_wymagana',
    ]);
});

it('odrzuca zły e-mail, telefon i za długą wiadomość', function () {
    $wynik = Walidator::sprawdz(poprawne([
        'email' => 'anna@',
        'telefon' => 'zadzwoń wieczorem',
        'wiadomosc' => str_repeat('a', 2001),
    ]));

    expect($wynik['bledy'])->toBe([
        'email' => 'email_niepoprawny',
        'telefon' => 'telefon_niepoprawny',
        'wiadomosc' => 'wiadomosc_za_dluga',
    ]);
});

it('telefon i mieszkanie są opcjonalne', function () {
    $wynik = Walidator::sprawdz(poprawne(['telefon' => '', 'mieszkanie' => '']));

    expect($wynik['bledy'])->toBe([])
        ->and($wynik['dane']['telefon'])->toBe('')
        ->and($wynik['dane']['mieszkanie'])->toBe(0);
});

it('usuwa znaczniki HTML z tekstu', function () {
    $wynik = Walidator::sprawdz(poprawne(['imie' => '<b>Jan</b>', 'wiadomosc' => "Linia 1\n<script>x</script>Linia 2"]));

    expect($wynik['dane']['imie'])->toBe('Jan')
        ->and($wynik['dane']['wiadomosc'])->toBe("Linia 1\nxLinia 2");
});

it('rozpoznaje zapis „powiadom mnie” o planowanej inwestycji', function () {
    $wynik = Walidator::sprawdz(poprawne(['rodzaj' => 'powiadomienie', 'inwestycja' => '7', 'mieszkanie' => '']));
    expect($wynik['dane']['rodzaj'])->toBe('powiadomienie')->and($wynik['dane']['inwestycja'])->toBe(7);

    expect(Walidator::sprawdz(poprawne(['rodzaj' => 'cokolwiek']))['dane']['rodzaj'])->toBe('zapytanie');
});

it('odrzuca zbyt krótkie imię', function () {
    expect(Walidator::sprawdz(poprawne(['imie' => 'A']))['bledy'])->toBe(['imie' => 'imie_za_krotkie']);
});
