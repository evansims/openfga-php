<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use OpenFGA\Models\Collections\{ConditionParameters, Conditions, ConditionsInterface};
use OpenFGA\Models\{Condition, ConditionMetadata, ConditionParameter, SourceInfo};
use OpenFGA\Models\Enums\TypeName;
use OpenFGA\Schemas\{CollectionSchemaInterface, SchemaInterface};

describe('Conditions Collection', function (): void {
    test('implements interface', function (): void {
        $collection = new Conditions;

        expect($collection)->toBeInstanceOf(ConditionsInterface::class);
    });

    test('creates empty', function (): void {
        $collection = new Conditions;

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('creates with array of conditions', function (): void {
        $condition1 = new Condition(
            name: 'condition1',
            expression: 'user.id == params.allowed_user',
            parameters: new ConditionParameters([
                new ConditionParameter(
                    typeName: TypeName::STRING,
                    genericTypes: new ConditionParameters,
                ),
            ]),
        );

        $condition2 = new Condition(
            name: 'condition2',
            expression: 'user.department == params.department',
            parameters: new ConditionParameters([
                new ConditionParameter(typeName: TypeName::STRING),
            ]),
        );

        $collection = new Conditions([$condition1, $condition2]);

        expect($collection->count())->toBe(2);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds conditions', function (): void {
        $collection = new Conditions;

        $condition = new Condition(
            name: 'is_owner',
            expression: 'user.id == resource.owner_id',
        );

        $collection->add($condition);

        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($condition);
    });

    test('checks if condition exists at index', function (): void {
        $condition = new Condition(
            name: 'time_based',
            expression: 'request.timestamp < params.expiry',
            parameters: new ConditionParameters([
                new ConditionParameter(typeName: TypeName::TIMESTAMP),
            ]),
        );

        $collection = new Conditions([$condition]);

        expect(isset($collection[0]))->toBeTrue();
        expect(isset($collection[1]))->toBeFalse();
    });

    test('iterates over conditions', function (): void {
        $condition1 = new Condition(name: 'cond1', expression: 'expr1');
        $condition2 = new Condition(name: 'cond2', expression: 'expr2');
        $condition3 = new Condition(name: 'cond3', expression: 'expr3');

        $collection = new Conditions([$condition1, $condition2, $condition3]);

        $names = [];
        $indices = [];

        foreach ($collection as $index => $condition) {
            $indices[] = $index;
            $names[] = $condition->getName();
        }

        expect($indices)->toBe([0, 1, 2]);
        expect($names)->toBe(['cond1', 'cond2', 'cond3']);
    });

    test('toArray', function (): void {
        $condition1 = new Condition(name: 'cond1', expression: 'expr1');
        $condition2 = new Condition(name: 'cond2', expression: 'expr2');

        $collection = new Conditions([$condition1, $condition2]);

        $array = $collection->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array[0])->toBe($condition1);
        expect($array[1])->toBe($condition2);
    });

    test('jsonSerialize', function (): void {
        $condition = new Condition(
            name: 'ip_check',
            expression: 'user.ip_address in params.allowed_ips',
            parameters: new ConditionParameters([
                new ConditionParameter(
                    typeName: TypeName::LIST,
                    genericTypes: new ConditionParameters([
                        new ConditionParameter(typeName: TypeName::STRING),
                    ]),
                ),
            ]),
        );

        $collection = new Conditions([$condition]);
        $json = $collection->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveCount(1);
        expect($json[0])->toHaveKey('name');
        expect($json[0]['name'])->toBe('ip_check');
    });

    test('schema', function (): void {
        $schema = Conditions::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(Conditions::class);
    });

    test('schema is cached', function (): void {
        $schema1 = Conditions::schema();
        $schema2 = Conditions::schema();

        expect($schema1)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema2)->toBeInstanceOf(CollectionSchemaInterface::class);
    });

    test('handles complex authorization conditions', function (): void {
        $conditions = new Conditions([
            new Condition(
                name: 'business_hours',
                expression: 'request.hour >= 9 && request.hour < 17',
            ),
            new Condition(
                name: 'geo_restriction',
                expression: 'user.country in params.allowed_countries',
                parameters: new ConditionParameters([
                    new ConditionParameter(
                        typeName: TypeName::LIST,
                        genericTypes: new ConditionParameters([
                            new ConditionParameter(typeName: TypeName::STRING),
                        ]),
                    ),
                ]),
            ),
            new Condition(
                name: 'same_department',
                expression: 'user.department == resource.department',
            ),
            new Condition(
                name: 'admin_or_owner',
                expression: 'user.role == "admin" || user.id == resource.owner_id',
            ),
        ]);

        expect($conditions->count())->toBe(4);

        // Check conditions by name
        $conditionNames = [];

        foreach ($conditions as $condition) {
            $conditionNames[] = $condition->getName();
        }

        expect($conditionNames)->toBe([
            'business_hours',
            'geo_restriction',
            'same_department',
            'admin_or_owner',
        ]);
    });

    test('supports condition metadata', function (): void {
        $sourceInfo = new SourceInfo(file: 'policy.yaml');
        $metadata = new ConditionMetadata(
            module: 'auth_conditions',
            sourceInfo: $sourceInfo,
        );

        $condition = new Condition(
            name: 'premium_feature',
            expression: 'user.subscription_level == "premium"',
            metadata: $metadata,
        );

        $collection = new Conditions([$condition]);

        $retrieved = $collection->get(0);
        expect($retrieved->getMetadata())->toBe($metadata);
        expect($retrieved->getMetadata()->getModule())->toBe('auth_conditions');
    });

    test('filters conditions by parameter types', function (): void {
        $collection = new Conditions([
            new Condition(
                name: 'string_check',
                expression: 'user.name == params.expected_name',
                parameters: new ConditionParameters([
                    new ConditionParameter(typeName: TypeName::STRING),
                ]),
            ),
            new Condition(
                name: 'age_check',
                expression: 'user.age >= params.min_age',
                parameters: new ConditionParameters([
                    new ConditionParameter(typeName: TypeName::INT),
                ]),
            ),
            new Condition(
                name: 'always_true',
                expression: 'true',
            ),
        ]);

        // Find conditions with string parameters
        $withStringParams = [];

        foreach ($collection as $index => $condition) {
            if (null !== $condition->getParameters()) {
                foreach ($condition->getParameters() as $param) {
                    if (TypeName::STRING === $param->getTypeName()) {
                        $withStringParams[] = $index;

                        break;
                    }
                }
            }
        }

        expect($withStringParams)->toBe([0]);
    });

    test('handles empty collection edge cases', function (): void {
        $collection = new Conditions;

        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);

        // Test iteration on empty collection
        $count = 0;

        foreach ($collection as $item) {
            ++$count;
        }
        expect($count)->toBe(0);

        // Test get on empty collection
        expect($collection->get(0))->toBeNull();
    });
});
