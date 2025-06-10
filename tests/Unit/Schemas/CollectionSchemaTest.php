<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Schemas;

use OpenFGA\Exceptions\ClientException;
use OpenFGA\Messages;
use OpenFGA\Schemas\CollectionSchema;

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
        new CollectionSchema('NonexistentClass', 'DateTime');
    })->throws(ClientException::class, trans(Messages::SCHEMA_CLASS_NOT_FOUND, ['className' => 'NonexistentClass']));

    test('throws exception for invalid item type', function (): void {
        new CollectionSchema('ArrayObject', 'NonexistentType');
    })->throws(ClientException::class, trans(Messages::SCHEMA_ITEM_TYPE_NOT_FOUND, ['itemType' => 'NonexistentType']));

    test('has default requireItems as false', function (): void {
        $schema = new CollectionSchema('ArrayObject', 'DateTime');

        expect($schema->requiresItems())->toBeFalse();
    });
});
