<?php

declare(strict_types=1);

use OpenFGA\Models\{TupleChange, TupleKey};
use OpenFGA\Models\Collections\{TupleChanges, TupleChangesInterface};
use OpenFGA\Models\Enums\TupleOperation;
use OpenFGA\Schema\{SchemaInterface, CollectionSchemaInterface};

describe('TupleChanges Collection', function (): void {
    test('implements TupleChangesInterface', function (): void {
        $collection = new TupleChanges();

        expect($collection)->toBeInstanceOf(TupleChangesInterface::class);
    });

    test('constructs with empty array', function (): void {
        $collection = new TupleChanges();

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('constructs with array of tuple changes', function (): void {
        $now = new DateTimeImmutable();
        
        $change1 = new TupleChange(
            tupleKey: new TupleKey(
                user: 'user:alice',
                relation: 'viewer',
                object: 'document:budget',
            ),
            operation: TupleOperation::TUPLE_OPERATION_WRITE,
            timestamp: $now->modify('-5 minutes'),
        );
        
        $change2 = new TupleChange(
            tupleKey: new TupleKey(
                user: 'user:bob',
                relation: 'editor',
                object: 'document:report',
            ),
            operation: TupleOperation::TUPLE_OPERATION_DELETE,
            timestamp: $now->modify('-2 minutes'),
        );
        
        $collection = new TupleChanges([$change1, $change2]);

        expect($collection->count())->toBe(2);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds tuple changes', function (): void {
        $collection = new TupleChanges();
        
        $change = new TupleChange(
            tupleKey: new TupleKey(
                user: 'user:charlie',
                relation: 'owner',
                object: 'folder:shared',
            ),
            operation: TupleOperation::TUPLE_OPERATION_WRITE,
            timestamp: new DateTimeImmutable(),
        );
        
        $collection->add($change);
        
        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($change);
    });

    test('checks if tuple change exists', function (): void {
        $change = new TupleChange(
            tupleKey: new TupleKey(
                user: 'user:test',
                relation: 'member',
                object: 'group:engineering',
            ),
            operation: TupleOperation::TUPLE_OPERATION_WRITE,
            timestamp: new DateTimeImmutable(),
        );
        
        $collection = new TupleChanges([$change]);
        
        expect(isset($collection[0]))->toBeTrue();
        expect(isset($collection[1]))->toBeFalse();
    });

    test('iterates over tuple changes', function (): void {
        $now = new DateTimeImmutable();
        
        $change1 = new TupleChange(
            tupleKey: new TupleKey(user: 'user:1', relation: 'read', object: 'doc:1'),
            operation: TupleOperation::TUPLE_OPERATION_WRITE,
            timestamp: $now,
        );
        $change2 = new TupleChange(
            tupleKey: new TupleKey(user: 'user:2', relation: 'write', object: 'doc:2'),
            operation: TupleOperation::TUPLE_OPERATION_DELETE,
            timestamp: $now,
        );
        $change3 = new TupleChange(
            tupleKey: new TupleKey(user: 'user:3', relation: 'admin', object: 'doc:3'),
            operation: TupleOperation::TUPLE_OPERATION_WRITE,
            timestamp: $now,
        );
        
        $collection = new TupleChanges([$change1, $change2, $change3]);
        
        $operations = [];
        foreach ($collection as $change) {
            $operations[] = $change->getOperation()->value;
        }
        
        expect($operations)->toBe(['TUPLE_OPERATION_WRITE', 'TUPLE_OPERATION_DELETE', 'TUPLE_OPERATION_WRITE']);
    });

    test('converts to array', function (): void {
        $now = new DateTimeImmutable();
        
        $change1 = new TupleChange(
            tupleKey: new TupleKey(user: 'user:a', relation: 'viewer', object: 'file:a'),
            operation: TupleOperation::TUPLE_OPERATION_WRITE,
            timestamp: $now,
        );
        $change2 = new TupleChange(
            tupleKey: new TupleKey(user: 'user:b', relation: 'editor', object: 'file:b'),
            operation: TupleOperation::TUPLE_OPERATION_DELETE,
            timestamp: $now,
        );
        
        $collection = new TupleChanges([$change1, $change2]);
        $array = $collection->toArray();
        
        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array[0])->toBe($change1);
        expect($array[1])->toBe($change2);
    });

    test('serializes to JSON', function (): void {
        $timestamp = new DateTimeImmutable('2024-01-15 10:00:00');
        
        $change = new TupleChange(
            tupleKey: new TupleKey(
                user: 'user:alice',
                relation: 'viewer',
                object: 'document:report',
            ),
            operation: TupleOperation::TUPLE_OPERATION_WRITE,
            timestamp: $timestamp,
        );
        
        $collection = new TupleChanges([$change]);
        $json = $collection->jsonSerialize();
        
        expect($json)->toBeArray();
        expect($json)->toHaveCount(1);
        expect($json[0])->toBe([
            'tuple_key' => [
                'user' => 'user:alice',
                'relation' => 'viewer',
                'object' => 'document:report',
            ],
            'operation' => 'TUPLE_OPERATION_WRITE',
            'timestamp' => $timestamp->format(DateTimeInterface::ATOM),
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = TupleChanges::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(TupleChanges::class);
    });

    test('schema is cached', function (): void {
        $schema1 = TupleChanges::schema();
        $schema2 = TupleChanges::schema();

        expect($schema1)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema2)->toBeInstanceOf(CollectionSchemaInterface::class);
    });

    test('filters changes by operation', function (): void {
        $now = new DateTimeImmutable();
        
        $collection = new TupleChanges([
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:1', relation: 'viewer', object: 'doc:1'),
                operation: TupleOperation::TUPLE_OPERATION_WRITE,
                timestamp: $now,
            ),
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:2', relation: 'editor', object: 'doc:2'),
                operation: TupleOperation::TUPLE_OPERATION_DELETE,
                timestamp: $now,
            ),
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:3', relation: 'owner', object: 'doc:3'),
                operation: TupleOperation::TUPLE_OPERATION_WRITE,
                timestamp: $now,
            ),
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:4', relation: 'viewer', object: 'doc:4'),
                operation: TupleOperation::TUPLE_OPERATION_DELETE,
                timestamp: $now,
            ),
        ]);
        
        // Filter WRITE operations
        $writes = [];
        foreach ($collection as $change) {
            if ($change->getOperation() === TupleOperation::TUPLE_OPERATION_WRITE) {
                $writes[] = $change->getTupleKey()->getUser();
            }
        }
        
        expect($writes)->toBe(['user:1', 'user:3']);
    });

    test('finds changes by time range', function (): void {
        $baseTime = new DateTimeImmutable('2024-01-15 12:00:00');
        
        $collection = new TupleChanges([
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:early', relation: 'viewer', object: 'doc:1'),
                operation: TupleOperation::TUPLE_OPERATION_WRITE,
                timestamp: $baseTime->modify('-2 hours'),
            ),
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:recent1', relation: 'editor', object: 'doc:2'),
                operation: TupleOperation::TUPLE_OPERATION_WRITE,
                timestamp: $baseTime->modify('-30 minutes'),
            ),
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:recent2', relation: 'owner', object: 'doc:3'),
                operation: TupleOperation::TUPLE_OPERATION_DELETE,
                timestamp: $baseTime->modify('-15 minutes'),
            ),
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:latest', relation: 'viewer', object: 'doc:4'),
                operation: TupleOperation::TUPLE_OPERATION_WRITE,
                timestamp: $baseTime,
            ),
        ]);
        
        // Find changes within last hour
        $oneHourAgo = $baseTime->modify('-1 hour');
        $recentChanges = [];
        
        foreach ($collection as $change) {
            if ($change->getTimestamp() >= $oneHourAgo) {
                $recentChanges[] = $change->getTupleKey()->getUser();
            }
        }
        
        expect($recentChanges)->toBe(['user:recent1', 'user:recent2', 'user:latest']);
    });

    test('groups changes by object', function (): void {
        $now = new DateTimeImmutable();
        
        $collection = new TupleChanges([
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:alice', relation: 'viewer', object: 'doc:report'),
                operation: TupleOperation::TUPLE_OPERATION_WRITE,
                timestamp: $now,
            ),
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:bob', relation: 'editor', object: 'doc:budget'),
                operation: TupleOperation::TUPLE_OPERATION_WRITE,
                timestamp: $now,
            ),
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:charlie', relation: 'owner', object: 'doc:report'),
                operation: TupleOperation::TUPLE_OPERATION_WRITE,
                timestamp: $now,
            ),
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:david', relation: 'viewer', object: 'doc:budget'),
                operation: TupleOperation::TUPLE_OPERATION_DELETE,
                timestamp: $now,
            ),
        ]);
        
        // Group by object
        $byObject = [];
        foreach ($collection as $change) {
            $object = $change->getTupleKey()->getObject();
            if (!isset($byObject[$object])) {
                $byObject[$object] = [];
            }
            $byObject[$object][] = $change->getTupleKey()->getUser();
        }
        
        expect($byObject)->toHaveKey('doc:report');
        expect($byObject['doc:report'])->toBe(['user:alice', 'user:charlie']);
        expect($byObject['doc:budget'])->toBe(['user:bob', 'user:david']);
    });

    test('tracks audit trail', function (): void {
        $startTime = new DateTimeImmutable('2024-01-15 09:00:00');
        
        // Simulate a series of permission changes
        $collection = new TupleChanges([
            // User gains initial access
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:alice', relation: 'viewer', object: 'doc:sensitive'),
                operation: TupleOperation::TUPLE_OPERATION_WRITE,
                timestamp: $startTime,
            ),
            // User upgraded to editor
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:alice', relation: 'viewer', object: 'doc:sensitive'),
                operation: TupleOperation::TUPLE_OPERATION_DELETE,
                timestamp: $startTime->modify('+1 hour'),
            ),
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:alice', relation: 'editor', object: 'doc:sensitive'),
                operation: TupleOperation::TUPLE_OPERATION_WRITE,
                timestamp: $startTime->modify('+1 hour'),
            ),
            // User access revoked
            new TupleChange(
                tupleKey: new TupleKey(user: 'user:alice', relation: 'editor', object: 'doc:sensitive'),
                operation: TupleOperation::TUPLE_OPERATION_DELETE,
                timestamp: $startTime->modify('+3 hours'),
            ),
        ]);
        
        expect($collection->count())->toBe(4);
        
        // Verify chronological order
        $times = [];
        foreach ($collection as $change) {
            $times[] = $change->getTimestamp()->format('H:i');
        }
        
        expect($times)->toBe(['09:00', '10:00', '10:00', '12:00']);
    });

    test('handles empty collection edge cases', function (): void {
        $collection = new TupleChanges();
        
        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);
        
        // Test iteration on empty collection
        $count = 0;
        foreach ($collection as $item) {
            $count++;
        }
        expect($count)->toBe(0);
        
        // Test get on empty collection
        expect($collection->get(0))->toBeNull();
    });
});