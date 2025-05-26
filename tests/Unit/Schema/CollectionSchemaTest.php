<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Schema;

use OpenFGA\Schema\CollectionSchema;
use InvalidArgumentException;

test('CollectionSchema can be created with valid class and item type', function (): void {
    $schema = new CollectionSchema(
        className: 'ArrayObject',
        itemType: 'DateTime',
        requireItems: true,
    );

    expect($schema->getClassName())->toBe('ArrayObject')
        ->and($schema->getItemType())->toBe('DateTime')
        ->and($schema->requiresItems())->toBeTrue()
        ->and($schema->getProperties())->toBe([])
        ->and($schema->getProperty('any'))->toBeNull();
});

test('CollectionSchema throws exception for invalid class name', function (): void {
    expect(static fn () => new CollectionSchema('NonexistentClass', 'DateTime'))
        ->toThrow(InvalidArgumentException::class, 'Class "NonexistentClass" does not exist or cannot be autoloaded');
});

test('CollectionSchema throws exception for invalid item type', function (): void {
    expect(static fn () => new CollectionSchema('ArrayObject', 'NonexistentType'))
        ->toThrow(InvalidArgumentException::class, 'Item type "NonexistentType" does not exist or cannot be autoloaded');
});

test('CollectionSchema has default requireItems as false', function (): void {
    $schema = new CollectionSchema('ArrayObject', 'DateTime');

    expect($schema->requiresItems())->toBeFalse();
});
