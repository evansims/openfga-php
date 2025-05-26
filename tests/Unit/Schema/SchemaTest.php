<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Schema;

use OpenFGA\Schema\{Schema, SchemaProperty};

test('Schema can be created with class name', function (): void {
    $schema = new Schema('TestClass');

    expect($schema->getClassName())->toBe('TestClass')
        ->and($schema->getProperties())->toBe([])
        ->and($schema->getProperty('nonexistent'))->toBeNull();
});

test('Schema can be created with properties', function (): void {
    $property1 = new SchemaProperty('name', 'string');
    $property2 = new SchemaProperty('age', 'integer');

    $schema = new Schema('TestClass', [$property1, $property2]);

    $properties = $schema->getProperties();

    expect($properties)->toHaveCount(2)
        ->and($properties['name'])->toBe($property1)
        ->and($properties['age'])->toBe($property2)
        ->and($schema->getProperty('name'))->toBe($property1)
        ->and($schema->getProperty('age'))->toBe($property2);
});

test('Schema properties are indexed by name', function (): void {
    $property1 = new SchemaProperty('name', 'string');
    $property2 = new SchemaProperty('name', 'integer'); // Same name, different type

    $schema = new Schema('TestClass', [$property1, $property2]);

    $properties = $schema->getProperties();

    expect($properties)->toHaveCount(1)
        ->and($properties['name']->type)->toBe('integer'); // Last one wins
});
