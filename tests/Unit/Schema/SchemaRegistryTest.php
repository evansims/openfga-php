<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Schema;

use ReflectionClass;
use OpenFGA\Schema\{Schema, SchemaBuilder, SchemaRegistry};

beforeEach(function (): void {
    // Reset the static schemas array before each test
    $reflection = new ReflectionClass(SchemaRegistry::class);
    $property = $reflection->getProperty('schemas');
    $property->setAccessible(true);
    $property->setValue(null, []);
});

test('SchemaRegistry can create a SchemaBuilder', function (): void {
    $builder = SchemaRegistry::create('TestClass');

    expect($builder)->toBeInstanceOf(SchemaBuilder::class);
});

test('SchemaRegistry can register and retrieve a schema', function (): void {
    $schema = new Schema('TestClass');

    SchemaRegistry::register($schema);
    $retrieved = SchemaRegistry::get('TestClass');

    expect($retrieved)->toBe($schema);
});

test('SchemaRegistry returns null for unregistered class', function (): void {
    $retrieved = SchemaRegistry::get('NonexistentClass');

    expect($retrieved)->toBeNull();
});

test('SchemaRegistry overwrites existing schema for same class', function (): void {
    $schema1 = new Schema('TestClass');
    $schema2 = new Schema('TestClass');

    SchemaRegistry::register($schema1);
    SchemaRegistry::register($schema2);

    $retrieved = SchemaRegistry::get('TestClass');

    expect($retrieved)->toBe($schema2);
});
