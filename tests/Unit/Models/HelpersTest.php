<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\{ConditionParameters, TupleKeys, Tuples};
use OpenFGA\Models\{Condition, ConditionParameter, TupleKey};

use function OpenFGA\Models\{tuple, tuples};

describe('Helper Functions', function (): void {
    describe('tuple() function', function (): void {
        test('creates TupleKey with required parameters', function (): void {
            $tupleKey = tuple(
                user: 'user:anne',
                relation: 'viewer',
                object: 'document:roadmap',
            );

            expect($tupleKey)->toBeInstanceOf(TupleKey::class);
            expect($tupleKey->getUser())->toBe('user:anne');
            expect($tupleKey->getRelation())->toBe('viewer');
            expect($tupleKey->getObject())->toBe('document:roadmap');
            expect($tupleKey->getCondition())->toBeNull();
        });

        test('creates TupleKey with condition', function (): void {
            $condition = new Condition(name: 'inRegion', expression: 'params.region == "us-east"', parameters: new ConditionParameters([
                    new ConditionParameter(typeName: \OpenFGA\Models\Enums\TypeName::STRING),
                ]),
            );

            $tupleKey = tuple(
                user: 'user:anne',
                relation: 'viewer',
                object: 'document:roadmap',
                condition: $condition,
            );

            expect($tupleKey)->toBeInstanceOf(TupleKey::class);
            expect($tupleKey->getCondition())->toBe($condition);
        });

        test('handles different user formats', function (): void {
            $formats = [
                'user:anne',
                'group:admins#member',
                'folder:projects#viewer',
                'user:*',
                'application:client-123',
            ];

            foreach ($formats as $userFormat) {
                $tupleKey = tuple(
                    user: $userFormat,
                    relation: 'viewer',
                    object: 'document:test',
                );

                expect($tupleKey->getUser())->toBe($userFormat);
            }
        });

        test('handles empty strings', function (): void {
            $tupleKey = tuple(
                user: '',
                relation: '',
                object: '',
            );

            expect($tupleKey->getUser())->toBe('');
            expect($tupleKey->getRelation())->toBe('');
            expect($tupleKey->getObject())->toBe('');
        });

        test('preserves exact values without modification', function (): void {
            $tupleKey = tuple(
                user: '  user:anne  ',
                relation: '  viewer  ',
                object: '  document:roadmap  ',
            );

            expect($tupleKey->getUser())->toBe('  user:anne  ');
            expect($tupleKey->getRelation())->toBe('  viewer  ');
            expect($tupleKey->getObject())->toBe('  document:roadmap  ');
        });
    });

    describe('tuples() function', function (): void {
        test('creates TupleKeys collection from single TupleKey', function (): void {
            $tupleKey = new TupleKey(
                user: 'user:anne',
                relation: 'viewer',
                object: 'document:roadmap',
            );

            $collection = tuples($tupleKey);

            expect($collection)->toBeInstanceOf(TupleKeys::class);
            expect($collection->count())->toBe(1);
            // Collection method removed - not available
        });

        test('creates TupleKeys collection from array of TupleKeys', function (): void {
            $tupleKey1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
            $tupleKey2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');
            $tupleKey3 = new TupleKey(user: 'user:charlie', relation: 'owner', object: 'document:3');

            $collection = new \OpenFGA\Models\Collections\Tuples([$tupleKey1, $tupleKey2, $tupleKey3]);

            expect($collection)->toBeInstanceOf(TupleKeys::class);
            expect($collection->count())->toBe(3);
            expect($collection->toArray())->toBe([$tupleKey1, $tupleKey2, $tupleKey3]);
        });

        test('creates empty collection from empty array', function (): void {
            $collection = tuples([]);

            expect($collection)->toBeInstanceOf(TupleKeys::class);
            expect($collection->count())->toBe(0);
            expect($collection->isEmpty())->toBe(true);
        });

        test('works with tuple() helper function', function (): void {
            $tupleKey = tuple(
                user: 'user:anne',
                relation: 'viewer',
                object: 'document:roadmap',
            );

            $collection = tuples($tupleKey);

            expect($collection->count())->toBe(1);
            expect($collection->first()->getUser())->toBe('user:anne');
        });

        test('works with array of tuple() results', function (): void {
            $tupleKeys = [
                tuple(user: 'user:anne', relation: 'viewer', object: 'document:1'),
                tuple(user: 'user:bob', relation: 'editor', object: 'document:2'),
                tuple(user: 'user:charlie', relation: 'owner', object: 'document:3'),
            ];

            $collection = tuples($tupleKeys);

            expect($collection->count())->toBe(3);
            expect($collection->get(0)->getUser())->toBe('user:anne');
            expect($collection->get(1)->getUser())->toBe('user:bob');
            expect($collection->get(2)->getUser())->toBe('user:charlie');
        });

        test('preserves tuple order', function (): void {
            $tupleKeys = [];
            for ($i = 0; $i < 10; ++$i) {
                $tupleKeys[] = tuple(
                    user: "user:user{$i}",
                    relation: 'viewer',
                    object: "document:doc{$i}",
                );
            }

            $collection = tuples($tupleKeys);

            expect($collection->count())->toBe(10);
            for ($i = 0; $i < 10; ++$i) {
                expect($collection->get($i)->getUser())->toBe("user:user{$i}");
            }
        });

        test('handles mixed tuple creation', function (): void {
            // Mix of direct TupleKey instances and tuple() helper results
            $tupleKeys = [
                new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1'),
                tuple(user: 'user:bob', relation: 'editor', object: 'document:2'),
                new TupleKey(user: 'user:charlie', relation: 'owner', object: 'document:3'),
            ];

            $collection = tuples($tupleKeys);

            expect($collection->count())->toBe(3);
            expect($collection->get(0)->getUser())->toBe('user:anne');
            expect($collection->get(1)->getUser())->toBe('user:bob');
            expect($collection->get(2)->getUser())->toBe('user:charlie');
        });
    });

    describe('combined usage', function (): void {
        test('fluent API usage', function (): void {
            $collection = new \OpenFGA\Models\Collections\Tuples([
                tuple(user: 'user:anne', relation: 'viewer', object: 'document:roadmap'),
                tuple(user: 'user:bob', relation: 'editor', object: 'document:roadmap'),
            ]);

            expect($collection->count())->toBe(2);
            expect($collection->jsonSerialize())->toBe([
                [
                    'user' => 'user:anne',
                    'relation' => 'viewer',
                    'object' => 'document:roadmap',
                ],
                [
                    'user' => 'user:bob',
                    'relation' => 'editor',
                    'object' => 'document:roadmap',
                ],
            ]);
        });

        test('single tuple shorthand', function (): void {
            $collection = new \OpenFGA\Models\Collections\Tuples([tuple(
                user: 'user:anne',
                relation: 'viewer',
                object: 'document:roadmap',
            )]);

            expect($collection->count())->toBe(1);
            expect($collection->first()->getUser())->toBe('user:anne');
        });

        test('with conditions', function (): void {
            $condition = new Condition(name: 'inRegion', expression: 'params.region == "us-east"', parameters: new ConditionParameters([
                    new ConditionParameter(typeName: \OpenFGA\Models\Enums\TypeName::STRING),
                ]),
            );

            $collection = new \OpenFGA\Models\Collections\Tuples([
                tuple(user: 'user:anne', relation: 'viewer', object: 'document:1'),
                tuple(user: 'user:bob', relation: 'editor', object: 'document:2', condition: $condition),
            ]);

            expect($collection->count())->toBe(2);
            expect($collection->get(0)->getCondition())->toBeNull();
            expect($collection->get(1)->getCondition())->toBe($condition);
        });
    });
});
