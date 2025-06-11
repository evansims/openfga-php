<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use JsonSerializable;
use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Models\Condition;
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
            $condition1->method('getName')->willReturn('cond1');
            $condition1->method('getContext')->willReturn(null);

            $condition2 = test()->createMock(ConditionInterface::class);
            $condition2->method('getName')->willReturn('cond2');
            $condition2->method('getContext')->willReturn(null);

            $tuple1 = new TupleKey('user:anne', 'reader', 'document:1', $condition1);
            $tuple2 = new TupleKey('user:anne', 'reader', 'document:1', $condition2); // same but different condition
            $tuple3 = new TupleKey('user:anne', 'reader', 'document:1', $condition1); // true duplicate

            $writes = new TupleKeys([$tuple1, $tuple2, $tuple3]);

            [$filteredWrites, $filteredDeletes] = $this->service->filterDuplicates($writes, null);

            expect($filteredWrites->count())->toBe(2); // two unique tuples (different conditions)
        });

        test('deduplicates tuples with equivalent conditions', function (): void {
            $condition1 = new Condition(
                name: 'inRegion',
                expression: 'params.region == "us"',
            );
            $condition2 = new Condition(
                name: 'inRegion',
                expression: 'params.region == "us"',
            );

            $tuple1 = new TupleKey('user:anne', 'reader', 'document:1', $condition1);
            $tuple2 = new TupleKey('user:anne', 'reader', 'document:1', $condition2); // same contents

            $writes = new TupleKeys([$tuple1, $tuple2]);

            [$filteredWrites, ] = $this->service->filterDuplicates($writes, null);

            expect($filteredWrites->count())->toBe(1); // duplicates merged
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

        test('handles conditions with nested context arrays having different key orders', function (): void {
            // Create conditions with same data but different nested key orders
            $condition1 = new Condition(
                name: 'hasAccess',
                expression: 'params.user.role == "admin"',
                context: [
                    'user' => [
                        'id' => 'user123',
                        'role' => 'admin',
                        'permissions' => [
                            'read' => true,
                            'write' => true,
                        ],
                    ],
                    'resource' => [
                        'type' => 'document',
                        'level' => 'confidential',
                    ],
                ],
            );

            $condition2 = new Condition(
                name: 'hasAccess',
                expression: 'params.user.role == "admin"',
                context: [
                    'resource' => [
                        'level' => 'confidential',
                        'type' => 'document',
                    ],
                    'user' => [
                        'permissions' => [
                            'write' => true,
                            'read' => true,
                        ],
                        'role' => 'admin',
                        'id' => 'user123',
                    ],
                ],
            );

            $tuple1 = new TupleKey('user:anne', 'reader', 'document:1', $condition1);
            $tuple2 = new TupleKey('user:anne', 'reader', 'document:1', $condition2); // Should be treated as duplicate

            $writes = new TupleKeys([$tuple1, $tuple2]);

            [$filteredWrites,] = $this->service->filterDuplicates($writes, null);

            expect($filteredWrites->count())->toBe(1); // Should deduplicate despite different key orders
        });

        test('correctly handles empty filtered collections', function (): void {
            // Create a scenario where filtering results in empty collections
            $tuple1 = new TupleKey('user:anne', 'reader', 'document:1');

            // Both writes and deletes have the same tuple (delete takes precedence)
            $writes = new TupleKeys([$tuple1]);
            $deletes = new TupleKeys([$tuple1]);

            [$filteredWrites, $filteredDeletes] = $this->service->filterDuplicates($writes, $deletes);

            // Writes should be null because the only write is also in deletes
            expect($filteredWrites)->toBeNull();
            // Deletes should contain the tuple
            expect($filteredDeletes)->toBeInstanceOf(TupleKeys::class);
            expect($filteredDeletes->count())->toBe(1);
        });

        test('handles conditions with empty context arrays', function (): void {
            $condition1 = new Condition(
                name: 'alwaysTrue',
                expression: 'true',
                context: [],
            );

            $condition2 = new Condition(
                name: 'alwaysTrue',
                expression: 'true',
                context: null,
            );

            $tuple1 = new TupleKey('user:anne', 'reader', 'document:1', $condition1);
            $tuple2 = new TupleKey('user:anne', 'reader', 'document:1', $condition2);

            $writes = new TupleKeys([$tuple1, $tuple2]);

            [$filteredWrites,] = $this->service->filterDuplicates($writes, null);

            // Should treat both as the same since empty array and null context are equivalent
            expect($filteredWrites->count())->toBe(1);
        });

        test('handles deeply nested arrays with recursive sorting', function (): void {
            $condition1 = new Condition(
                name: 'complexCondition',
                expression: 'params.user.roles[0] == "admin"',
                context: [
                    'user' => [
                        'profile' => [
                            'settings' => [
                                'theme' => 'dark',
                                'language' => 'en',
                                'notifications' => [
                                    'email' => true,
                                    'sms' => false,
                                ],
                            ],
                        ],
                        'roles' => ['admin', 'user'],
                    ],
                ],
            );

            $condition2 = new Condition(
                name: 'complexCondition',
                expression: 'params.user.roles[0] == "admin"',
                context: [
                    'user' => [
                        'roles' => ['admin', 'user'],
                        'profile' => [
                            'settings' => [
                                'notifications' => [
                                    'sms' => false,
                                    'email' => true,
                                ],
                                'language' => 'en',
                                'theme' => 'dark',
                            ],
                        ],
                    ],
                ],
            );

            $tuple1 = new TupleKey('user:alice', 'admin', 'resource:secret', $condition1);
            $tuple2 = new TupleKey('user:alice', 'admin', 'resource:secret', $condition2);

            $writes = new TupleKeys([$tuple1, $tuple2]);

            [$filteredWrites,] = $this->service->filterDuplicates($writes, null);

            // Should be deduplicated because recursive sorting makes contexts equivalent
            expect($filteredWrites->count())->toBe(1);
        });

        test('handles tuples with binary or special data in context', function (): void {
            // Create conditions with data that might be challenging for JSON encoding
            $condition1 = new Condition(
                name: 'binaryCondition',
                expression: 'params.data == "test"',
                context: [
                    'data' => 'test',
                    'metadata' => [
                        'encoding' => 'utf-8',
                        'special_chars' => "Special: \u{1F600} \u{1F4A9}",
                        'numbers' => [1, 2, 3, PHP_INT_MAX],
                    ],
                ],
            );

            $condition2 = new Condition(
                name: 'binaryCondition',
                expression: 'params.data == "test"',
                context: [
                    'metadata' => [
                        'numbers' => [1, 2, 3, PHP_INT_MAX],
                        'special_chars' => "Special: \u{1F600} \u{1F4A9}",
                        'encoding' => 'utf-8',
                    ],
                    'data' => 'test',
                ],
            );

            $tuple1 = new TupleKey('user:alice', 'viewer', 'resource:file', $condition1);
            $tuple2 = new TupleKey('user:alice', 'viewer', 'resource:file', $condition2);

            $writes = new TupleKeys([$tuple1, $tuple2]);

            [$filteredWrites,] = $this->service->filterDuplicates($writes, null);

            // Should be deduplicated even with special characters and large numbers
            expect($filteredWrites->count())->toBe(1);
        });

        test('correctly handles empty string values without null coercion issues', function (): void {
            // Test that tuples with actual empty strings are handled correctly
            // This verifies our fix prevents key collisions between null and empty string
            $tuple1 = new TupleKey('', 'reader', 'document:1');  // Empty user
            $tuple2 = new TupleKey('user:anne', '', 'document:2');  // Empty relation
            $tuple3 = new TupleKey('user:bob', 'writer', '');  // Empty object
            $tuple4 = new TupleKey('user:charlie', 'viewer', 'document:3');  // All non-empty

            $writes = new TupleKeys([$tuple1, $tuple2, $tuple3, $tuple4]);

            [$filteredWrites,] = $this->service->filterDuplicates($writes, null);

            // All tuples should be preserved as they are genuinely different
            expect($filteredWrites->count())->toBe(4);

            // Verify each tuple is correctly preserved
            $tuples = [];

            foreach ($filteredWrites as $tuple) {
                $tuples[] = [
                    'user' => $tuple->getUser(),
                    'relation' => $tuple->getRelation(),
                    'object' => $tuple->getObject(),
                ];
            }

            expect($tuples)->toBe([
                ['user' => '', 'relation' => 'reader', 'object' => 'document:1'],
                ['user' => 'user:anne', 'relation' => '', 'object' => 'document:2'],
                ['user' => 'user:bob', 'relation' => 'writer', 'object' => ''],
                ['user' => 'user:charlie', 'relation' => 'viewer', 'object' => 'document:3'],
            ]);
        });

        test('handles contexts with JsonSerializable objects', function (): void {
            // Create a mock JsonSerializable object for testing
            $mockJsonSerializable = new class implements JsonSerializable {
                public function jsonSerialize(): array
                {
                    return [
                        'type' => 'user',
                        'permissions' => ['read', 'write'],
                        'metadata' => [
                            'created' => '2024-01-01',
                            'updated' => '2024-01-02',
                        ],
                    ];
                }
            };

            // Create conditions with JsonSerializable objects that should normalize to the same value
            $condition1 = new Condition(
                name: 'hasPermission',
                expression: 'params.user.type == "user"',
                context: [
                    'user' => $mockJsonSerializable,
                    'other' => 'data',
                ],
            );

            $condition2 = new Condition(
                name: 'hasPermission',
                expression: 'params.user.type == "user"',
                context: [
                    'other' => 'data',
                    'user' => [
                        'metadata' => [
                            'updated' => '2024-01-02',
                            'created' => '2024-01-01',
                        ],
                        'permissions' => ['read', 'write'],
                        'type' => 'user',
                    ],
                ],
            );

            $tuple1 = new TupleKey('user:bob', 'editor', 'document:report', $condition1);
            $tuple2 = new TupleKey('user:bob', 'editor', 'document:report', $condition2);

            $writes = new TupleKeys([$tuple1, $tuple2]);

            [$filteredWrites,] = $this->service->filterDuplicates($writes, null);

            // Should be deduplicated because the JsonSerializable object gets normalized
            // to the same structure as condition2 after recursive sorting
            expect($filteredWrites->count())->toBe(1);
        });
    });
});
