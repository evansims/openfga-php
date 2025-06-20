<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Schemas;

use DateTimeImmutable;
use OpenFGA\Schemas\{Schema, SchemaBuilder};

describe('SchemaBuilder', function (): void {
    test('creates a schema with the correct class name', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $schema = $builder->register();

        expect($schema)->toBeInstanceOf(Schema::class)
            ->and($schema->getClassName())->toBe('TestClass')
            ->and($schema->getProperties())->toBeEmpty();
    });

    test('can add string property', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $schema = $builder->string('name', required: true, default: 'test')->register();

        $property = $schema->getProperty('name');

        expect($property)->not->toBeNull()
            ->and($property->name)->toBe('name')
            ->and($property->type)->toBe('string')
            ->and($property->required)->toBeTrue()
            ->and($property->default)->toBe('test');
    });

    test('can add integer property', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $schema = $builder->integer('age', required: true, default: 25)->register();

        $property = $schema->getProperty('age');

        expect($property)->not->toBeNull()
            ->and($property->type)->toBe('integer')
            ->and($property->default)->toBe(25);
    });

    test('can add number property', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $schema = $builder->number('price', required: true, default: 9.99)->register();

        $property = $schema->getProperty('price');

        expect($property)->not->toBeNull()
            ->and($property->type)->toBe('number')
            ->and($property->default)->toBe(9.99);
    });

    test('can add boolean property', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $schema = $builder->boolean('active', required: true, default: true)->register();

        $property = $schema->getProperty('active');

        expect($property)->not->toBeNull()
            ->and($property->type)->toBe('boolean')
            ->and($property->default)->toBeTrue();
    });

    test('can add array property', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $schema = $builder->array('tags', ['type' => 'string'], required: true, default: ['php', 'test'])->register();

        $property = $schema->getProperty('tags');

        expect($property)->not->toBeNull()
            ->and($property->type)->toBe('array')
            ->and($property->items)->toBe(['type' => 'string'])
            ->and($property->default)->toBe(['php', 'test']);
    });

    test('can add object property', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $schema = $builder->object('address', 'AddressClass', required: true)->register();

        $property = $schema->getProperty('address');

        expect($property)->not->toBeNull()
            ->and($property->type)->toBe('object')
            ->and($property->className)->toBe('AddressClass');
    });

    test('can add date property', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $date = new DateTimeImmutable('2023-01-01');
        $schema = $builder->date('createdAt', required: true, default: $date)->register();

        $property = $schema->getProperty('createdAt');

        expect($property)->not->toBeNull()
            ->and($property->type)->toBe('string')
            ->and($property->format)->toBe('date')
            ->and($property->default)->toBe($date);
    });

    test('can add enum property', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $schema = $builder->string('status', required: true, enum: ['active', 'inactive', 'pending'], default: 'pending')->register();

        $property = $schema->getProperty('status');

        expect($property)->not->toBeNull()
            ->and($property->type)->toBe('string')
            ->and($property->enum)->toBe(['active', 'inactive', 'pending'])
            ->and($property->default)->toBe('pending');
    });

    test('can add datetime property', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $date = new DateTimeImmutable('2023-01-01T10:30:00Z');
        $schema = $builder->datetime('updatedAt', required: true, default: $date)->register();

        $property = $schema->getProperty('updatedAt');

        expect($property)->not->toBeNull()
            ->and($property->type)->toBe('string')
            ->and($property->format)->toBe('datetime')
            ->and($property->required)->toBeTrue()
            ->and($property->default)->toBe($date);
    });

    test('string method supports format parameter', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $schema = $builder->string('email', required: true, format: 'email', default: 'test@example.com')->register();

        $property = $schema->getProperty('email');

        expect($property)->not->toBeNull()
            ->and($property->type)->toBe('string')
            ->and($property->format)->toBe('email')
            ->and($property->required)->toBeTrue()
            ->and($property->default)->toBe('test@example.com');
    });

    test('array method supports object className', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $schema = $builder->array('items', ['type' => 'object', 'className' => 'ItemClass'], required: true)->register();

        $property = $schema->getProperty('items');

        expect($property)->not->toBeNull()
            ->and($property->type)->toBe('array')
            ->and($property->items)->toBe(['type' => 'object', 'className' => 'ItemClass'])
            ->and($property->required)->toBeTrue();
    });

    test('maintains fluent interface for all methods', function (): void {
        $builder = new SchemaBuilder('TestClass');

        $result = $builder
            ->string('name')
            ->integer('age')
            ->number('price')
            ->boolean('active')
            ->array('tags', ['type' => 'string'])
            ->object('address', 'AddressClass')
            ->date('createdAt')
            ->datetime('updatedAt');

        expect($result)->toBe($builder);
    });

    test('properties have correct default values', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $schema = $builder
            ->string('optional_string')
            ->integer('optional_int')
            ->number('optional_number')
            ->boolean('optional_bool')
            ->object('optional_object', 'SomeClass')
            ->register();

        $properties = $schema->getProperties();

        foreach ($properties as $property) {
            expect($property->required)->toBeFalse();
            expect($property->default)->toBeNull();
        }
    });

    test('can add multiple properties', function (): void {
        $builder = new SchemaBuilder('TestClass');
        $schema = $builder
            ->string('name', required: true)
            ->integer('age', required: true)
            ->register();

        expect($schema->getProperties())->toHaveCount(2)
            ->and($schema->getProperty('name'))->not->toBeNull()
            ->and($schema->getProperty('age'))->not->toBeNull();
    });
});
