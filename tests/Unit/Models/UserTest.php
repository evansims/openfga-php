<?php

declare(strict_types=1);

use OpenFGA\Models\{DifferenceV1, ObjectRelation, TypedWildcard, User, Userset, UsersetUser};

test('constructor and getters', function (): void {
    $object = new stdClass();
    $object->type = 'user';
    $object->id = '123';

    $userset = new UsersetUser('document', '1', 'writer');
    $wildcard = new TypedWildcard('user');

    // Create a difference object with valid Userset instances
    $base = new Userset(computedUserset: new ObjectRelation('document:1', 'reader'));
    $subtract = new Userset(computedUserset: new ObjectRelation('user:1', ''));
    $difference = new DifferenceV1($base, $subtract);

    $user = new User(
        object: $object,
        userset: $userset,
        wildcard: $wildcard,
        difference: $difference,
    );

    expect($user->getObject())->toBe($object)
        ->and($user->getUserset())->toBe($userset)
        ->and($user->getWildcard())->toBe($wildcard)
        ->and($user->getDifference())->toBe($difference);
});

test('json serialize with all properties', function (): void {
    $object = new stdClass();
    $object->type = 'user';
    $object->id = '123';

    $userset = new UsersetUser('document', '1', 'writer');
    $wildcard = new TypedWildcard('user');

    // Create a difference object with valid Userset instances
    $base = new Userset(computedUserset: new ObjectRelation('document:1', 'reader'));
    $subtract = new Userset(computedUserset: new ObjectRelation('user:1', ''));
    $difference = new DifferenceV1($base, $subtract);

    $user = new User(
        object: $object,
        userset: $userset,
        wildcard: $wildcard,
        difference: $difference,
    );

    $result = $user->jsonSerialize();

    expect($result)->toMatchArray([
        'object' => ['type' => 'user', 'id' => '123'],
        'userset' => ['type' => 'document', 'id' => '1', 'relation' => 'writer'],
        'wildcard' => $wildcard->jsonSerialize(),
        'difference' => $difference->jsonSerialize(),
    ]);
});

test('json serialize with null properties', function (): void {
    $user = new User();

    $result = $user->jsonSerialize();

    expect($result)->toBeEmpty();
});

test('json serialize with only object', function (): void {
    $object = new stdClass();
    $object->type = 'user';
    $object->id = '123';

    $user = new User(object: $object);

    $result = $user->jsonSerialize();

    expect($result)->toMatchArray([
        'object' => ['type' => 'user', 'id' => '123'],
    ]);
});

test('schema returns correct schema', function (): void {
    $schema = User::schema();

    $properties = $schema->getProperties();

    expect($properties)->toHaveCount(4);
    $propertyNames = array_map(static fn ($prop) => $prop->name, $properties);
    expect($propertyNames)->toContain('object')
        ->and($propertyNames)->toContain('userset')
        ->and($propertyNames)->toContain('wildcard')
        ->and($propertyNames)->toContain('difference');
});
