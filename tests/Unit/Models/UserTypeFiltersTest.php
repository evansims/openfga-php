<?php

declare(strict_types=1);

use OpenFGA\Models\{UserTypeFilter, UserTypeFilters};

test('can create empty collection', function (): void {
    $filters = new UserTypeFilters();

    expect($filters)->toHaveCount(0);
});

test('can create collection with items', function (): void {
    $filter1 = new UserTypeFilter('user', 'member');
    $filter2 = new UserTypeFilter('group');

    $filters = new UserTypeFilters($filter1, $filter2);

    expect($filters)->toHaveCount(2)
        ->and($filters[0])->toBe($filter1)
        ->and($filters[1])->toBe($filter2);
});

test('can create collection with iterable', function (): void {
    $filter1 = new UserTypeFilter('user', 'member');
    $filter2 = new UserTypeFilter('group');
    $filterArray = [$filter1, $filter2];

    $filters = new UserTypeFilters($filterArray);

    expect($filters)->toHaveCount(2)
        ->and($filters[0])->toBe($filter1)
        ->and($filters[1])->toBe($filter2);
});

test('can iterate over collection', function (): void {
    $filter1 = new UserTypeFilter('user', 'member');
    $filter2 = new UserTypeFilter('group');
    $filters = new UserTypeFilters($filter1, $filter2);

    $items = [];
    foreach ($filters as $filter) {
        $items[] = $filter;
    }

    expect($items)->toHaveCount(2)
        ->and($items[0])->toBe($filter1)
        ->and($items[1])->toBe($filter2);
});

test('json serialize returns array of serialized items', function (): void {
    $filter1 = new UserTypeFilter('user', 'member');
    $filter2 = new UserTypeFilter('group');
    $filters = new UserTypeFilters($filter1, $filter2);

    $result = $filters->jsonSerialize();

    expect($result)->toBeArray()
        ->toHaveCount(2)
        ->and($result[0])->toBe($filter1->jsonSerialize())
        ->and($result[1])->toBe($filter2->jsonSerialize());
});

test('can add items to collection', function (): void {
    $filter1 = new UserTypeFilter('user', 'member');
    $filter2 = new UserTypeFilter('group');

    $filters = new UserTypeFilters();
    $filters[] = $filter1;
    $filters[] = $filter2;

    expect($filters)->toHaveCount(2)
        ->and($filters[0])->toBe($filter1)
        ->and($filters[1])->toBe($filter2);
});

test('can check if offset exists', function (): void {
    $filter = new UserTypeFilter('user', 'member');
    $filters = new UserTypeFilters($filter);

    expect(isset($filters[0]))->toBeTrue()
        ->and(isset($filters[1]))->toBeFalse();
});

test('can unset offset', function (): void {
    $filter1 = new UserTypeFilter('user', 'member');
    $filter2 = new UserTypeFilter('group');
    $filters = new UserTypeFilters($filter1, $filter2);

    unset($filters[0]);

    expect($filters)->toHaveCount(1)
        ->and($filters[0])->toBe($filter2);
});

test('schema returns correct schema', function (): void {
    $schema = UserTypeFilters::schema();

    expect($schema->getItemType())->toBe(UserTypeFilter::class);
});
