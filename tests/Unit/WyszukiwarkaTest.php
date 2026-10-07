<?php

use Przystan\Mieszkania\Wyszukiwarka;

it('bez filtrów zwraca wszystkie mieszkania posortowane od parteru', function () {
    $args = Wyszukiwarka::argumenty(['status' => 'wszystkie'], 'pl');

    expect($args['post_type'])->toBe('mieszkanie')
        ->and($args['posts_per_page'])->toBe(-1)
        ->and($args['no_found_rows'])->toBeTrue()
        ->and($args['lang'])->toBe('pl')
        ->and($args['meta_query'])->toBe([
            'relation' => 'AND',
            'pietro_sort' => ['key' => 'pietro', 'type' => 'NUMERIC'],
            'pozycja_sort' => ['key' => 'pozycja', 'type' => 'NUMERIC'],
        ])
        ->and($args['orderby'])->toBe(['pietro_sort' => 'ASC', 'pozycja_sort' => 'ASC']);
});

it('zamienia filtry na warunki meta_query', function () {
    $args = Wyszukiwarka::argumenty([
        'pokoje' => [2, 3],
        'pietro' => 4,
        'metraz_min' => 40.0,
        'metraz_max' => 65.5,
        'status' => 'wolne',
        'widok' => true,
        'balkon' => true,
    ], 'en');

    expect($args['lang'])->toBe('en');
    $warunki = array_values(array_filter($args['meta_query'], 'is_int', ARRAY_FILTER_USE_KEY));

    expect($warunki)->toBe([
        ['key' => 'pokoje', 'value' => [2, 3], 'compare' => 'IN', 'type' => 'NUMERIC'],
        ['key' => 'pietro', 'value' => 4, 'compare' => '=', 'type' => 'NUMERIC'],
        ['key' => 'metraz', 'value' => 40.0, 'compare' => '>=', 'type' => 'DECIMAL(6,2)'],
        ['key' => 'metraz', 'value' => 65.5, 'compare' => '<=', 'type' => 'DECIMAL(6,2)'],
        ['key' => 'status', 'value' => 'wolne', 'compare' => '='],
        ['key' => 'widok_na_rzeke', 'value' => '1', 'compare' => '='],
        ['key' => 'balkon_m2', 'value' => 0, 'compare' => '>', 'type' => 'DECIMAL(6,2)'],
    ]);
});

it('filtruje po inwestycji i po liście ID', function () {
    $args = Wyszukiwarka::argumenty(['inwestycja' => 12, 'ids' => [5, 7], 'status' => 'wszystkie'], 'pl');

    expect($args['post__in'])->toBe([5, 7])
        ->and(array_values(array_filter($args['meta_query'], 'is_int', ARRAY_FILTER_USE_KEY)))
        ->toBe([['key' => 'inwestycja', 'value' => 12, 'compare' => '=', 'type' => 'NUMERIC']]);
});

it('daje inny klucz pamięci podręcznej dla innych filtrów i języków', function () {
    $a = Wyszukiwarka::kluczCache(['status' => 'wolne'], 'pl');
    $b = Wyszukiwarka::kluczCache(['status' => 'wolne'], 'en');
    $c = Wyszukiwarka::kluczCache(['status' => 'sprzedane'], 'pl');

    expect($a)->not->toBe($b)->and($a)->not->toBe($c)
        ->and(strlen($a))->toBeLessThanOrEqual(172);
});
