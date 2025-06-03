<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use InvalidArgumentException;
use OpenFGA\Models\{BatchTupleOperation, BatchTupleOperationInterface};
use OpenFGA\Models\Collections\TupleKeys;

use function OpenFGA\{tuple, tuples};

describe('BatchTupleOperation', function (): void {
    test('implements BatchTupleOperationInterface', function (): void {
        $operation = new BatchTupleOperation;

        expect($operation)->toBeInstanceOf(BatchTupleOperationInterface::class);
    });

    test('creates empty operation by default', function (): void {
        $operation = new BatchTupleOperation;

        expect($operation->isEmpty())->toBeTrue();
        expect($operation->getTotalOperations())->toBe(0);
        expect($operation->getWrites())->toBeNull();
        expect($operation->getDeletes())->toBeNull();
    });

    test('creates operation with writes only', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
        );

        $operation = new BatchTupleOperation(writes: $writes);

        expect($operation->isEmpty())->toBeFalse();
        expect($operation->getTotalOperations())->toBe(2);
        expect($operation->getWrites())->toBe($writes);
        expect($operation->getDeletes())->toBeNull();
    });

    test('creates operation with deletes only', function (): void {
        $deletes = tuples(
            tuple('user:charlie', 'reader', 'document:3'),
            tuple('user:dave', 'reader', 'document:4'),
        );

        $operation = new BatchTupleOperation(deletes: $deletes);

        expect($operation->isEmpty())->toBeFalse();
        expect($operation->getTotalOperations())->toBe(2);
        expect($operation->getWrites())->toBeNull();
        expect($operation->getDeletes())->toBe($deletes);
    });

    test('creates operation with both writes and deletes', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
        );

        $deletes = tuples(
            tuple('user:charlie', 'reader', 'document:3'),
        );

        $operation = new BatchTupleOperation(writes: $writes, deletes: $deletes);

        expect($operation->isEmpty())->toBeFalse();
        expect($operation->getTotalOperations())->toBe(3);
        expect($operation->getWrites())->toBe($writes);
        expect($operation->getDeletes())->toBe($deletes);
    });

    test('chunks operation into smaller batches', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
            tuple('user:charlie', 'reader', 'document:3'),
            tuple('user:dave', 'reader', 'document:4'),
            tuple('user:eve', 'reader', 'document:5'),
        );

        $operation = new BatchTupleOperation(writes: $writes);
        $chunks = $operation->chunk(2);

        expect($chunks)->toHaveCount(3);

        // First chunk should have 2 tuples
        expect($chunks[0]->getTotalOperations())->toBe(2);
        expect($chunks[0]->getWrites())->toHaveCount(2);
        expect($chunks[0]->getDeletes())->toBeNull();

        // Second chunk should have 2 tuples
        expect($chunks[1]->getTotalOperations())->toBe(2);
        expect($chunks[1]->getWrites())->toHaveCount(2);
        expect($chunks[1]->getDeletes())->toBeNull();

        // Third chunk should have 1 tuple
        expect($chunks[2]->getTotalOperations())->toBe(1);
        expect($chunks[2]->getWrites())->toHaveCount(1);
        expect($chunks[2]->getDeletes())->toBeNull();
    });

    test('chunks operation with both writes and deletes', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
            tuple('user:charlie', 'reader', 'document:3'),
        );

        $deletes = tuples(
            tuple('user:dave', 'reader', 'document:4'),
            tuple('user:eve', 'reader', 'document:5'),
        );

        $operation = new BatchTupleOperation(writes: $writes, deletes: $deletes);
        $chunks = $operation->chunk(2);

        expect($chunks)->toHaveCount(3);

        // First chunk: 2 writes
        expect($chunks[0]->getTotalOperations())->toBe(2);
        expect($chunks[0]->getWrites())->toHaveCount(2);
        expect($chunks[0]->getDeletes())->toBeNull();

        // Second chunk: 1 write + 1 delete
        expect($chunks[1]->getTotalOperations())->toBe(2);
        expect($chunks[1]->getWrites())->toHaveCount(1);
        expect($chunks[1]->getDeletes())->toHaveCount(1);

        // Third chunk: 1 delete
        expect($chunks[2]->getTotalOperations())->toBe(1);
        expect($chunks[2]->getWrites())->toBeNull();
        expect($chunks[2]->getDeletes())->toHaveCount(1);
    });

    test('chunks operation with single tuple returns single chunk', function (): void {
        $writes = tuples(tuple('user:alice', 'reader', 'document:1'));
        $operation = new BatchTupleOperation(writes: $writes);

        $chunks = $operation->chunk(10);

        expect($chunks)->toHaveCount(1);
        expect($chunks[0]->getTotalOperations())->toBe(1);
        expect($chunks[0]->getWrites())->toHaveCount(1);
    });

    test('chunks empty operation returns empty array', function (): void {
        $operation = new BatchTupleOperation;
        $chunks = $operation->chunk(10);

        expect($chunks)->toBe([]);
    });

    test('chunk size must be positive', function (): void {
        $operation = new BatchTupleOperation(writes: tuples(tuple('user:alice', 'reader', 'document:1')));

        expect(fn () => $operation->chunk(0))
            ->toThrow(InvalidArgumentException::class);

        expect(fn () => $operation->chunk(-1))
            ->toThrow(InvalidArgumentException::class);
    });

    test('respects maximum chunk size limit', function (): void {
        $operation = new BatchTupleOperation(writes: tuples(tuple('user:alice', 'reader', 'document:1')));

        expect(fn () => $operation->chunk(101))
            ->toThrow(InvalidArgumentException::class);
    });

    test('chunks preserve tuple order within writes', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
            tuple('user:charlie', 'reader', 'document:3'),
            tuple('user:dave', 'reader', 'document:4'),
        );

        $operation = new BatchTupleOperation(writes: $writes);
        $chunks = $operation->chunk(2);

        expect($chunks)->toHaveCount(2);

        // First chunk should have alice and bob
        $firstChunkWrites = $chunks[0]->getWrites()->toArray();
        expect($firstChunkWrites[0]->getUser())->toBe('user:alice');
        expect($firstChunkWrites[1]->getUser())->toBe('user:bob');

        // Second chunk should have charlie and dave
        $secondChunkWrites = $chunks[1]->getWrites()->toArray();
        expect($secondChunkWrites[0]->getUser())->toBe('user:charlie');
        expect($secondChunkWrites[1]->getUser())->toBe('user:dave');
    });

    test('chunks preserve tuple order within deletes', function (): void {
        $deletes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
            tuple('user:charlie', 'reader', 'document:3'),
        );

        $operation = new BatchTupleOperation(deletes: $deletes);
        $chunks = $operation->chunk(2);

        expect($chunks)->toHaveCount(2);

        // First chunk should have alice and bob
        $firstChunkDeletes = $chunks[0]->getDeletes()->toArray();
        expect($firstChunkDeletes[0]->getUser())->toBe('user:alice');
        expect($firstChunkDeletes[1]->getUser())->toBe('user:bob');

        // Second chunk should have charlie
        $secondChunkDeletes = $chunks[1]->getDeletes()->toArray();
        expect($secondChunkDeletes[0]->getUser())->toBe('user:charlie');
    });

    test('handles large batch operations efficiently', function (): void {
        $writes = [];
        $deletes = [];

        for ($i = 0; 1000 > $i; $i++) {
            $writes[] = tuple("user:user{$i}", 'reader', "document:{$i}");
            $deletes[] = tuple("user:olduser{$i}", 'reader', "olddocument:{$i}");
        }

        $operation = new BatchTupleOperation(
            writes: tuples(...$writes),
            deletes: tuples(...$deletes),
        );

        expect($operation->getTotalOperations())->toBe(2000);

        $chunks = $operation->chunk(100);
        expect($chunks)->toHaveCount(20);

        // Verify each chunk has exactly 100 operations
        for ($i = 0; 19 > $i; $i++) {
            expect($chunks[$i]->getTotalOperations())->toBe(100);
        }

        // Last chunk should also have 100 operations
        expect($chunks[19]->getTotalOperations())->toBe(100);
    });

    test('mixed operations chunking prioritizes writes first', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
        );

        $deletes = tuples(
            tuple('user:charlie', 'reader', 'document:3'),
            tuple('user:dave', 'reader', 'document:4'),
        );

        $operation = new BatchTupleOperation(writes: $writes, deletes: $deletes);
        $chunks = $operation->chunk(3);

        expect($chunks)->toHaveCount(2);

        // First chunk: 2 writes + 1 delete
        expect($chunks[0]->getTotalOperations())->toBe(3);
        expect($chunks[0]->getWrites())->toHaveCount(2);
        expect($chunks[0]->getDeletes())->toHaveCount(1);

        // Verify the first chunk has all writes and first delete
        $firstChunkWrites = $chunks[0]->getWrites()->toArray();
        expect($firstChunkWrites[0]->getUser())->toBe('user:alice');
        expect($firstChunkWrites[1]->getUser())->toBe('user:bob');

        $firstChunkDeletes = $chunks[0]->getDeletes()->toArray();
        expect($firstChunkDeletes[0]->getUser())->toBe('user:charlie');

        // Second chunk: 1 delete
        expect($chunks[1]->getTotalOperations())->toBe(1);
        expect($chunks[1]->getWrites())->toBeNull();
        expect($chunks[1]->getDeletes())->toHaveCount(1);

        $secondChunkDeletes = $chunks[1]->getDeletes()->toArray();
        expect($secondChunkDeletes[0]->getUser())->toBe('user:dave');
    });

    test('verifies MAX_TUPLES_PER_REQUEST constant', function (): void {
        expect(BatchTupleOperation::MAX_TUPLES_PER_REQUEST)->toBe(100);
    });

    test('chunk size cannot exceed MAX_TUPLES_PER_REQUEST', function (): void {
        $operation = new BatchTupleOperation(writes: tuples(tuple('user:alice', 'reader', 'document:1')));

        expect(fn () => $operation->chunk(BatchTupleOperation::MAX_TUPLES_PER_REQUEST + 1))
            ->toThrow(InvalidArgumentException::class);
    });

    test('can handle empty writes and deletes collections', function (): void {
        $emptyWrites = new TupleKeys([]);
        $emptyDeletes = new TupleKeys([]);

        $operation = new BatchTupleOperation(writes: $emptyWrites, deletes: $emptyDeletes);

        expect($operation->isEmpty())->toBeTrue();
        expect($operation->getTotalOperations())->toBe(0);
        expect($operation->chunk(10))->toBe([]);
    });

    test('handles single operation chunking correctly', function (): void {
        $writes = tuples(tuple('user:alice', 'reader', 'document:1'));
        $operation = new BatchTupleOperation(writes: $writes);

        $chunks = $operation->chunk(1);

        expect($chunks)->toHaveCount(1);
        expect($chunks[0]->getTotalOperations())->toBe(1);
        expect($chunks[0]->getWrites())->toHaveCount(1);
        expect($chunks[0]->getDeletes())->toBeNull();
    });

    test('verifies chunk distribution is even when possible', function (): void {
        // Create 10 operations that should chunk evenly into groups of 5
        $writes = [];

        for ($i = 0; 10 > $i; $i++) {
            $writes[] = tuple("user:user{$i}", 'reader', "document:{$i}");
        }

        $operation = new BatchTupleOperation(writes: tuples(...$writes));
        $chunks = $operation->chunk(5);

        expect($chunks)->toHaveCount(2);
        expect($chunks[0]->getTotalOperations())->toBe(5);
        expect($chunks[1]->getTotalOperations())->toBe(5);
    });
});
