<?php

declare(strict_types=1);

use OpenFGA\Models\UserTypeFilter;

test('constructor and getters', function (): void {
    $type = 'user';
    $relation = 'member';

    $filter = new UserTypeFilter(
        type: $type,
        relation: $relation,
    );

    expect($filter->getType())->toBe($type)
        ->and($filter->getRelation())->toBe($relation);
});

test('constructor with required fields only', function (): void {
    $type = 'user';

    $filter = new UserTypeFilter(
        type: $type,
    );

    expect($filter->getType())->toBe($type)
        ->and($filter->getRelation())->toBeNull();
});

test('json serialize with all properties', function (): void {
    $type = 'user';
    $relation = 'member';

    $filter = new UserTypeFilter(
        type: $type,
        relation: $relation,
    );

    $result = $filter->jsonSerialize();

    expect($result)->toMatchArray([
        'type' => $type,
        'relation' => $relation,
    ]);
});

test('json serialize with required fields only', function (): void {
    $type = 'user';

    $filter = new UserTypeFilter(
        type: $type,
    );

    $result = $filter->jsonSerialize();

    expect($result)->toMatchArray([
        'type' => $type,
    ]);
});

test('schema returns correct schema', function (): void {
    $schema = UserTypeFilter::schema();

    $properties = $schema->getProperties();

    // Convert properties to an associative array for easier validation
    $propertyMap = [];
    foreach ($properties as $name => $property) {
        $propertyMap[$name] = $property->required;
    }

    expect($propertyMap)->toHaveCount(2)
        ->toHaveKey('type')
        ->toHaveKey('relation')
        ->and($propertyMap['type'])->toBeTrue()
        ->and($propertyMap['relation'] ?? null)->toBeFalse();
});
