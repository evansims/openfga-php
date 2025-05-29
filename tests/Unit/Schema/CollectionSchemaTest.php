<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Schema;

use InvalidArgumentException;
use OpenFGA\Schema\CollectionSchema;

describe('CollectionSchema', function (): void {
    test('can be created with valid class and item type', function (): void {
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

    test('throws exception for invalid class name', function (): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class "NonexistentClass" does not exist or cannot be autoloaded');
        new CollectionSchema('NonexistentClass', 'DateTime');
    });

    test('throws exception for invalid item type', function (): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Item type "NonexistentType" does not exist or cannot be autoloaded');
        new CollectionSchema('ArrayObject', 'NonexistentType');
    });

    test('has default requireItems as false', function (): void {
        $schema = new CollectionSchema('ArrayObject', 'DateTime');

        expect($schema->requiresItems())->toBeFalse();
    });
});
