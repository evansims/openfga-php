<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Models\{ConditionInterface, TupleKey};
use OpenFGA\Services\TupleFilterService;

describe('TupleFilterService', function (): void {
    beforeEach(function (): void {
        $this->service = new TupleFilterService;
    });

    describe('filterDuplicates()', function (): void {
        test('returns null arrays when both inputs are null', function (): void {
            [$writes, $deletes] = $this->service->filterDuplicates(null, null);

            expect($writes)->toBeNull();
            expect($deletes)->toBeNull();
        });

        test('returns null arrays when both inputs are empty', function (): void {
            $emptyWrites = new TupleKeys([]);
            $emptyDeletes = new TupleKeys([]);

            [$writes, $deletes] = $this->service->filterDuplicates($emptyWrites, $emptyDeletes);

            expect($writes)->toBeNull();
            expect($deletes)->toBeNull();
        });

        test('removes duplicate writes', function (): void {
            $tuple1 = new TupleKey('user:anne', 'reader', 'document:1');
            $tuple2 = new TupleKey('user:anne', 'reader', 'document:1'); // duplicate
            $tuple3 = new TupleKey('user:bob', 'writer', 'document:2');

            $writes = new TupleKeys([$tuple1, $tuple2, $tuple3]);

            [$filteredWrites, $filteredDeletes] = $this->service->filterDuplicates($writes, null);

            expect($filteredWrites)->toBeInstanceOf(TupleKeys::class);
            expect($filteredWrites->count())->toBe(2);
            expect($filteredDeletes)->toBeNull();

            // Verify the filtered collection contains the right tuples
            $filtered = [];

            foreach ($filteredWrites as $tuple) {
                $filtered[] = [
                    'user' => $tuple->getUser(),
                    'relation' => $tuple->getRelation(),
                    'object' => $tuple->getObject(),
                ];
            }

            expect($filtered)->toBe([
                ['user' => 'user:anne', 'relation' => 'reader', 'object' => 'document:1'],
                ['user' => 'user:bob', 'relation' => 'writer', 'object' => 'document:2'],
            ]);
        });

        test('removes duplicate deletes', function (): void {
            $tuple1 = new TupleKey('user:anne', 'reader', 'document:1');
            $tuple2 = new TupleKey('user:anne', 'reader', 'document:1'); // duplicate
            $tuple3 = new TupleKey('user:bob', 'writer', 'document:2');

            $deletes = new TupleKeys([$tuple1, $tuple2, $tuple3]);

            [$filteredWrites, $filteredDeletes] = $this->service->filterDuplicates(null, $deletes);

            expect($filteredWrites)->toBeNull();
            expect($filteredDeletes)->toBeInstanceOf(TupleKeys::class);
            expect($filteredDeletes->count())->toBe(2);
        });

        test('delete takes precedence when tuple appears in both writes and deletes', function (): void {
            $writeTuple1 = new TupleKey('user:anne', 'reader', 'document:1');
            $writeTuple2 = new TupleKey('user:bob', 'writer', 'document:2');
            $writeTuple3 = new TupleKey('user:charlie', 'viewer', 'document:3');

            $deleteTuple1 = new TupleKey('user:anne', 'reader', 'document:1'); // same as write
            $deleteTuple2 = new TupleKey('user:dave', 'editor', 'document:4');

            $writes = new TupleKeys([$writeTuple1, $writeTuple2, $writeTuple3]);
            $deletes = new TupleKeys([$deleteTuple1, $deleteTuple2]);

            [$filteredWrites, $filteredDeletes] = $this->service->filterDuplicates($writes, $deletes);

            expect($filteredWrites)->toBeInstanceOf(TupleKeys::class);
            expect($filteredWrites->count())->toBe(2); // anne's tuple removed
            expect($filteredDeletes)->toBeInstanceOf(TupleKeys::class);
            expect($filteredDeletes->count())->toBe(2);

            // Verify anne's tuple is not in writes
            $writeUsers = [];

            foreach ($filteredWrites as $tuple) {
                $writeUsers[] = $tuple->getUser();
            }
            expect($writeUsers)->not->toContain('user:anne');
            expect($writeUsers)->toContain('user:bob', 'user:charlie');
        });

        test('preserves order of first occurrence when filtering duplicates', function (): void {
            $tuple1 = new TupleKey('user:first', 'reader', 'document:1');
            $tuple2 = new TupleKey('user:second', 'writer', 'document:2');
            $tuple3 = new TupleKey('user:first', 'reader', 'document:1'); // duplicate
            $tuple4 = new TupleKey('user:third', 'viewer', 'document:3');

            $writes = new TupleKeys([$tuple1, $tuple2, $tuple3, $tuple4]);

            [$filteredWrites, $filteredDeletes] = $this->service->filterDuplicates($writes, null);

            $users = [];

            foreach ($filteredWrites as $tuple) {
                $users[] = $tuple->getUser();
            }

            expect($users)->toBe(['user:first', 'user:second', 'user:third']);
        });

        test('handles tuples with conditions correctly', function (): void {
            $condition1 = test()->createMock(ConditionInterface::class);
            $condition2 = test()->createMock(ConditionInterface::class);

            $tuple1 = new TupleKey('user:anne', 'reader', 'document:1', $condition1);
            $tuple2 = new TupleKey('user:anne', 'reader', 'document:1', $condition2); // same but different condition
            $tuple3 = new TupleKey('user:anne', 'reader', 'document:1', $condition1); // true duplicate

            $writes = new TupleKeys([$tuple1, $tuple2, $tuple3]);

            [$filteredWrites, $filteredDeletes] = $this->service->filterDuplicates($writes, null);

            expect($filteredWrites->count())->toBe(2); // two unique tuples (different conditions)
        });

        test('handles null values in tuple properties', function (): void {
            // TupleKey constructor requires non-null values, but the service should handle edge cases gracefully
            $tuple1 = new TupleKey('user:anne', 'reader', 'document:1');
            $tuple2 = new TupleKey('user:bob', 'writer', 'document:2');

            $writes = new TupleKeys([$tuple1, $tuple2]);

            [$filteredWrites, $filteredDeletes] = $this->service->filterDuplicates($writes, null);

            expect($filteredWrites)->toBeInstanceOf(TupleKeys::class);
            expect($filteredWrites->count())->toBe(2);
        });

        test('complex scenario with mixed duplicates and overlaps', function (): void {
            // Writes with duplicates
            $w1 = new TupleKey('user:1', 'reader', 'doc:a');
            $w2 = new TupleKey('user:2', 'writer', 'doc:b');
            $w3 = new TupleKey('user:1', 'reader', 'doc:a'); // duplicate of w1
            $w4 = new TupleKey('user:3', 'viewer', 'doc:c');
            $w5 = new TupleKey('user:4', 'editor', 'doc:d');

            // Deletes with duplicates and overlap with writes
            $d1 = new TupleKey('user:2', 'writer', 'doc:b'); // overlaps with w2
            $d2 = new TupleKey('user:5', 'reader', 'doc:e');
            $d3 = new TupleKey('user:2', 'writer', 'doc:b'); // duplicate of d1
            $d4 = new TupleKey('user:4', 'editor', 'doc:d'); // overlaps with w5

            $writes = new TupleKeys([$w1, $w2, $w3, $w4, $w5]);
            $deletes = new TupleKeys([$d1, $d2, $d3, $d4]);

            [$filteredWrites, $filteredDeletes] = $this->service->filterDuplicates($writes, $deletes);

            // Expected writes: w1, w4 (w2 and w5 removed due to deletes, w3 was duplicate)
            expect($filteredWrites->count())->toBe(2);

            // Expected deletes: d1, d2, d4 (d3 was duplicate)
            expect($filteredDeletes->count())->toBe(3);

            // Verify specific tuples
            $writeUsers = [];

            foreach ($filteredWrites as $tuple) {
                $writeUsers[] = $tuple->getUser();
            }
            expect($writeUsers)->toBe(['user:1', 'user:3']);

            $deleteUsers = [];

            foreach ($filteredDeletes as $tuple) {
                $deleteUsers[] = $tuple->getUser();
            }
            expect($deleteUsers)->toContain('user:2', 'user:5', 'user:4');
        });
    });
});
