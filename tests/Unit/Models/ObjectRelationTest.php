<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use OpenFGA\Models\{ObjectRelation, ObjectRelationInterface};

test('constructor with all properties', function (): void {
    $object = 'document:1';
    $relation = 'reader';

    $objectRelation = new ObjectRelation(
        object: $object,
        relation: $relation,
    );

    expect($objectRelation->getObject())->toBe($object)
        ->and($objectRelation->getRelation())->toBe($relation);
});

test('constructor with null properties', function (): void {
    $objectRelation = new ObjectRelation();

    expect($objectRelation->getObject())->toBeNull()
        ->and($objectRelation->getRelation())->toBeNull();
});

test('json serialize with all properties', function (): void {
    $object = 'document:1';
    $relation = 'reader';

    $objectRelation = new ObjectRelation(
        object: $object,
        relation: $relation,
    );

    $result = $objectRelation->jsonSerialize();

    expect($result)->toBe([
        'object' => $object,
        'relation' => $relation,
    ]);
});

test('json serialize with null properties', function (): void {
    $objectRelation = new ObjectRelation();

    $result = $objectRelation->jsonSerialize();

    expect($result)->toBe([]);
});

test('schema', function (): void {
    $schema = ObjectRelation::schema();

    expect($schema->getClassName())->toBe(ObjectRelation::class);

    $properties = $schema->getProperties();
    expect($properties)->toBeArray()
        ->toHaveCount(2);

    // Check object property
    expect($properties)->toHaveKey('object')
        ->and($properties['object']->name)->toBe('object')
        ->and($properties['object']->type)->toBe('string')
        ->and($properties['object']->required)->toBeFalse();

    // Check relation property
    expect($properties)->toHaveKey('relation')
        ->and($properties['relation']->name)->toBe('relation')
        ->and($properties['relation']->type)->toBe('string')
        ->and($properties['relation']->required)->toBeFalse();
});

test('implements ObjectRelationInterface', function (): void {
    $objectRelation = new ObjectRelation();
    expect($objectRelation)->toBeInstanceOf(ObjectRelationInterface::class);
});

test('empty strings are treated as valid values', function (): void {
    $objectRelation = new ObjectRelation(
        object: '',
        relation: '',
    );

    expect($objectRelation->getObject())->toBe('')
        ->and($objectRelation->getRelation())->toBe('');
});

test('json serialize with empty strings', function (): void {
    $objectRelation = new ObjectRelation(
        object: '',
        relation: '',
    );

    $result = $objectRelation->jsonSerialize();

    expect($result)->toBe([
        'object' => '',
        'relation' => '',
    ]);
});

test('json serialize with partial null values', function (): void {
    // Test with null object
    $objectRelation1 = new ObjectRelation(
        object: null,
        relation: 'reader',
    );
    $result1 = $objectRelation1->jsonSerialize();
    expect($result1)->toBe(['relation' => 'reader']);

    // Test with null relation
    $objectRelation2 = new ObjectRelation(
        object: 'document:1',
        relation: null,
    );
    $result2 = $objectRelation2->jsonSerialize();
    expect($result2)->toBe(['object' => 'document:1']);
});

test('json encode with all properties', function (): void {
    $object = 'document:1';
    $relation = 'reader';

    $objectRelation = new ObjectRelation(
        object: $object,
        relation: $relation,
    );

    $json = json_encode($objectRelation, JSON_THROW_ON_ERROR);
    $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

    expect($decoded)->toBe([
        'object' => $object,
        'relation' => $relation,
    ]);
});

test('json encode with empty object', function (): void {
    $objectRelation = new ObjectRelation();
    $json = json_encode($objectRelation, JSON_THROW_ON_ERROR);
    $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

    expect($decoded)->toBe([]);
});
