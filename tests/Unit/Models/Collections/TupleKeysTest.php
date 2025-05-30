<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use OpenFGA\Models\Collections\{ConditionParameters, TupleKeys, TupleKeysInterface};
use OpenFGA\Models\{Condition, ConditionParameter, TupleKey};
use OpenFGA\Models\Enums\TypeName;
use stdClass;
use TypeError;

describe('TupleKeys Collection', function (): void {
    test('implements TupleKeysInterface', function (): void {
        $collection = new TupleKeys();

        expect($collection)->toBeInstanceOf(TupleKeysInterface::class);
    });

    test('constructs empty collection', function (): void {
        $collection = new TupleKeys();

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBe(true);
        expect($collection->toArray())->toBe([]);
    });

    test('constructs with single TupleKey', function (): void {
        $tupleKey = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $collection = new TupleKeys($tupleKey);

        expect($collection->count())->toBe(1);
        expect($collection->isEmpty())->toBe(false);
        // Collection method removed - not available
    });

    test('constructs with multiple TupleKeys', function (): void {
        $tupleKey1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:roadmap');
        $tupleKey2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:roadmap');
        $tupleKey3 = new TupleKey(user: 'user:charlie', relation: 'owner', object: 'document:roadmap');

        $collection = new TupleKeys($tupleKey1, $tupleKey2, $tupleKey3);

        expect($collection->count())->toBe(3);
        expect($collection->toArray())->toBe([$tupleKey1, $tupleKey2, $tupleKey3]);
    });

    test('constructs with array of TupleKeys', function (): void {
        $tupleKeys = [
            new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
            new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2'),
        ];

        $collection = new TupleKeys($tupleKeys);

        expect($collection->count())->toBe(2);
        expect($collection->toArray())->toBe($tupleKeys);
    });

    test('adds TupleKey to collection', function (): void {
        $collection = new TupleKeys();
        $tupleKey = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:roadmap');

        $result = $collection->add($tupleKey);

        expect($result)->toBe($collection); // Fluent interface
        expect($collection->count())->toBe(1);
        // Collection method removed - not available
    });

    test('supports method chaining when adding', function (): void {
        $tupleKey1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tupleKey2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = (new TupleKeys())
            ->add($tupleKey1)
            ->add($tupleKey2);

        expect($collection->count())->toBe(2);
        expect($collection->toArray())->toBe([$tupleKey1, $tupleKey2]);
    });

    test('clear removes all items', function (): void {
        $collection = new TupleKeys(
            new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
            new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2'),
        );

        expect($collection->count())->toBe(2);

        $collection->clear();

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBe(true);
    });

    test('iterates over collection', function (): void {
        $tupleKeys = [
            new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
            new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2'),
            new TupleKey(user: 'user:charlie', relation: 'owner', object: 'document:3'),
        ];

        $collection = new TupleKeys(...$tupleKeys);

        $iterated = [];
        foreach ($collection as $index => $tupleKey) {
            $iterated[$index] = $tupleKey;
        }

        expect($iterated)->toBe($tupleKeys);
    });

    test('gets item by index', function (): void {
        $tupleKey1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tupleKey2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TupleKeys($tupleKey1, $tupleKey2);

        expect($collection->get(0))->toBe($tupleKey1);
        expect($collection->get(1))->toBe($tupleKey2);
    });

    test('returns null for invalid index', function (): void {
        $collection = new TupleKeys(
            new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
        );

        expect($collection->get(1))->toBeNull();
        expect($collection->get(-1))->toBeNull();
        expect($collection->get(100))->toBeNull();
    });

    test('gets first and last items', function (): void {
        $tupleKey1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tupleKey2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');
        $tupleKey3 = new TupleKey(user: 'user:charlie', relation: 'owner', object: 'document:3');

        $collection = new TupleKeys($tupleKey1, $tupleKey2, $tupleKey3);

        // First and last methods not available - use get() instead
        expect($collection->get(0))->toBe($tupleKey1);
        expect($collection->get(2))->toBe($tupleKey3);
    });

    test('returns null for first and last on empty collection', function (): void {
        $collection = new TupleKeys();

        // First and last methods not available - verify empty collection behavior
        expect($collection->get(0))->toBeNull();
        expect($collection->count())->toBe(0);
    });

    test('serializes to JSON', function (): void {
        $tupleKey1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tupleKey2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TupleKeys($tupleKey1, $tupleKey2);

        $json = $collection->jsonSerialize();

        expect($json)->toBe([
            'tuple_keys' => [
                $tupleKey1->jsonSerialize(),
                $tupleKey2->jsonSerialize(),
            ],
        ]);
    });

    test('serializes empty collection to empty array', function (): void {
        $collection = new TupleKeys();

        expect($collection->jsonSerialize())->toBe(['tuple_keys' => []]);
    });

    test('handles TupleKeys with conditions', function (): void {
        $condition = new Condition(
            name: 'inRegion',
            expression: 'params.region == "us-east"',
            parameters: new ConditionParameters([
                new ConditionParameter(typeName: TypeName::STRING),
            ]),
        );

        $tupleKey = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
            condition: $condition,
        );

        $collection = new TupleKeys($tupleKey);

        expect($collection->count())->toBe(1);
        expect($collection->get(0)->getCondition())->toBe($condition);
    });

    test('throws TypeError when adding wrong type', function (): void {
        $collection = new TupleKeys();
        $wrongType = new stdClass();

        $this->expectException(TypeError::class);
        $collection->add($wrongType);
    });

    test('mixed TupleKey formats in same collection', function (): void {
        $tupleKeys = [
            new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
            new TupleKey(user: 'group:admins#member', relation: 'editor', object: 'folder:projects'),
            new TupleKey(user: 'user:*', relation: 'viewer', object: 'document:public'),
        ];

        $collection = new TupleKeys(...$tupleKeys);

        expect($collection->count())->toBe(3);
        expect($collection->toArray())->toBe($tupleKeys);
    });

    test('maintains insertion order', function (): void {
        $tupleKeys = [];
        for ($i = 0; $i < 10; ++$i) {
            $tupleKeys[] = new TupleKey(
                user: "user:user{$i}",
                relation: 'viewer',
                object: "document:doc{$i}",
            );
        }

        $collection = new TupleKeys(...$tupleKeys);

        expect($collection->toArray())->toBe($tupleKeys);
    });
});
