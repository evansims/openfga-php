<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\ConditionParameters;
use OpenFGA\Models\{Condition, ConditionParameter, TupleKey, TupleKeyInterface};
use OpenFGA\Schema\SchemaInterface;

describe('TupleKey Model', function (): void {
    test('implements TupleKeyInterface', function (): void {
        $tupleKey = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        expect($tupleKey)->toBeInstanceOf(TupleKeyInterface::class);
    });

    test('constructs with required parameters only', function (): void {
        $tupleKey = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        expect($tupleKey->getUser())->toBe('user:anne');
        expect($tupleKey->getRelation())->toBe('viewer');
        expect($tupleKey->getObject())->toBe('document:roadmap');
        expect($tupleKey->getCondition())->toBeNull();
    });

    test('constructs with condition', function (): void {
        $param = new ConditionParameter(name: 'region', value: 'string');
        $params = new ConditionParameters([$param]);
        $condition = new Condition(name: 'inRegion', expression: 'params.region == "us-east"', parameters: $params);

        $tupleKey = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
            condition: $condition,
        );

        expect($tupleKey->getUser())->toBe('user:anne');
        expect($tupleKey->getRelation())->toBe('viewer');
        expect($tupleKey->getObject())->toBe('document:roadmap');
        expect($tupleKey->getCondition())->toBe($condition);
    });

    test('serializes to JSON without condition', function (): void {
        $tupleKey = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $json = $tupleKey->jsonSerialize();

        expect($json)->toBe([
            'user' => 'user:anne',
            'relation' => 'viewer',
            'object' => 'document:roadmap',
        ]);
        expect($json)->not->toHaveKey('condition');
    });

    test('serializes to JSON with condition', function (): void {
        $param = new ConditionParameter(name: 'region', value: 'string');
        $params = new ConditionParameters([$param]);
        $condition = new Condition(name: 'inRegion', expression: 'params.region == "us-east"', parameters: $params);

        $tupleKey = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
            condition: $condition,
        );

        $json = $tupleKey->jsonSerialize();

        expect($json)->toHaveKeys(['user', 'relation', 'object', 'condition']);
        expect($json['condition'])->toBe($condition->jsonSerialize());
    });

    test('handles wildcard users', function (): void {
        $tupleKey = new TupleKey(
            user: 'user:*',
            relation: 'viewer',
            object: 'document:public',
        );

        expect($tupleKey->getUser())->toBe('user:*');
    });

    test('handles userset references', function (): void {
        $tupleKey = new TupleKey(
            user: 'group:engineering#member',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        expect($tupleKey->getUser())->toBe('group:engineering#member');
    });

    test('handles complex object identifiers', function (): void {
        $tupleKey = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:2023/Q4/financial-report.pdf',
        );

        expect($tupleKey->getObject())->toBe('document:2023/Q4/financial-report.pdf');
    });

    test('returns schema instance', function (): void {
        $schema = TupleKey::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(TupleKey::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(4);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['user', 'relation', 'object', 'condition']);
    });

    test('schema properties have correct types', function (): void {
        $schema = TupleKey::schema();
        $properties = $schema->getProperties();

        $userProp = $properties[array_keys($properties)[0]];
        expect($userProp->name)->toBe('user');
        expect($userProp->type)->toBe('string');
        expect($userProp->required)->toBe(true);

        $relationProp = $properties[array_keys($properties)[1]];
        expect($relationProp->name)->toBe('relation');
        expect($relationProp->type)->toBe('string');
        expect($relationProp->required)->toBe(true);

        $objectProp = $properties[array_keys($properties)[2]];
        expect($objectProp->name)->toBe('object');
        expect($objectProp->type)->toBe('string');
        expect($objectProp->required)->toBe(true);

        $conditionProp = $properties[array_keys($properties)[3]];
        expect($conditionProp->name)->toBe('condition');
        expect($conditionProp->type)->toBe(Condition::class);
        expect($conditionProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = TupleKey::schema();
        $schema2 = TupleKey::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles empty strings', function (): void {
        $tupleKey = new TupleKey(
            user: '',
            relation: '',
            object: '',
        );

        expect($tupleKey->getUser())->toBe('');
        expect($tupleKey->getRelation())->toBe('');
        expect($tupleKey->getObject())->toBe('');
    });

    test('preserves whitespace in values', function (): void {
        $tupleKey = new TupleKey(
            user: '  user:anne  ',
            relation: '  viewer  ',
            object: '  document:roadmap  ',
        );

        expect($tupleKey->getUser())->toBe('  user:anne  ');
        expect($tupleKey->getRelation())->toBe('  viewer  ');
        expect($tupleKey->getObject())->toBe('  document:roadmap  ');
    });
});
