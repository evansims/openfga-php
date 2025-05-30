<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use OpenFGA\Exceptions\ClientException;
use OpenFGA\Models\Collections\{ConditionParameters, ConditionParametersInterface};
use OpenFGA\Models\{ConditionParameter};
use OpenFGA\Models\Enums\TypeName;
use OpenFGA\Schema\CollectionSchemaInterface;
use stdClass;

use function count;

describe('ConditionParameters Collection', function (): void {
    test('implements interface', function (): void {
        $collection = new ConditionParameters([]);

        expect($collection)->toBeInstanceOf(ConditionParametersInterface::class);
    });

    test('creates empty', function (): void {
        $collection = new ConditionParameters([]);

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBe(true);
        expect($collection->toArray())->toBe([]);
    });

    test('creates with single parameter', function (): void {
        $param = new ConditionParameter(typeName: TypeName::STRING);
        $collection = new ConditionParameters([$param]);

        expect($collection->count())->toBe(1);
        expect($collection->isEmpty())->toBe(false);
        expect($collection->get(0))->toBe($param);
    });

    test('creates with multiple parameters', function (): void {
        $param1 = new ConditionParameter(typeName: TypeName::STRING);
        $param2 = new ConditionParameter(typeName: TypeName::INT);
        $param3 = new ConditionParameter(typeName: TypeName::BOOL);

        $collection = new ConditionParameters([$param1, $param2, $param3]);

        expect($collection->count())->toBe(3);
        expect($collection->get(0))->toBe($param1);
        expect($collection->get(1))->toBe($param2);
        expect($collection->get(2))->toBe($param3);
    });

    test('adds parameters to collection', function (): void {
        $collection = new ConditionParameters([]);

        $param1 = new ConditionParameter(typeName: TypeName::STRING);
        $collection->add($param1);

        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($param1);

        $param2 = new ConditionParameter(typeName: TypeName::INT);
        $collection->add($param2);

        expect($collection->count())->toBe(2);
        expect($collection->get(1))->toBe($param2);
    });

    test('iterates over collection', function (): void {
        $param1 = new ConditionParameter(typeName: TypeName::STRING);
        $param2 = new ConditionParameter(typeName: TypeName::INT);
        $collection = new ConditionParameters([$param1, $param2]);

        $items = [];
        foreach ($collection as $index => $param) {
            $items[$index] = $param;
        }

        expect($items)->toBe([0 => $param1, 1 => $param2]);
    });

    test('accesses parameters by array notation', function (): void {
        $param1 = new ConditionParameter(typeName: TypeName::STRING);
        $param2 = new ConditionParameter(typeName: TypeName::INT);
        $collection = new ConditionParameters([$param1, $param2]);

        expect($collection[0])->toBe($param1);
        expect($collection[1])->toBe($param2);
        expect(isset($collection[0]))->toBe(true);
        expect(isset($collection[2]))->toBe(false);
    });

    test('returns null for non-existent index', function (): void {
        $param = new ConditionParameter(typeName: TypeName::STRING);
        $collection = new ConditionParameters([$param]);

        expect($collection->get(1))->toBeNull();
        expect($collection[999])->toBeNull();
    });

    test('jsonSerialize', function (): void {
        $param1 = new ConditionParameter(typeName: TypeName::STRING);
        $param2 = new ConditionParameter(typeName: TypeName::INT);
        $collection = new ConditionParameters([$param1, $param2]);

        $json = $collection->jsonSerialize();

        expect($json)->toBe([
            ['type_name' => 'TYPE_NAME_STRING'],
            ['type_name' => 'TYPE_NAME_INT'],
        ]);
    });

    test('serializes empty collection to empty array', function (): void {
        $collection = new ConditionParameters([]);

        expect($collection->jsonSerialize())->toBe([]);
    });

    test('handles parameters with generic types', function (): void {
        $innerParam = new ConditionParameter(typeName: TypeName::STRING);
        $innerCollection = new ConditionParameters([$innerParam]);

        $listParam = new ConditionParameter(
            typeName: TypeName::LIST,
            genericTypes: $innerCollection,
        );

        $collection = new ConditionParameters([$listParam]);

        expect($collection->count())->toBe(1);
        expect($collection->get(0)->getTypeName())->toBe(TypeName::LIST);
        expect($collection->get(0)->getGenericTypes())->toBe($innerCollection);
    });

    test('schema', function (): void {
        $schema = ConditionParameters::schema();

        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(ConditionParameters::class);
        expect($schema->getItemType())->toBe(ConditionParameter::class);
    });

    test('preserves parameter order', function (): void {
        $params = [];
        $types = [
            TypeName::STRING,
            TypeName::INT,
            TypeName::BOOL,
            TypeName::DOUBLE,
            TypeName::TIMESTAMP,
        ];

        foreach ($types as $type) {
            $params[] = new ConditionParameter(typeName: $type);
        }

        $collection = new ConditionParameters($params);

        for ($i = 0; $i < count($types); ++$i) {
            expect($collection->get($i)->getTypeName())->toBe($types[$i]);
        }
    });

    test('toArray', function (): void {
        $param1 = new ConditionParameter(typeName: TypeName::STRING);
        $param2 = new ConditionParameter(typeName: TypeName::INT);
        $collection = new ConditionParameters([$param1, $param2]);

        $array = $collection->toArray();

        expect($array)->toBe([$param1, $param2]);
        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
    });

    test('throws on invalid type', function (): void {
        $collection = new ConditionParameters([]);

        $collection->add(new stdClass);
    })->throws(ClientException::class);

    test('uses first() method', function (): void {
        $param1 = new ConditionParameter(typeName: TypeName::STRING);
        $param2 = new ConditionParameter(typeName: TypeName::INT);
        $param3 = new ConditionParameter(typeName: TypeName::BOOL);

        $collection = new ConditionParameters([$param1, $param2, $param3]);

        expect($collection->first())->toBe($param1);
    });

    test('first() returns null on empty collection', function (): void {
        $collection = new ConditionParameters([]);

        expect($collection->first())->toBeNull();
    });
});
