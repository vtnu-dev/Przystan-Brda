<?php

use Przystan\Mieszkania\Filtry;

it('zostawia tylko znane i poprawne parametry', function () {
    $filtry = Filtry::z([
        'pokoje' => '3',
        'pietro' => '2',
        'metraz_min' => '45',
        'metraz_max' => '70',
        'status' => 'wolne',
        'widok' => '1',
        'balkon' => 'on',
        'hakerski' => 'DROP TABLE',
    ]);

    expect($filtry)->toBe([
        'pokoje' => [3],
        'pietro' => 2,
        'metraz_min' => 45.0,
        'metraz_max' => 70.0,
        'status' => 'wolne',
        'widok' => true,
        'balkon' => true,
    ]);
});

it('pomija wartości spoza zakresu zamiast rzucać wyjątek', function () {
    expect(Filtry::z([
        'pokoje' => '9',
        'pietro' => '-1',
        'metraz_min' => 'abc',
        'status' => 'cokolwiek',
    ]))->toBe(['status' => 'wszystkie']);
});

it('przyjmuje kilka wartości pokoi jako listę lub tekst z przecinkami', function () {
    expect(Filtry::z(['pokoje' => ['2', '3']])['pokoje'])->toBe([2, 3])
        ->and(Filtry::z(['pokoje' => '1,4,4'])['pokoje'])->toBe([1, 4]);
});

it('domyślnie pokazuje wszystkie statusy', function () {
    expect(Filtry::z([]))->toBe(['status' => 'wszystkie']);
});

it('zamienia metraże, gdy podano je w złej kolejności', function () {
    $f = Filtry::z(['metraz_min' => '80', 'metraz_max' => '40']);
    expect($f['metraz_min'])->toBe(40.0)->and($f['metraz_max'])->toBe(80.0);
});

it('buduje parametry do adresu URL bez wartości domyślnych', function () {
    expect(Filtry::doUrl(['pokoje' => [2, 3], 'status' => 'wszystkie', 'widok' => true]))
        ->toBe(['pokoje' => '2,3', 'widok' => '1']);
});
