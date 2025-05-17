<?php

declare(strict_types=1);

use OpenFGA\Models\AssertionTupleKey;

test('constructor and getters', function (): void {
    $user = 'user:1';
    $relation = 'reader';
    $object = 'document:1';

    $tupleKey = new AssertionTupleKey($user, $relation, $object);

    expect($tupleKey->getUser())->toBe($user)
        ->and($tupleKey->getRelation())->toBe($relation)
        ->and($tupleKey->getObject())->toBe($object);
});

test('json serialize', function (): void {
    $user = 'user:1';
    $relation = 'reader';
    $object = 'document:1';

    $tupleKey = new AssertionTupleKey($user, $relation, $object);
    $result = $tupleKey->jsonSerialize();

    expect($result)->toMatchArray([
        'user' => $user,
        'relation' => $relation,
        'object' => $object,
    ]);
});

test('schema', function (): void {
    $schema = AssertionTupleKey::schema();

    expect($schema->getClassName())->toBe(AssertionTupleKey::class);

    $properties = $schema->getProperties();
    expect($properties)->toHaveCount(3);

    $expectedProps = ['user', 'relation', 'object'];
    foreach ($expectedProps as $propName) {
        expect($properties)->toHaveKey($propName);
        expect($properties[$propName]->required)->toBeTrue();
        expect($properties[$propName]->type)->toBe('string');
    }
});
