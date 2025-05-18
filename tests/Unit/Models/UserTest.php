<?php

declare(strict_types=1);

use OpenFGA\Models\{DifferenceV1, TypedWildcard, User, UsersetUser};

test('constructor and getters', function (): void {
    $object = new stdClass();
    $object->type = 'user';
    $object->id = '123';

    $userset = new UsersetUser('document:1#writer');
    $wildcard = new TypedWildcard('user');
    $difference = new DifferenceV1(new UsersetUser('document:1#reader'), new UsersetUser('user:1'));

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

    $userset = new UsersetUser('document:1#writer');
    $wildcard = new TypedWildcard('user');
    $difference = new DifferenceV1(new UsersetUser('document:1#reader'), new UsersetUser('user:1'));

    $user = new User(
        object: $object,
        userset: $userset,
        wildcard: $wildcard,
        difference: $difference,
    );

    $result = $user->jsonSerialize();

    expect($result)->toMatchArray([
        'object' => ['type' => 'user', 'id' => '123'],
        'userset' => $userset->jsonSerialize(),
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

    expect($properties)->toHaveCount(4)
        ->and($properties[0]->getName())->toBe('object')
        ->and($properties[1]->getName())->toBe('userset')
        ->and($properties[2]->getName())->toBe('wildcard')
        ->and($properties[3]->getName())->toBe('difference');
});
