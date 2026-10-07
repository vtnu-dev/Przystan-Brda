<?php

use App\Elewacja;

it('rozkłada okna według liczby kondygnacji i lokali inwestycji', function () {
    $e = new Elewacja(kondygnacje: 4, kolumny: 4);

    $parter = $e->okno(['pietro' => 0, 'pozycja' => 1]);
    $gora = $e->okno(['pietro' => 3, 'pozycja' => 4]);

    expect($parter['y'])->toBeGreaterThan($gora['y'])
        ->and($gora['x'])->toBeGreaterThan($parter['x'])
        ->and($e->dol())->toBe(Elewacja::GORA + 4 * Elewacja::WYS_PIETRA)
        ->and($gora['x'] + $gora['w'])->toBeLessThanOrEqual(Elewacja::X1);
});

it('nie wychodzi poza budynek przy złej pozycji', function () {
    $e = new Elewacja(kondygnacje: 6, kolumny: 6);

    expect($e->okno(['pietro' => 0, 'pozycja' => 99]))->toBe($e->okno(['pietro' => 0, 'pozycja' => 6]))
        ->and($e->okno(['pietro' => 0, 'pozycja' => 0]))->toBe($e->okno(['pietro' => 0, 'pozycja' => 1]));
});
