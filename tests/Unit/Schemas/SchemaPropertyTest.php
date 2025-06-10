<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Schemas;

use OpenFGA\Schemas\{SchemaProperty, SchemaPropertyInterface};

describe('SchemaProperty', function (): void {
    test('SchemaProperty implements SchemaPropertyInterface', function (): void {
        $property = new SchemaProperty('test', 'string');

        expect($property)->toBeInstanceOf(SchemaPropertyInterface::class);
    });

    test('can be created with required fields only', function (): void {
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
            ->and($property->className)->toBeNull()
            ->and($property->parameterName)->toBeNull();
    });

    test('can be created with all fields', function (): void {
        $property = new SchemaProperty(
            name: 'test',
            type: 'array',
            required: true,
            default: ['default'],
            format: 'datetime',
            enum: ['a', 'b', 'c'],
            items: ['type' => 'string'],
            className: 'TestClass',
            parameterName: 'testParam',
        );

        expect($property->name)->toBe('test')
            ->and($property->type)->toBe('array')
            ->and($property->required)->toBeTrue()
            ->and($property->default)->toBe(['default'])
            ->and($property->format)->toBe('datetime')
            ->and($property->enum)->toBe(['a', 'b', 'c'])
            ->and($property->items)->toBe(['type' => 'string'])
            ->and($property->className)->toBe('TestClass')
            ->and($property->parameterName)->toBe('testParam');
    });

    test('getName() returns property name', function (): void {
        $property = new SchemaProperty('propertyName', 'string');

        expect($property->getName())->toBe('propertyName');
    });

    test('getType() returns property type', function (): void {
        $property = new SchemaProperty('test', 'integer');

        expect($property->getType())->toBe('integer');
    });

    test('isRequired() returns required status', function (): void {
        $requiredProperty = new SchemaProperty('test', 'string', required: true);
        $optionalProperty = new SchemaProperty('test', 'string', required: false);

        expect($requiredProperty->isRequired())->toBeTrue();
        expect($optionalProperty->isRequired())->toBeFalse();
    });

    test('getDefault() returns default value', function (): void {
        $propertyWithDefault = new SchemaProperty('test', 'string', default: 'defaultValue');
        $propertyWithoutDefault = new SchemaProperty('test', 'string');

        expect($propertyWithDefault->getDefault())->toBe('defaultValue');
        expect($propertyWithoutDefault->getDefault())->toBeNull();
    });

    test('getFormat() returns format constraint', function (): void {
        $propertyWithFormat = new SchemaProperty('test', 'string', format: 'date');
        $propertyWithoutFormat = new SchemaProperty('test', 'string');

        expect($propertyWithFormat->getFormat())->toBe('date');
        expect($propertyWithoutFormat->getFormat())->toBeNull();
    });

    test('getEnum() returns enumeration values', function (): void {
        $propertyWithEnum = new SchemaProperty('test', 'string', enum: ['option1', 'option2']);
        $propertyWithoutEnum = new SchemaProperty('test', 'string');

        expect($propertyWithEnum->getEnum())->toBe(['option1', 'option2']);
        expect($propertyWithoutEnum->getEnum())->toBeNull();
    });

    test('getItems() returns array item specification', function (): void {
        $arrayProperty = new SchemaProperty('test', 'array', items: ['type' => 'string', 'className' => 'TestClass']);
        $nonArrayProperty = new SchemaProperty('test', 'string');

        expect($arrayProperty->getItems())->toBe(['type' => 'string', 'className' => 'TestClass']);
        expect($nonArrayProperty->getItems())->toBeNull();
    });

    test('getClassName() returns class name for object types', function (): void {
        $objectProperty = new SchemaProperty('test', 'object', className: 'TestClass');
        $primitiveProperty = new SchemaProperty('test', 'string');

        expect($objectProperty->getClassName())->toBe('TestClass');
        expect($primitiveProperty->getClassName())->toBeNull();
    });

    test('getParameterName() returns alternative parameter name', function (): void {
        $propertyWithParam = new SchemaProperty('test', 'string', parameterName: 'altName');
        $propertyWithoutParam = new SchemaProperty('test', 'string');

        expect($propertyWithParam->getParameterName())->toBe('altName');
        expect($propertyWithoutParam->getParameterName())->toBeNull();
    });

    test('supports various data types', function (): void {
        $stringProperty = new SchemaProperty('str', 'string');
        $intProperty = new SchemaProperty('int', 'integer');
        $boolProperty = new SchemaProperty('bool', 'boolean');
        $arrayProperty = new SchemaProperty('arr', 'array');
        $objectProperty = new SchemaProperty('obj', 'object');

        expect($stringProperty->getType())->toBe('string');
        expect($intProperty->getType())->toBe('integer');
        expect($boolProperty->getType())->toBe('boolean');
        expect($arrayProperty->getType())->toBe('array');
        expect($objectProperty->getType())->toBe('object');
    });

    test('supports different default value types', function (): void {
        $stringDefault = new SchemaProperty('str', 'string', default: 'test');
        $intDefault = new SchemaProperty('int', 'integer', default: 42);
        $boolDefault = new SchemaProperty('bool', 'boolean', default: true);
        $arrayDefault = new SchemaProperty('arr', 'array', default: ['item1', 'item2']);
        $objectDefault = new SchemaProperty('obj', 'object', default: (object) ['key' => 'value']);

        expect($stringDefault->getDefault())->toBe('test');
        expect($intDefault->getDefault())->toBe(42);
        expect($boolDefault->getDefault())->toBeTrue();
        expect($arrayDefault->getDefault())->toBe(['item1', 'item2']);
        expect($objectDefault->getDefault())->toEqual((object) ['key' => 'value']);
    });

    test('supports complex array items configuration', function (): void {
        $arrayProperty = new SchemaProperty(
            'complexArray',
            'array',
            items: [
                'type' => 'object',
                'className' => 'ComplexClass',
            ],
        );

        expect($arrayProperty->getItems())->toBe([
            'type' => 'object',
            'className' => 'ComplexClass',
        ]);
    });

    test('supports enumeration validation', function (): void {
        $enumProperty = new SchemaProperty(
            'status',
            'string',
            enum: ['active', 'inactive', 'pending'],
        );

        expect($enumProperty->getEnum())->toBe(['active', 'inactive', 'pending']);
    });

    test('supports format validation constraints', function (): void {
        $dateProperty = new SchemaProperty('date', 'string', format: 'date');
        $datetimeProperty = new SchemaProperty('datetime', 'string', format: 'datetime');
        $emailProperty = new SchemaProperty('email', 'string', format: 'email');

        expect($dateProperty->getFormat())->toBe('date');
        expect($datetimeProperty->getFormat())->toBe('datetime');
        expect($emailProperty->getFormat())->toBe('email');
    });

    test('readonly properties are immutable', function (): void {
        $property = new SchemaProperty('test', 'string', required: true);

        expect($property->name)->toBe('test');
        expect($property->type)->toBe('string');
        expect($property->required)->toBeTrue();

        // Readonly properties cannot be modified after construction
        expect($property)->toHaveProperty('name', 'test');
        expect($property)->toHaveProperty('type', 'string');
        expect($property)->toHaveProperty('required', true);
    });

    test('complex property configuration example', function (): void {
        $property = new SchemaProperty(
            name: 'userPreferences',
            type: 'array',
            required: false,
            default: [],
            format: null,
            enum: null,
            items: [
                'type' => 'object',
                'className' => 'UserPreference',
            ],
            className: null,
            parameterName: 'preferences',
        );

        expect($property->getName())->toBe('userPreferences');
        expect($property->getType())->toBe('array');
        expect($property->isRequired())->toBeFalse();
        expect($property->getDefault())->toBe([]);
        expect($property->getItems())->toBe(['type' => 'object', 'className' => 'UserPreference']);
        expect($property->getParameterName())->toBe('preferences');
    });
});
