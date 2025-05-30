<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use Exception;
use OpenFGA\ClientInterface;
use OpenFGA\Models\AuthorizationModelInterface;
use OpenFGA\Models\Collections\{ConditionParameters, TupleKeys};
use OpenFGA\Models\{Condition, ConditionParameter, TupleKey};
use OpenFGA\Models\Enums\TypeName;
use OpenFGA\Responses\CreateStoreResponseInterface;
use OpenFGA\Results\{Failure, Success};

use function OpenFGA\Models\{dsl, store, tuple, tuples};

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
            $condition = new Condition(
                name: 'inRegion',
                expression: 'params.region == "us-east"',
                parameters: new ConditionParameters([
                    new ConditionParameter(typeName: TypeName::STRING),
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
        });

        test('creates TupleKeys collection from array of TupleKeys', function (): void {
            $tupleKey1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
            $tupleKey2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');
            $tupleKey3 = new TupleKey(user: 'user:charlie', relation: 'owner', object: 'document:3');

            $collection = new TupleKeys([$tupleKey1, $tupleKey2, $tupleKey3]);

            expect($collection)->toBeInstanceOf(TupleKeys::class);
            expect($collection->count())->toBe(3);
            expect($collection->toArray())->toBe([$tupleKey1, $tupleKey2, $tupleKey3]);
        });

        test('creates empty collection from empty array', function (): void {
            $collection = tuples();

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

            $collection = tuples(...$tupleKeys);

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

            $collection = tuples(...$tupleKeys);

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

            $collection = tuples(...$tupleKeys);

            expect($collection->count())->toBe(3);
            expect($collection->get(0)->getUser())->toBe('user:anne');
            expect($collection->get(1)->getUser())->toBe('user:bob');
            expect($collection->get(2)->getUser())->toBe('user:charlie');
        });
    });

    describe('combined usage', function (): void {
        test('fluent API usage', function (): void {
            $collection = new TupleKeys([
                tuple(user: 'user:anne', relation: 'viewer', object: 'document:roadmap'),
                tuple(user: 'user:bob', relation: 'editor', object: 'document:roadmap'),
            ]);

            expect($collection->count())->toBe(2);
            expect($collection->jsonSerialize())->toBe([
                'tuple_keys' => [
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
                ],
            ]);
        });

        test('single tuple shorthand', function (): void {
            $collection = new TupleKeys([tuple(
                user: 'user:anne',
                relation: 'viewer',
                object: 'document:roadmap',
            )]);

            expect($collection->count())->toBe(1);
            expect($collection->get(0)->getUser())->toBe('user:anne');
        });

        test('with conditions', function (): void {
            $condition = new Condition(
                name: 'inRegion',
                expression: 'params.region == "us-east"',
                parameters: new ConditionParameters([
                    new ConditionParameter(typeName: TypeName::STRING),
                ]),
            );

            $collection = new TupleKeys([
                tuple(user: 'user:anne', relation: 'viewer', object: 'document:1'),
                tuple(user: 'user:bob', relation: 'editor', object: 'document:2', condition: $condition),
            ]);

            expect($collection->count())->toBe(2);
            expect($collection->get(0)->getCondition())->toBeNull();
            expect($collection->get(1)->getCondition())->toBe($condition);
        });
    });

    describe('store() function', function (): void {
        test('creates store and returns store ID', function (): void {
            $expectedStoreId = 'store-12345';

            $response = test()->createMock(CreateStoreResponseInterface::class);
            $response->method('getId')->willReturn($expectedStoreId);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('createStore')
                ->with('My Test Store')
                ->willReturn(new Success($response));

            $storeId = store($client, 'My Test Store');

            expect($storeId)->toBe($expectedStoreId);
        });

        test('throws exception when store creation fails', function (): void {
            $exception = new Exception('Store creation failed');

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('createStore')
                ->with('My Test Store')
                ->willReturn(new Failure($exception));

            expect(fn () => store($client, 'My Test Store'))
                ->toThrow(Exception::class, 'Store creation failed');
        });

        test('handles different store names', function (): void {
            $testCases = [
                'Simple Store',
                'Store-With-Dashes',
                'Store_With_Underscores',
                'Store With Spaces',
                'Store123',
                '🚀 Unicode Store 🎉',
                '',
            ];

            foreach ($testCases as $storeName) {
                $response = test()->createMock(CreateStoreResponseInterface::class);
                $response->method('getId')->willReturn('store-id');

                $client = test()->createMock(ClientInterface::class);
                $client->expects($this->once())
                    ->method('createStore')
                    ->with($storeName)
                    ->willReturn(new Success($response));

                $storeId = store($client, $storeName);

                expect($storeId)->toBe('store-id');
            }
        });
    });

    describe('dsl() function', function (): void {
        test('creates authorization model from DSL', function (): void {
            $dslString = 'model
  schema 1.1

type user

type document
  relations
    define viewer: [user]
    define editor: [user]
    define owner: [user]';

            $authModel = test()->createMock(AuthorizationModelInterface::class);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('dsl')
                ->with($dslString)
                ->willReturn(new Success($authModel));

            $result = dsl($client, $dslString);

            expect($result)->toBe($authModel);
        });

        test('throws exception when DSL parsing fails', function (): void {
            $dslString = 'invalid dsl';
            $exception = new Exception('Invalid DSL syntax');

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('dsl')
                ->with($dslString)
                ->willReturn(new Failure($exception));

            expect(fn () => dsl($client, $dslString))
                ->toThrow(Exception::class, 'Invalid DSL syntax');
        });

        test('handles empty DSL string', function (): void {
            $dslString = '';
            $authModel = test()->createMock(AuthorizationModelInterface::class);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('dsl')
                ->with($dslString)
                ->willReturn(new Success($authModel));

            $result = dsl($client, $dslString);

            expect($result)->toBe($authModel);
        });

        test('handles complex DSL with conditions', function (): void {
            $dslString = 'model
  schema 1.1

type user

type document
  relations
    define viewer: [user] with condition1
    define editor: [user] and viewer
    define owner: [user] and editor

condition condition1(region: string) {
  region == "us-east"
}';

            $authModel = test()->createMock(AuthorizationModelInterface::class);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('dsl')
                ->with($dslString)
                ->willReturn(new Success($authModel));

            $result = dsl($client, $dslString);

            expect($result)->toBe($authModel);
        });
    });
});
