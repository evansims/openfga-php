<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use DateTimeImmutable;
use OpenFGA\Models\Collections\{Tuples, TuplesInterface};
use OpenFGA\Models\{Condition, Tuple, TupleKey};
use OpenFGA\Schemas\{CollectionSchemaInterface, SchemaInterface};

describe('Tuples Collection', function (): void {
    test('implements interface', function (): void {
        $collection = new Tuples;

        expect($collection)->toBeInstanceOf(TuplesInterface::class);
    });

    test('creates empty', function (): void {
        $collection = new Tuples;

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('creates with array of tuples', function (): void {
        $tuple1 = new Tuple(
            key: new TupleKey(
                user: 'user:alice',
                relation: 'viewer',
                object: 'document:budget',
            ),
            timestamp: new DateTimeImmutable,
        );

        $tuple2 = new Tuple(
            key: new TupleKey(
                user: 'user:bob',
                relation: 'editor',
                object: 'document:report',
            ),
            timestamp: new DateTimeImmutable,
        );

        $tuple3 = new Tuple(
            key: new TupleKey(
                user: 'group:engineering#member',
                relation: 'owner',
                object: 'folder:shared',
            ),
            timestamp: new DateTimeImmutable,
        );

        $collection = new Tuples([$tuple1, $tuple2, $tuple3]);

        expect($collection->count())->toBe(3);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds tuples', function (): void {
        $collection = new Tuples;

        $tuple = new Tuple(
            key: new TupleKey(
                user: 'user:charlie',
                relation: 'admin',
                object: 'system:main',
            ),
            timestamp: new DateTimeImmutable,
        );

        $collection->add($tuple);

        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($tuple);
    });

    test('checks if tuple exists', function (): void {
        $tuple = new Tuple(
            key: new TupleKey(
                user: 'user:test',
                relation: 'member',
                object: 'group:admins',
            ),
            timestamp: new DateTimeImmutable,
        );

        $collection = new Tuples([$tuple]);

        expect(isset($collection[0]))->toBeTrue();
        expect(isset($collection[1]))->toBeFalse();
    });

    test('iterates over tuples', function (): void {
        $tuple1 = new Tuple(
            key: new TupleKey(user: 'user:1', relation: 'read', object: 'doc:1'),
            timestamp: new DateTimeImmutable,
        );
        $tuple2 = new Tuple(
            key: new TupleKey(user: 'user:2', relation: 'write', object: 'doc:2'),
            timestamp: new DateTimeImmutable,
        );
        $tuple3 = new Tuple(
            key: new TupleKey(user: 'user:3', relation: 'delete', object: 'doc:3'),
            timestamp: new DateTimeImmutable,
        );

        $collection = new Tuples([$tuple1, $tuple2, $tuple3]);

        $relations = [];

        foreach ($collection as $tuple) {
            $relations[] = $tuple->getKey()->getRelation();
        }

        expect($relations)->toBe(['read', 'write', 'delete']);
    });

    test('toArray', function (): void {
        $tuple1 = new Tuple(
            key: new TupleKey(user: 'user:a', relation: 'viewer', object: 'file:a'),
            timestamp: new DateTimeImmutable,
        );
        $tuple2 = new Tuple(
            key: new TupleKey(user: 'user:b', relation: 'editor', object: 'file:b'),
            timestamp: new DateTimeImmutable,
        );

        $collection = new Tuples([$tuple1, $tuple2]);
        $array = $collection->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array[0])->toBe($tuple1);
        expect($array[1])->toBe($tuple2);
    });

    test('jsonSerialize', function (): void {
        $timestamp = new DateTimeImmutable('2024-01-15 10:00:00');

        $tuple1 = new Tuple(
            key: new TupleKey(
                user: 'user:alice',
                relation: 'viewer',
                object: 'document:report',
            ),
            timestamp: new DateTimeImmutable,
        );

        $tuple2 = new Tuple(
            key: new TupleKey(
                user: 'group:admins#member',
                relation: 'editor',
                object: 'document:budget',
                condition: new Condition(name: 'in_business_hours', expression: 'request.time >= "09:00" && request.time <= "17:00"'),
            ),
            timestamp: $timestamp,
        );

        $collection = new Tuples([$tuple1, $tuple2]);
        $json = $collection->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveCount(2);

        expect($json[0])->toHaveKey('key');
        expect($json[0]['key'])->toBe([
            'user' => 'user:alice',
            'relation' => 'viewer',
            'object' => 'document:report',
        ]);

        expect($json[1])->toHaveKey('key');
        expect($json[1])->toHaveKey('timestamp');
        expect($json[1]['key']['user'])->toBe('group:admins#member');
        expect($json[1]['key']['relation'])->toBe('editor');
        expect($json[1]['key']['object'])->toBe('document:budget');
        expect($json[1]['key']['condition'])->toHaveKey('name');
        expect($json[1]['key']['condition']['name'])->toBe('in_business_hours');
    });

    test('schema', function (): void {
        $schema = Tuples::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(Tuples::class);
    });

    test('schema is cached', function (): void {
        $schema1 = Tuples::schema();
        $schema2 = Tuples::schema();

        expect($schema1)->toBe($schema2);
        expect($schema1)->toBeInstanceOf(CollectionSchemaInterface::class);
    });

    test('filters tuples by user', function (): void {
        $collection = new Tuples([
            new Tuple(key: new TupleKey(user: 'user:alice', relation: 'viewer', object: 'doc:1'), timestamp: new DateTimeImmutable),
            new Tuple(key: new TupleKey(user: 'user:bob', relation: 'editor', object: 'doc:2'), timestamp: new DateTimeImmutable),
            new Tuple(key: new TupleKey(user: 'user:alice', relation: 'owner', object: 'doc:3'), timestamp: new DateTimeImmutable),
            new Tuple(key: new TupleKey(user: 'group:eng#member', relation: 'viewer', object: 'doc:4'), timestamp: new DateTimeImmutable),
            new Tuple(key: new TupleKey(user: 'user:alice', relation: 'viewer', object: 'doc:5'), timestamp: new DateTimeImmutable),
        ]);

        // Filter tuples for user:alice
        $aliceTuples = [];

        foreach ($collection as $tuple) {
            if ('user:alice' === $tuple->getKey()->getUser()) {
                $aliceTuples[] = $tuple->getKey()->getObject();
            }
        }

        expect($aliceTuples)->toBe(['doc:1', 'doc:3', 'doc:5']);
    });

    test('groups tuples by relation', function (): void {
        $collection = new Tuples([
            new Tuple(key: new TupleKey(user: 'user:1', relation: 'viewer', object: 'doc:1'), timestamp: new DateTimeImmutable),
            new Tuple(key: new TupleKey(user: 'user:2', relation: 'editor', object: 'doc:2'), timestamp: new DateTimeImmutable),
            new Tuple(key: new TupleKey(user: 'user:3', relation: 'viewer', object: 'doc:3'), timestamp: new DateTimeImmutable),
            new Tuple(key: new TupleKey(user: 'user:4', relation: 'owner', object: 'doc:4'), timestamp: new DateTimeImmutable),
            new Tuple(key: new TupleKey(user: 'user:5', relation: 'viewer', object: 'doc:5'), timestamp: new DateTimeImmutable),
        ]);

        // Group by relation
        $byRelation = [];

        foreach ($collection as $tuple) {
            $relation = $tuple->getKey()->getRelation();

            if (! isset($byRelation[$relation])) {
                $byRelation[$relation] = [];
            }
            $byRelation[$relation][] = $tuple->getKey()->getUser();
        }

        expect($byRelation)->toHaveKey('viewer');
        expect($byRelation['viewer'])->toBe(['user:1', 'user:3', 'user:5']);
        expect($byRelation['editor'])->toBe(['user:2']);
        expect($byRelation['owner'])->toBe(['user:4']);
    });

    test('handles conditional tuples', function (): void {
        $collection = new Tuples([
            new Tuple(
                key: new TupleKey(
                    user: 'user:alice',
                    relation: 'viewer',
                    object: 'doc:sensitive',
                    condition: new Condition(name: 'in_business_hours', expression: 'request.time >= "09:00" && request.time <= "17:00"'),
                ),
                timestamp: new DateTimeImmutable,
            ),
            new Tuple(
                key: new TupleKey(
                    user: 'user:bob',
                    relation: 'editor',
                    object: 'doc:public',
                ),
                timestamp: new DateTimeImmutable,
            ),
            new Tuple(
                key: new TupleKey(
                    user: 'user:charlie',
                    relation: 'owner',
                    object: 'doc:restricted',
                    condition: new Condition(name: 'is_verified', expression: 'user.verified == true'),
                ),
                timestamp: new DateTimeImmutable,
            ),
        ]);

        // Find conditional tuples
        $conditionalTuples = [];

        foreach ($collection as $tuple) {
            if (null !== $tuple->getKey()->getCondition()) {
                $conditionalTuples[] = [
                    'user' => $tuple->getKey()->getUser(),
                    'condition' => $tuple->getKey()->getCondition()->getName(),
                ];
            }
        }

        expect($conditionalTuples)->toHaveCount(2);
        expect($conditionalTuples[0])->toBe([
            'user' => 'user:alice',
            'condition' => 'in_business_hours',
        ]);
        expect($conditionalTuples[1])->toBe([
            'user' => 'user:charlie',
            'condition' => 'is_verified',
        ]);
    });

    test('represents permission relationships', function (): void {
        // Simulate a document with various permission levels
        $docId = 'document:quarterly-report';

        $collection = new Tuples([
            // Direct user permissions
            new Tuple(key: new TupleKey(user: 'user:ceo', relation: 'owner', object: $docId), timestamp: new DateTimeImmutable),
            new Tuple(key: new TupleKey(user: 'user:cfo', relation: 'editor', object: $docId), timestamp: new DateTimeImmutable),
            new Tuple(key: new TupleKey(user: 'user:analyst1', relation: 'viewer', object: $docId), timestamp: new DateTimeImmutable),
            new Tuple(key: new TupleKey(user: 'user:analyst2', relation: 'viewer', object: $docId), timestamp: new DateTimeImmutable),

            // Group-based permissions
            new Tuple(key: new TupleKey(user: 'group:finance#member', relation: 'viewer', object: $docId), timestamp: new DateTimeImmutable),
            new Tuple(key: new TupleKey(user: 'group:executives#member', relation: 'editor', object: $docId), timestamp: new DateTimeImmutable),

            // Conditional permission
            new Tuple(key: new TupleKey(
                user: 'user:contractor',
                relation: 'viewer',
                object: $docId,
                condition: new Condition(name: 'during_contract_period', expression: 'request.time >= contract.start && request.time <= contract.end'),
            ), timestamp: new DateTimeImmutable),
        ]);

        expect($collection->count())->toBe(7);

        // Count permission levels
        $permissionCounts = ['owner' => 0, 'editor' => 0, 'viewer' => 0];

        foreach ($collection as $tuple) {
            $relation = $tuple->getKey()->getRelation();
            ++$permissionCounts[$relation];
        }

        expect($permissionCounts)->toBe([
            'owner' => 1,
            'editor' => 2,
            'viewer' => 4,
        ]);
    });

    test('handles empty collection edge cases', function (): void {
        $collection = new Tuples;

        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);

        // Test iteration on empty collection
        $count = 0;

        foreach ($collection as $_) {
            ++$count;
        }
        expect($count)->toBe(0);

        // Test get on empty collection
        expect($collection->get(0))->toBeNull();
    });
});
