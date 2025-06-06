<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\Collections\{ConditionParameters};
use OpenFGA\Models\{ConditionParameter, ConditionParameterInterface};
use OpenFGA\Models\Enums\TypeName;
use OpenFGA\Schemas\SchemaInterface;

describe('ConditionParameter Model', function (): void {
    test('implements ConditionParameterInterface', function (): void {
        $param = new ConditionParameter(typeName: TypeName::STRING);

        expect($param)->toBeInstanceOf(ConditionParameterInterface::class);
    });

    test('constructs with typeName only', function (): void {
        $param = new ConditionParameter(typeName: TypeName::STRING);

        expect($param->getTypeName())->toBe(TypeName::STRING);
        expect($param->getGenericTypes())->toBeNull();
    });

    test('constructs with genericTypes', function (): void {
        $innerParam = new ConditionParameter(typeName: TypeName::STRING);
        $genericTypes = new ConditionParameters([$innerParam]);

        $param = new ConditionParameter(
            typeName: TypeName::LIST,
            genericTypes: $genericTypes,
        );

        expect($param->getTypeName())->toBe(TypeName::LIST);
        expect($param->getGenericTypes())->toBe($genericTypes);
        expect($param->getGenericTypes()->count())->toBe(1);
    });

    test('handles all type names', function (): void {
        $typeNames = [
            TypeName::ANY,
            TypeName::BOOL,
            TypeName::DOUBLE,
            TypeName::DURATION,
            TypeName::INT,
            TypeName::IPADDRESS,
            TypeName::LIST,
            TypeName::MAP,
            TypeName::STRING,
            TypeName::TIMESTAMP,
            TypeName::UINT,
            TypeName::UNSPECIFIED,
        ];

        foreach ($typeNames as $typeName) {
            $param = new ConditionParameter(typeName: $typeName);
            expect($param->getTypeName())->toBe($typeName);
        }
    });

    test('serializes to JSON without genericTypes', function (): void {
        $param = new ConditionParameter(typeName: TypeName::STRING);

        $json = $param->jsonSerialize();

        expect($json)->toBe([
            'type_name' => 'TYPE_NAME_STRING',
        ]);
    });

    test('serializes to JSON with genericTypes', function (): void {
        $innerParam = new ConditionParameter(typeName: TypeName::STRING);
        $genericTypes = new ConditionParameters([$innerParam]);

        $param = new ConditionParameter(
            typeName: TypeName::LIST,
            genericTypes: $genericTypes,
        );

        $json = $param->jsonSerialize();

        expect($json)->toBe([
            'type_name' => 'TYPE_NAME_LIST',
            'generic_types' => [
                ['type_name' => 'TYPE_NAME_STRING'],
            ],
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = ConditionParameter::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(ConditionParameter::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['type_name', 'generic_types']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = ConditionParameter::schema();
        $properties = $schema->getProperties();

        // TypeName property
        $typeNameProp = $properties['type_name'];
        expect($typeNameProp->name)->toBe('type_name');
        expect($typeNameProp->type)->toBe('object');
        expect($typeNameProp->required)->toBe(true);

        // GenericTypes property
        $genericTypesProp = $properties['generic_types'];
        expect($genericTypesProp->name)->toBe('generic_types');
        expect($genericTypesProp->type)->toBe('object');
        expect($genericTypesProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = ConditionParameter::schema();
        $schema2 = ConditionParameter::schema();

        expect($schema1)->toBe($schema2);
    });

    test('nested generic types', function (): void {
        // Create a nested structure: MAP<STRING, LIST<INT>>
        $intParam = new ConditionParameter(typeName: TypeName::INT);
        $listGenericTypes = new ConditionParameters([$intParam]);
        $listParam = new ConditionParameter(
            typeName: TypeName::LIST,
            genericTypes: $listGenericTypes,
        );

        $stringParam = new ConditionParameter(typeName: TypeName::STRING);
        $mapGenericTypes = new ConditionParameters([$stringParam, $listParam]);
        $mapParam = new ConditionParameter(
            typeName: TypeName::MAP,
            genericTypes: $mapGenericTypes,
        );

        expect($mapParam->getTypeName())->toBe(TypeName::MAP);
        expect($mapParam->getGenericTypes()->count())->toBe(2);

        $genericList = $mapParam->getGenericTypes()->get(1);
        expect($genericList->getTypeName())->toBe(TypeName::LIST);
        expect($genericList->getGenericTypes()->count())->toBe(1);
    });

    test('empty generic types collection', function (): void {
        $genericTypes = new ConditionParameters([]);
        $param = new ConditionParameter(
            typeName: TypeName::LIST,
            genericTypes: $genericTypes,
        );

        expect($param->getGenericTypes())->toBe($genericTypes);
        expect($param->getGenericTypes()->isEmpty())->toBe(true);
    });
});
