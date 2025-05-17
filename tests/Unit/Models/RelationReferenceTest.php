<?php

declare(strict_types=1);

use OpenFGA\Models\RelationReference;

test('constructor with all properties', function (): void {
    $type = 'document';
    $relation = 'reader';
    $wildcard = (object) ['type' => 'user'];
    $condition = 'condition1';

    $relationRef = new RelationReference(
        type: $type,
        relation: $relation,
        wildcard: $wildcard,
        condition: $condition,
    );

    expect($relationRef->getType())->toBe($type)
        ->and($relationRef->getRelation())->toBe($relation)
        ->and($relationRef->getWildcard())->toBe($wildcard)
        ->and($relationRef->getCondition())->toBe($condition);
});

test('constructor with required properties only', function (): void {
    $type = 'document';

    $relationRef = new RelationReference(
        type: $type,
    );

    expect($relationRef->getType())->toBe($type)
        ->and($relationRef->getRelation())->toBeNull()
        ->and($relationRef->getWildcard())->toBeNull()
        ->and($relationRef->getCondition())->toBeNull();
});

test('json serialize with all properties', function (): void {
    $type = 'document';
    $relation = 'reader';
    $wildcard = (object) ['type' => 'user'];
    $condition = 'condition1';

    $relationRef = new RelationReference(
        type: $type,
        relation: $relation,
        wildcard: $wildcard,
        condition: $condition,
    );

    $result = $relationRef->jsonSerialize();

    expect($result)->toBe([
        'type' => $type,
        'relation' => $relation,
        'wildcard' => $wildcard,
        'condition' => $condition,
    ]);
});

test('json serialize with required properties only', function (): void {
    $type = 'document';

    $relationRef = new RelationReference(
        type: $type,
    );

    $result = $relationRef->jsonSerialize();

    expect($result)->toBe([
        'type' => $type,
    ]);
});

test('schema', function (): void {
    $schema = RelationReference::schema();

    expect($schema->getClassName())->toBe(RelationReference::class);

    $properties = $schema->getProperties();
    expect($properties)->toBeArray()
        ->toHaveCount(4);

    // Check type property
    expect($properties)->toHaveKey('type')
        ->and($properties['type']->name)->toBe('type')
        ->and($properties['type']->type)->toBe('string')
        ->and($properties['type']->required)->toBeTrue();

    // Check relation property
    expect($properties)->toHaveKey('relation')
        ->and($properties['relation']->name)->toBe('relation')
        ->and($properties['relation']->type)->toBe('string')
        ->and($properties['relation']->required)->toBeFalse();

    // Check wildcard property
    expect($properties)->toHaveKey('wildcard')
        ->and($properties['wildcard']->name)->toBe('wildcard')
        ->and($properties['wildcard']->type)->toBe('object')
        ->and($properties['wildcard']->required)->toBeFalse();

    // Check condition property
    expect($properties)->toHaveKey('condition')
        ->and($properties['condition']->name)->toBe('condition')
        ->and($properties['condition']->type)->toBe('string')
        ->and($properties['condition']->required)->toBeFalse();
});
