<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Schema;

use OpenFGA\Schema\SchemaProperty;

test('SchemaProperty can be created with required fields', function (): void {
    $property = new SchemaProperty(
        name: 'test',
        type: 'string',
    );

    expect($property->name)->toBe('test')
        ->and($property->type)->toBe('string')
        ->and($property->required)->toBeFalse()
        ->and($property->default)->toBeNull()
        ->and($property->format)->toBeNull()
        ->and($property->enum)->toBeNull()
        ->and($property->items)->toBeNull()
        ->and($property->className)->toBeNull();
});

test('SchemaProperty can be created with all fields', function (): void {
    $property = new SchemaProperty(
        name: 'test',
        type: 'array',
        required: true,
        default: ['default'],
        format: 'date-time',
        enum: ['a', 'b', 'c'],
        items: ['type' => 'string'],
        className: 'TestClass',
    );

    expect($property->name)->toBe('test')
        ->and($property->type)->toBe('array')
        ->and($property->required)->toBeTrue()
        ->and($property->default)->toBe(['default'])
        ->and($property->format)->toBe('date-time')
        ->and($property->enum)->toBe(['a', 'b', 'c'])
        ->and($property->items)->toBe(['type' => 'string'])
        ->and($property->className)->toBe('TestClass');
});
