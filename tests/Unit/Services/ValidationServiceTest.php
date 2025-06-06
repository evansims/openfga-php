<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use OpenFGA\Exceptions\SerializationException;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty, ValidationService};
use stdClass;

describe('ValidationService', function (): void {
    beforeEach(function (): void {
        $this->service = new ValidationService;
    });

    describe('validate()', function (): void {
        test('validates data against registered schema', function (): void {
            // Create a test schema
            $schema = test()->createMock(SchemaInterface::class);
            $schema->method('getClassName')->willReturn('TestClass');

            $property1 = new SchemaProperty(
                name: 'name',
                type: 'string',
                required: true,
            );

            $property2 = new SchemaProperty(
                name: 'age',
                type: 'integer',
                required: false,
                default: 0,
            );

            $schema->method('getProperties')->willReturn([$property1, $property2]);

            // Register schema
            $this->service->registerSchema($schema);

            // Test valid data
            $data = ['name' => 'John Doe'];
            $result = $this->service->validate($data, 'TestClass');

            expect($result)->toBe(['name' => 'John Doe', 'age' => 0]);
        });

        test('throws exception for non-array data', function (): void {
            $this->service->validate('not an array', 'TestClass');
        })->throws(SerializationException::class, 'Invalid item type');

        test('throws exception for unregistered schema', function (): void {
            $this->service->validate(['test' => 'data'], 'UnknownClass');
        })->throws(SerializationException::class, 'Item type is not defined for UnknownClass');

        test('throws exception for missing required properties', function (): void {
            $schema = test()->createMock(SchemaInterface::class);
            $schema->method('getClassName')->willReturn('TestClass');

            $property = new SchemaProperty(
                name: 'required_field',
                type: 'string',
                required: true,
            );

            $schema->method('getProperties')->willReturn([$property]);

            $this->service->registerSchema($schema);
            $this->service->validate([], 'TestClass');
        })->throws(SerializationException::class, 'Invalid item type');

        test('validates nested objects', function (): void {
            // Parent schema
            $parentSchema = test()->createMock(SchemaInterface::class);
            $parentSchema->method('getClassName')->willReturn('ParentClass');

            $nestedProperty = new SchemaProperty(
                name: 'child',
                type: 'object',
                required: true,
                className: 'ChildClass',
            );

            $parentSchema->method('getProperties')->willReturn([$nestedProperty]);

            // Child schema
            $childSchema = test()->createMock(SchemaInterface::class);
            $childSchema->method('getClassName')->willReturn('ChildClass');

            $childProperty = new SchemaProperty(
                name: 'value',
                type: 'string',
                required: true,
            );

            $childSchema->method('getProperties')->willReturn([$childProperty]);

            // Register schemas
            $this->service->registerSchema($parentSchema);
            $this->service->registerSchema($childSchema);

            // Test nested validation
            $data = [
                'child' => ['value' => 'test'],
            ];

            $result = $this->service->validate($data, 'ParentClass');
            expect($result)->toBe(['child' => ['value' => 'test']]);
        });

        test('validates arrays of objects', function (): void {
            // Schema with array property
            $schema = test()->createMock(SchemaInterface::class);
            $schema->method('getClassName')->willReturn('TestClass');

            $arrayProperty = new SchemaProperty(
                name: 'items',
                type: 'array',
                required: true,
                items: ['type' => 'object', 'className' => 'ItemClass'],
            );

            $schema->method('getProperties')->willReturn([$arrayProperty]);

            // Item schema
            $itemSchema = test()->createMock(SchemaInterface::class);
            $itemSchema->method('getClassName')->willReturn('ItemClass');

            $itemProperty = new SchemaProperty(
                name: 'id',
                type: 'integer',
                required: true,
            );

            $itemSchema->method('getProperties')->willReturn([$itemProperty]);

            // Register schemas
            $this->service->registerSchema($schema);
            $this->service->registerSchema($itemSchema);

            // Test array validation
            $data = [
                'items' => [
                    ['id' => 1],
                    ['id' => 2],
                ],
            ];

            $result = $this->service->validate($data, 'TestClass');
            expect($result)->toBe([
                'items' => [
                    ['id' => 1],
                    ['id' => 2],
                ],
            ]);
        });
    });

    describe('validateProperty()', function (): void {
        test('validates string properties', function (): void {
            $property = new SchemaProperty(
                name: 'test',
                type: 'string',
                required: true,
            );

            $result = $this->service->validateProperty('hello', $property, 'test.path');
            expect($result)->toBe('hello');
        });

        test('validates integer properties', function (): void {
            $property = new SchemaProperty(
                name: 'test',
                type: 'integer',
                required: true,
            );

            $result = $this->service->validateProperty(42, $property, 'test.path');
            expect($result)->toBe(42);
        });

        test('validates boolean properties', function (): void {
            $property = new SchemaProperty(
                name: 'test',
                type: 'boolean',
                required: true,
            );

            $result = $this->service->validateProperty(true, $property, 'test.path');
            expect($result)->toBe(true);
        });

        test('validates enum properties', function (): void {
            $property = new SchemaProperty(
                name: 'test',
                type: 'string',
                required: true,
                enum: ['red', 'green', 'blue'],
            );

            $result = $this->service->validateProperty('green', $property, 'test.path');
            expect($result)->toBe('green');
        });

        test('throws exception for invalid enum value', function (): void {
            $property = new SchemaProperty(
                name: 'test',
                type: 'string',
                required: true,
                enum: ['red', 'green', 'blue'],
            );

            $this->service->validateProperty('yellow', $property, 'test.path');
        })->throws(SerializationException::class, 'Invalid item type');

        test('throws exception for type mismatch', function (): void {
            $property = new SchemaProperty(
                name: 'test',
                type: 'integer',
                required: true,
            );

            $this->service->validateProperty('not an integer', $property, 'test.path');
        })->throws(SerializationException::class, 'Invalid item type');

        test('validates null values', function (): void {
            $property = new SchemaProperty(
                name: 'test',
                type: 'null',
                required: true,
            );

            $result = $this->service->validateProperty(null, $property, 'test.path');
            expect($result)->toBeNull();
        });

        test('validates object properties', function (): void {
            $property = new SchemaProperty(
                name: 'test',
                type: 'object',
                required: true,
            );

            // Test with stdClass object
            $obj = new stdClass;
            $result = $this->service->validateProperty($obj, $property, 'test.path');
            expect($result)->toBe($obj);

            // Test with associative array
            $array = ['key' => 'value'];
            $result = $this->service->validateProperty($array, $property, 'test.path');
            expect($result)->toBe($array);

            // Test with empty array (valid for empty object)
            $empty = [];
            $result = $this->service->validateProperty($empty, $property, 'test.path');
            expect($result)->toBe($empty);
        });

        test('validates number properties', function (): void {
            $property = new SchemaProperty(
                name: 'test',
                type: 'number',
                required: true,
            );

            // Test with float
            $result = $this->service->validateProperty(3.14, $property, 'test.path');
            expect($result)->toBe(3.14);

            // Test with integer (valid for number type)
            $result = $this->service->validateProperty(42, $property, 'test.path');
            expect($result)->toBe(42);
        });
    });

    describe('registerSchema()', function (): void {
        test('registers a schema', function (): void {
            $schema = test()->createMock(SchemaInterface::class);
            $schema->method('getClassName')->willReturn('TestClass');

            $result = $this->service->registerSchema($schema);

            expect($result)->toBe($this->service);
            expect($this->service->hasSchema('TestClass'))->toBeTrue();
        });

        test('allows method chaining', function (): void {
            $schema1 = test()->createMock(SchemaInterface::class);
            $schema1->method('getClassName')->willReturn('Class1');

            $schema2 = test()->createMock(SchemaInterface::class);
            $schema2->method('getClassName')->willReturn('Class2');

            $this->service
                ->registerSchema($schema1)
                ->registerSchema($schema2);

            expect($this->service->hasSchema('Class1'))->toBeTrue();
            expect($this->service->hasSchema('Class2'))->toBeTrue();
        });
    });

    describe('hasSchema()', function (): void {
        test('returns true for registered schema', function (): void {
            $schema = test()->createMock(SchemaInterface::class);
            $schema->method('getClassName')->willReturn('TestClass');

            $this->service->registerSchema($schema);

            expect($this->service->hasSchema('TestClass'))->toBeTrue();
        });

        test('returns false for unregistered schema', function (): void {
            expect($this->service->hasSchema('UnknownClass'))->toBeFalse();
        });
    });
});
