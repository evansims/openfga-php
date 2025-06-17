<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use OpenFGA\{ClientInterface, Language};
use OpenFGA\Exceptions\{ClientError, ClientException, NetworkError};
use OpenFGA\Models\{AuthorizationModelInterface, BatchCheckItem, Condition, ConditionParameter, TupleChange, TupleChangeInterface, TupleKey, TupleKeyInterface};
use OpenFGA\Models\Collections\{
    ConditionParameters,
    Conditions,
    TupleChanges,
    TupleKeys,
    Tuples,
    TypeDefinitions
};
use OpenFGA\Models\Collections\{TupleKeysInterface, UserTypeFiltersInterface, Users};
use OpenFGA\Models\Collections\UserTypeFilters;
use OpenFGA\Models\Enums\{Consistency, SchemaVersion, TupleOperation, TypeName};
use OpenFGA\Models\Tuple;
use OpenFGA\Models\{User, UserTypeFilter};
use OpenFGA\Responses\{
    CheckResponseInterface,
    CreateAuthorizationModelResponseInterface,
    CreateStoreResponseInterface,
    WriteTuplesResponseInterface
};
use OpenFGA\Responses\{ListTupleChangesResponseInterface, ListUsersResponseInterface, ReadTuplesResponseInterface};
use OpenFGA\Results\{Failure, Success};
use RuntimeException;
use Throwable;

use function OpenFGA\{
    allowed,
    changes,
    check,
    delete,
    dsl,
    err,
    failure,
    filter,
    filters,
    lang,
    model,
    ok,
    read,
    result,
    store,
    success,
    tuple,
    tuples,
    unwrap,
    users,
    write
};

describe('Helper Functions', function (): void {
    beforeEach(function (): void {
        $this->testValue = 'test-value';
        $this->testError = ClientError::Validation->exception();
        $this->networkError = NetworkError::Unexpected->exception();
    });

    // ==============================================================================
    // Models Helpers Tests
    // ==============================================================================

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

            for ($i = 0; 10 > $i; ++$i) {
                $tupleKeys[] = tuple(
                    user: "user:user{$i}",
                    relation: 'viewer',
                    object: "document:doc{$i}",
                );
            }

            $collection = tuples(...$tupleKeys);

            expect($collection->count())->toBe(10);

            for ($i = 0; 10 > $i; ++$i) {
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

    describe('check() function', function (): void {
        test('creates BatchCheckItem with TupleKey and correlation', function (): void {
            $tupleKey = tuple('user:anne', 'viewer', 'document:budget');
            $correlation = 'anne-check';

            $batchCheckItem = check($correlation, $tupleKey);

            expect($batchCheckItem)->toBeInstanceOf(BatchCheckItem::class);
            expect($batchCheckItem->getTupleKey())->toBe($tupleKey);
            expect($batchCheckItem->getCorrelationId())->toBe($correlation);
            expect($batchCheckItem->getContextualTuples())->toBeNull();
            expect($batchCheckItem->getContext())->toBeNull();
        });

        test('creates BatchCheckItem with individual parameters', function (): void {
            $batchCheckItem = check(user: 'user:anne', relation: 'viewer', object: 'document:budget');

            expect($batchCheckItem)->toBeInstanceOf(BatchCheckItem::class);
            expect($batchCheckItem->getTupleKey()->getUser())->toBe('user:anne');
            expect($batchCheckItem->getTupleKey()->getRelation())->toBe('viewer');
            expect($batchCheckItem->getTupleKey()->getObject())->toBe('document:budget');
            expect($batchCheckItem->getContextualTuples())->toBeNull();
            expect($batchCheckItem->getContext())->toBeNull();
        });

        test('creates BatchCheckItem with individual parameters and correlation', function (): void {
            $batchCheckItem = check('anne-check', user: 'user:anne', relation: 'viewer', object: 'document:budget');

            expect($batchCheckItem)->toBeInstanceOf(BatchCheckItem::class);
            expect($batchCheckItem->getTupleKey()->getUser())->toBe('user:anne');
            expect($batchCheckItem->getTupleKey()->getRelation())->toBe('viewer');
            expect($batchCheckItem->getTupleKey()->getObject())->toBe('document:budget');
            expect($batchCheckItem->getCorrelationId())->toBe('anne-check');
        });

        test('throws exception when individual parameters are incomplete', function (): void {
            check(user: 'user:anne', object: 'document:budget');
        })->throws(InvalidArgumentException::class, 'Either $tuple must be provided, or all of $user, $relation, and $object must be provided');

        test('creates BatchCheckItem with auto-generated correlation ID', function (): void {
            $tupleKey = tuple('user:anne', 'viewer', 'document:budget');

            $batchCheckItem = check(tuple: $tupleKey);

            expect($batchCheckItem)->toBeInstanceOf(BatchCheckItem::class);
            expect($batchCheckItem->getTupleKey())->toBe($tupleKey);
            expect($batchCheckItem->getCorrelationId())->toBeString();
            expect($batchCheckItem->getCorrelationId())->toHaveLength(36);
            expect($batchCheckItem->getContextualTuples())->toBeNull();
            expect($batchCheckItem->getContext())->toBeNull();
        });

        test('generates consistent correlation ID for same tuple', function (): void {
            $tupleKey1 = tuple('user:anne', 'viewer', 'document:budget');
            $tupleKey2 = tuple('user:anne', 'viewer', 'document:budget');

            $batchCheckItem1 = check(tuple: $tupleKey1);
            $batchCheckItem2 = check(tuple: $tupleKey2);

            expect($batchCheckItem1->getCorrelationId())->toBe($batchCheckItem2->getCorrelationId());
        });

        test('generates different correlation ID for different tuples', function (): void {
            $tupleKey1 = tuple('user:anne', 'viewer', 'document:budget');
            $tupleKey2 = tuple('user:bob', 'viewer', 'document:budget');

            $batchCheckItem1 = check(tuple: $tupleKey1);
            $batchCheckItem2 = check(tuple: $tupleKey2);

            expect($batchCheckItem1->getCorrelationId())->not->toBe($batchCheckItem2->getCorrelationId());
        });

        test('creates BatchCheckItem with context', function (): void {
            $tupleKey = tuple('user:bob', 'editor', 'document:budget');
            $correlation = 'bob-edit-check';
            $context = (object) ['time' => '10:00', 'department' => 'finance'];

            $batchCheckItem = check($correlation, $tupleKey, context: $context);

            expect($batchCheckItem)->toBeInstanceOf(BatchCheckItem::class);
            expect($batchCheckItem->getContext())->toBe($context);
        });

        test('creates BatchCheckItem with contextual tuples', function (): void {
            $tupleKey = tuple('user:charlie', 'owner', 'document:budget');
            $correlation = 'charlie-owner-check';
            $contextualTuples = tuples(
                tuple('user:charlie', 'member', 'team:finance'),
            );

            $batchCheckItem = check($correlation, $tupleKey, contextualTuples: $contextualTuples);

            expect($batchCheckItem)->toBeInstanceOf(BatchCheckItem::class);
            expect($batchCheckItem->getContextualTuples())->toBe($contextualTuples);
        });

        test('creates BatchCheckItem with all parameters', function (): void {
            $tupleKey = tuple('user:david', 'admin', 'system:config');
            $correlation = 'david-admin-check';
            $contextualTuples = tuples(
                tuple('user:david', 'member', 'group:administrators'),
            );
            $context = (object) ['ip' => '192.168.1.1', 'time' => '14:30'];

            $batchCheckItem = check($correlation, $tupleKey, contextualTuples: $contextualTuples, context: $context);

            expect($batchCheckItem)->toBeInstanceOf(BatchCheckItem::class);
            expect($batchCheckItem->getTupleKey())->toBe($tupleKey);
            expect($batchCheckItem->getCorrelationId())->toBe($correlation);
            expect($batchCheckItem->getContextualTuples())->toBe($contextualTuples);
            expect($batchCheckItem->getContext())->toBe($context);
        });

        test('validates correlation ID format when provided', function (): void {
            $tupleKey = tuple('user:anne', 'viewer', 'document:budget');

            check('invalid correlation id with spaces', $tupleKey);
        })->throws(Exception::class);

        test('handles different correlation ID formats', function (): void {
            $tupleKey = tuple('user:anne', 'viewer', 'document:budget');

            $validIds = [
                'simple',
                'with-hyphens',
                'with_underscores',
                'with123numbers',
                'a',
                '123456789012345678901234567890123456', // 36 characters max
            ];

            foreach ($validIds as $correlation) {
                $batchCheckItem = check($correlation, $tupleKey);
                expect($batchCheckItem->getCorrelationId())->toBe($correlation);
            }
        });

        test('works with different tuple key formats', function (): void {
            $testCases = [
                tuple('user:anne', 'viewer', 'document:budget'),
                tuple('group:admins#member', 'editor', 'folder:root'),
                tuple('user:*', 'viewer', 'document:public'),
                tuple('application:client', 'can_read', 'resource:data'),
            ];

            foreach ($testCases as $tupleKey) {
                $batchCheckItem = check('test-check', $tupleKey);
                expect($batchCheckItem->getTupleKey())->toBe($tupleKey);
            }
        });

        test('preserves tuple key with conditions', function (): void {
            $condition = new Condition(
                name: 'inRegion',
                expression: 'params.region == "us-east"',
                parameters: new ConditionParameters([
                    new ConditionParameter(typeName: TypeName::STRING),
                ]),
            );

            $tupleKey = tuple('user:anne', 'viewer', 'document:regional', $condition);
            $batchCheckItem = check('conditional-check', $tupleKey);

            expect($batchCheckItem->getTupleKey()->getCondition())->toBe($condition);
        });

        test('creates BatchCheckItem with individual parameters and condition', function (): void {
            $condition = new Condition(
                name: 'inRegion',
                expression: 'params.region == "us-east"',
                parameters: new ConditionParameters([
                    new ConditionParameter(typeName: TypeName::STRING),
                ]),
            );

            $batchCheckItem = check('conditional-check', user: 'user:anne', relation: 'viewer', object: 'document:regional', condition: $condition);

            expect($batchCheckItem)->toBeInstanceOf(BatchCheckItem::class);
            expect($batchCheckItem->getTupleKey()->getUser())->toBe('user:anne');
            expect($batchCheckItem->getTupleKey()->getRelation())->toBe('viewer');
            expect($batchCheckItem->getTupleKey()->getObject())->toBe('document:regional');
            expect($batchCheckItem->getTupleKey()->getCondition())->toBe($condition);
            expect($batchCheckItem->getCorrelationId())->toBe('conditional-check');
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

            $storeId = store('My Test Store', $client);

            expect($storeId)->toBe($expectedStoreId);
        });

        test('throws exception when store creation fails', function (): void {
            $exception = new Exception('Store creation failed');

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('createStore')
                ->with('My Test Store')
                ->willReturn(new Failure($exception));

            store('My Test Store', $client);
        })->throws(Exception::class, 'Store creation failed');

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

                $storeId = store($storeName, $client);

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

            $result = dsl($dslString, $client);

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

            dsl($dslString, $client);
        })->throws(Exception::class, 'Invalid DSL syntax');

        test('handles empty DSL string', function (): void {
            $dslString = '';
            $authModel = test()->createMock(AuthorizationModelInterface::class);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('dsl')
                ->with($dslString)
                ->willReturn(new Success($authModel));

            $result = dsl($dslString, $client);

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

            $result = dsl($dslString, $client);

            expect($result)->toBe($authModel);
        });
    });

    // ==============================================================================
    // Results Helpers Tests
    // ==============================================================================

    describe('result() function', function (): void {
        test('wraps closure return value in Success', function (): void {
            $closure = fn () => $this->testValue;

            $result = result($closure);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->val())->toBe($this->testValue);
        });

        test('wraps closure thrown exception in Failure', function (): void {
            $closure = function (): void {
                throw $this->testError;
            };

            $result = result($closure);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBe($this->testError);
        });

        test('returns ResultInterface when closure returns one', function (): void {
            $expectedResult = new Success($this->testValue);
            $closure = fn () => $expectedResult;

            $result = result($closure);

            expect($result)->toBe($expectedResult);
        });

        test('returns ResultInterface Failure when closure returns one', function (): void {
            $expectedResult = new Failure($this->testError);
            $closure = fn () => $expectedResult;

            $result = result($closure);

            expect($result)->toBe($expectedResult);
        });

        test('unwraps Success ResultInterface parameter', function (): void {
            $success = new Success($this->testValue);

            $result = result($success);

            expect($result)->toBe($this->testValue);
        });

        test('throws error from Failure ResultInterface parameter', function (): void {
            $failure = new Failure($this->testError);

            $exceptionThrown = false;

            try {
                result($failure);
            } catch (Throwable $e) {
                $exceptionThrown = true;
                expect($e)->toBe($this->testError);
            }

            expect($exceptionThrown)->toBeTrue();
        });

        test('handles different exception types in closures', function (): void {
            $exceptions = [
                ClientError::Validation->exception(),
                NetworkError::Unexpected->exception(),
                new Exception('standard exception'),
                new RuntimeException('runtime error'),
            ];

            foreach ($exceptions as $exception) {
                $closure = function () use ($exception): void {
                    throw $exception;
                };

                $result = result($closure);

                expect($result)->toBeInstanceOf(Failure::class);
                expect($result->err())->toBe($exception);
            }
        });

        test('handles different return types from closures', function (): void {
            $values = [
                'string' => 'test',
                'integer' => 42,
                'float' => 3.14,
                'boolean' => true,
                'array' => ['a', 'b'],
                'object' => (object) ['test' => true],
                'null' => null,
            ];

            foreach ($values as $type => $value) {
                $closure = fn () => $value;
                $result = result($closure);

                expect($result)->toBeInstanceOf(Success::class);
                expect($result->val())->toBe($value);
            }
        });
    });

    describe('unwrap() function', function (): void {
        test('returns value from Success', function (): void {
            $success = new Success($this->testValue);

            $result = unwrap($success);

            expect($result)->toBe($this->testValue);
        });

        test('returns callback result from Failure', function (): void {
            $failure = new Failure($this->testError);

            $result = unwrap($failure, fn () => 'default-value');

            expect($result)->toBe('default-value');
        });

        test('throws from Failure when no callback', function (): void {
            $failure = new Failure($this->testError);

            unwrap($failure);
        })->throws(ClientException::class);
    });

    describe('success() function', function (): void {
        test('returns true and executes callback for Success', function (): void {
            $success = new Success($this->testValue);
            $callbackExecuted = false;
            $receivedValue = null;

            $result = success($success, function ($value) use (&$callbackExecuted, &$receivedValue): void {
                $callbackExecuted = true;
                $receivedValue = $value;
            });

            expect($result)->toBeTrue();
            expect($callbackExecuted)->toBeTrue();
            expect($receivedValue)->toBe($this->testValue);
        });

        test('returns false and does not execute callback for Failure', function (): void {
            $failure = new Failure($this->testError);
            $callbackExecuted = false;

            $result = success($failure, function (): void {
                $callbackExecuted = true;
            });

            expect($result)->toBeFalse();
            expect($callbackExecuted)->toBeFalse();
        });

        test('returns true for Success without callback', function (): void {
            $success = new Success($this->testValue);

            $result = success($success);

            expect($result)->toBeTrue();
        });

        test('returns false for Failure without callback', function (): void {
            $failure = new Failure($this->testError);

            $result = success($failure);

            expect($result)->toBeFalse();
        });

        test('with null callback behaves like no callback', function (): void {
            $success = new Success($this->testValue);
            $failure = new Failure($this->testError);

            expect(success($success, null))->toBeTrue();
            expect(success($failure, null))->toBeFalse();
        });
    });

    describe('failure() function', function (): void {
        test('returns true and executes callback for Failure', function (): void {
            $failure = new Failure($this->testError);
            $callbackExecuted = false;
            $receivedError = null;

            $result = failure($failure, function ($error) use (&$callbackExecuted, &$receivedError): void {
                $callbackExecuted = true;
                $receivedError = $error;
            });

            expect($result)->toBeTrue();
            expect($callbackExecuted)->toBeTrue();
            expect($receivedError)->toBe($this->testError);
        });

        test('returns false and does not execute callback for Success', function (): void {
            $success = new Success($this->testValue);
            $callbackExecuted = false;

            $result = failure($success, function (): void {
                $callbackExecuted = true;
            });

            expect($result)->toBeFalse();
            expect($callbackExecuted)->toBeFalse();
        });

        test('returns true for Failure without callback', function (): void {
            $failure = new Failure($this->testError);

            $result = failure($failure);

            expect($result)->toBeTrue();
        });

        test('returns false for Success without callback', function (): void {
            $success = new Success($this->testValue);

            $result = failure($success);

            expect($result)->toBeFalse();
        });

        test('with null callback behaves like no callback', function (): void {
            $success = new Success($this->testValue);
            $failure = new Failure($this->testError);

            expect(failure($success, null))->toBeFalse();
            expect(failure($failure, null))->toBeTrue();
        });
    });

    describe('ok() function', function (): void {
        test('creates Success with value', function (): void {
            $result = ok($this->testValue);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->val())->toBe($this->testValue);
        });

        test('creates Success with different value types', function (): void {
            $values = [
                'string' => 'test',
                'integer' => 42,
                'float' => 3.14,
                'boolean' => true,
                'array' => ['a', 'b'],
                'object' => (object) ['test' => true],
                'null' => null,
            ];

            foreach ($values as $type => $value) {
                $result = ok($value);

                expect($result)->toBeInstanceOf(Success::class);
                expect($result->val())->toBe($value);
            }
        });
    });

    describe('err() function', function (): void {
        test('creates Failure with error', function (): void {
            $result = err($this->testError);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBe($this->testError);
        });

        test('creates Failure with different error types', function (): void {
            $errors = [
                ClientError::Validation->exception(),
                NetworkError::Unexpected->exception(),
                new Exception('standard exception'),
                new RuntimeException('runtime error'),
            ];

            foreach ($errors as $error) {
                $result = err($error);

                expect($result)->toBeInstanceOf(Failure::class);
                expect($result->err())->toBe($error);
            }
        });
    });

    describe('Results helpers - integration tests', function (): void {
        test('work together in complex scenarios', function (): void {
            $closure = fn (): string => 'success value';

            $successClosure = fn () => 'success value';
            $result1 = result($successClosure);

            $finalValue = null;
            $errorOccurred = false;

            success($result1, function ($value) use (&$finalValue): void {
                $finalValue = $value;
            });

            failure($result1, function () use (&$errorOccurred): void {
                $errorOccurred = true;
            });

            expect($finalValue)->toBe('success value');
            expect($errorOccurred)->toBeFalse();

            $unwrappedValue = unwrap($result1);
            expect($unwrappedValue)->toBe($finalValue);
        });

        test('maintain type safety', function (): void {
            $stringSuccess = ok('string value');
            $intSuccess = ok(42);
            $arraySuccess = ok(['a', 'b', 'c']);

            expect(unwrap($stringSuccess))->toBeString();
            expect(unwrap($intSuccess))->toBeInt();
            expect(unwrap($arraySuccess))->toBeArray();
        });

        test('handle edge cases correctly', function (): void {
            $emptyStringSuccess = ok('');
            $zeroSuccess = ok(0);
            $falseSuccess = ok(false);
            $nullSuccess = ok(null);

            expect(success($emptyStringSuccess))->toBeTrue();
            expect(success($zeroSuccess))->toBeTrue();
            expect(success($falseSuccess))->toBeTrue();
            expect(success($nullSuccess))->toBeTrue();

            expect(unwrap($emptyStringSuccess))->toBe('');
            expect(unwrap($zeroSuccess))->toBe(0);
            expect(unwrap($falseSuccess))->toBeFalse();
            expect(unwrap($nullSuccess))->toBeNull();
        });
    });

    // ==============================================================================
    // Requests Helpers Tests
    // ==============================================================================

    describe('write() function', function (): void {
        test('writes single tuple successfully', function (): void {
            $storeId = 'store-abc';
            $model = test()->createMock(AuthorizationModelInterface::class);
            $tuple = new TupleKey('user:anne', 'viewer', 'document:roadmap');

            $response = test()->createMock(WriteTuplesResponseInterface::class);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('writeTuples')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($model),
                    $this->isInstanceOf(TupleKeys::class),
                    $this->isNull(),
                )
                ->willReturn(new Success($response));

            write($tuple, $client, $storeId, $model);

            // If we get here without throwing, the write was successful
            expect(true)->toBe(true);
        });

        test('writes TupleKeys collection successfully', function (): void {
            $storeId = 'store-def';
            $model = test()->createMock(AuthorizationModelInterface::class);
            $tuples = new TupleKeys([
                new TupleKey('user:anne', 'viewer', 'document:1'),
                new TupleKey('user:bob', 'editor', 'document:2'),
            ]);

            $response = test()->createMock(WriteTuplesResponseInterface::class);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('writeTuples')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($model),
                    $this->equalTo($tuples),
                    $this->isNull(),
                )
                ->willReturn(new Success($response));

            write($tuples, $client, $storeId, $model);

            // If we get here without throwing, the write was successful
            expect(true)->toBe(true);
        });

        test('does not call unwrap when writes succeed', function (): void {
            $storeId = 'store-fail';
            $model = test()->createMock(AuthorizationModelInterface::class);
            $tuple = new TupleKey('user:anne', 'viewer', 'document:roadmap');

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('writeTuples')
                ->willReturn(new Success(test()->createMock(WriteTuplesResponseInterface::class)));

            // The function doesn't call unwrap() anymore, so it won't throw
            write($tuple, $client, $storeId, $model);

            expect(true)->toBe(true);
        });

        test('handles different tuple formats', function (): void {
            $storeId = 'store-123';
            $model = test()->createMock(AuthorizationModelInterface::class);
            $testCases = [
                new TupleKey('user:*', 'viewer', 'document:public'),
                new TupleKey('group:admins#member', 'editor', 'folder:root'),
                new TupleKey('folder:parent#viewer', 'viewer', 'document:child'),
                tuple('application:client', 'can_read', 'resource:data'),
            ];

            foreach ($testCases as $testTuple) {
                $response = test()->createMock(WriteTuplesResponseInterface::class);

                $client = test()->createMock(ClientInterface::class);
                $client->expects($this->once())
                    ->method('writeTuples')
                    ->willReturn(new Success($response));

                write($testTuple, $client, $storeId, $model);
            }

            expect(true)->toBe(true);
        });
    });

    describe('delete() function', function (): void {
        test('deletes single tuple successfully', function (): void {
            $storeId = 'store-abc';
            $modelId = 'model-xyz';
            $tuple = new TupleKey('user:anne', 'viewer', 'document:roadmap');

            $response = test()->createMock(WriteTuplesResponseInterface::class);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('writeTuples')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($modelId),
                    $this->isNull(),
                    $this->isInstanceOf(TupleKeys::class),
                )
                ->willReturn(new Success($response));

            delete($tuple, $client, $storeId, $modelId);

            // If we get here without throwing, the delete was successful
            expect(true)->toBe(true);
        });

        test('deletes TupleKeys collection successfully', function (): void {
            $storeId = 'store-def';
            $modelId = 'model-uvw';
            $tuples = new TupleKeys([
                new TupleKey('user:anne', 'viewer', 'document:1'),
                new TupleKey('user:bob', 'editor', 'document:2'),
            ]);

            $response = test()->createMock(WriteTuplesResponseInterface::class);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('writeTuples')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($modelId),
                    $this->isNull(),
                    $this->equalTo($tuples),
                )
                ->willReturn(new Success($response));

            delete($tuples, $client, $storeId, $modelId);

            // If we get here without throwing, the delete was successful
            expect(true)->toBe(true);
        });

        test('does not call unwrap when deletes succeed', function (): void {
            $storeId = 'store-fail';
            $modelId = 'model-fail';
            $tuple = new TupleKey('user:anne', 'viewer', 'document:roadmap');

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('writeTuples')
                ->willReturn(new Success(test()->createMock(WriteTuplesResponseInterface::class)));

            // The function doesn't call unwrap() anymore, so it won't throw
            delete($tuple, $client, $storeId, $modelId);

            expect(true)->toBe(true);
        });

        test('handles batch deletes', function (): void {
            $storeId = 'store-batch';
            $modelId = 'model-batch';
            $tuples = new TupleKeys([]);

            // Add 100 tuples for batch delete
            for ($i = 0; 100 > $i; ++$i) {
                $tuples->add(new TupleKey("user:user{$i}", 'viewer', "document:doc{$i}"));
            }

            $response = test()->createMock(WriteTuplesResponseInterface::class);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('writeTuples')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($modelId),
                    $this->isNull(),
                    $this->callback(fn ($deletes) => $deletes instanceof TupleKeys && 100 === $deletes->count()),
                )
                ->willReturn(new Success($response));

            delete($tuples, $client, $storeId, $modelId);

            // If we get here without throwing, the batch delete was successful
            expect(true)->toBe(true);
        });
    });

    describe('read() function', function (): void {
        test('reads all tuples with automatic pagination', function (): void {
            $storeId = 'store-read';

            // Mock first page response
            $firstPageResponse = test()->createMock(ReadTuplesResponseInterface::class);
            $firstPageResponse->method('getTuples')->willReturn(new Tuples([
                new Tuple(new TupleKey('user:anne', 'viewer', 'document:1'), new DateTimeImmutable),
                new Tuple(new TupleKey('user:bob', 'viewer', 'document:2'), new DateTimeImmutable),
            ]));
            $firstPageResponse->method('getContinuationToken')->willReturn('page-2-token');

            // Mock second page response
            $secondPageResponse = test()->createMock(ReadTuplesResponseInterface::class);
            $secondPageResponse->method('getTuples')->willReturn(new Tuples([
                new Tuple(new TupleKey('user:charlie', 'viewer', 'document:3'), new DateTimeImmutable),
            ]));
            $secondPageResponse->method('getContinuationToken')->willReturn(null);

            $client = test()->createStub(ClientInterface::class);
            $client->method('readTuples')
                ->willReturnOnConsecutiveCalls(
                    new Success($firstPageResponse),
                    new Success($secondPageResponse),
                );

            $allTuples = read($client, $storeId);

            expect($allTuples)->toHaveCount(3);
            expect($allTuples[0])->toBeInstanceOf(TupleKeyInterface::class);
            expect($allTuples[0]->getUser())->toBe('user:anne');
            expect($allTuples[1]->getUser())->toBe('user:bob');
            expect($allTuples[2]->getUser())->toBe('user:charlie');
        });

        test('reads tuples with filtering', function (): void {
            $storeId = 'store-filter';
            $filterTuple = new TupleKey('user:anne', '', '');

            $response = test()->createMock(ReadTuplesResponseInterface::class);
            $response->method('getTuples')->willReturn(new Tuples([
                new Tuple(new TupleKey('user:anne', 'viewer', 'document:1'), new DateTimeImmutable),
                new Tuple(new TupleKey('user:anne', 'editor', 'document:2'), new DateTimeImmutable),
            ]));
            $response->method('getContinuationToken')->willReturn(null);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('readTuples')
                ->with(
                    store: $storeId,
                    tuple: $filterTuple,
                    continuationToken: null,
                    pageSize: 50,
                    consistency: null,
                )
                ->willReturn(new Success($response));

            $allTuples = read($client, $storeId, $filterTuple);

            expect($allTuples)->toHaveCount(2);
            expect($allTuples[0]->getUser())->toBe('user:anne');
            expect($allTuples[1]->getUser())->toBe('user:anne');
        });

        test('handles empty results', function (): void {
            $storeId = 'store-empty';

            $response = test()->createMock(ReadTuplesResponseInterface::class);
            $response->method('getTuples')->willReturn(new Tuples([]));
            $response->method('getContinuationToken')->willReturn(null);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('readTuples')
                ->willReturn(new Success($response));

            $allTuples = read($client, $storeId);

            expect($allTuples)->toHaveCount(0);
            expect($allTuples)->toBe([]);
        });

        test('respects custom page size', function (): void {
            $storeId = 'store-pagesize';
            $customPageSize = 25;

            $response = test()->createMock(ReadTuplesResponseInterface::class);
            $response->method('getTuples')->willReturn(new Tuples([]));
            $response->method('getContinuationToken')->willReturn(null);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('readTuples')
                ->with(
                    store: $storeId,
                    tupleKey: null,
                    continuationToken: null,
                    pageSize: $customPageSize,
                    consistency: null,
                )
                ->willReturn(new Success($response));

            read($client, $storeId, pageSize: $customPageSize);
        });

        test('handles consistency parameter', function (): void {
            $storeId = 'store-consistency';
            $consistency = Consistency::HIGHER_CONSISTENCY;

            $response = test()->createMock(ReadTuplesResponseInterface::class);
            $response->method('getTuples')->willReturn(new Tuples([]));
            $response->method('getContinuationToken')->willReturn(null);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('readTuples')
                ->with(
                    store: $storeId,
                    tupleKey: null,
                    continuationToken: null,
                    pageSize: 50,
                    consistency: $consistency,
                )
                ->willReturn(new Success($response));

            read($client, $storeId, consistency: $consistency);
        });
    });

    describe('changes() function', function (): void {
        test('lists all changes with automatic pagination', function (): void {
            $storeId = 'store-changes';

            // Mock first page response
            $firstPageResponse = test()->createMock(ListTupleChangesResponseInterface::class);
            $firstPageResponse->method('getChanges')->willReturn(new TupleChanges([
                new TupleChange(
                    new TupleKey('user:anne', 'viewer', 'document:1'),
                    TupleOperation::TUPLE_OPERATION_WRITE,
                    new DateTimeImmutable('2024-01-01 10:00:00'),
                ),
                new TupleChange(
                    new TupleKey('user:bob', 'viewer', 'document:2'),
                    TupleOperation::TUPLE_OPERATION_WRITE,
                    new DateTimeImmutable('2024-01-01 11:00:00'),
                ),
            ]));
            $firstPageResponse->method('getContinuationToken')->willReturn('page-2-token');

            // Mock second page response
            $secondPageResponse = test()->createMock(ListTupleChangesResponseInterface::class);
            $secondPageResponse->method('getChanges')->willReturn(new TupleChanges([
                new TupleChange(
                    new TupleKey('user:charlie', 'editor', 'document:3'),
                    TupleOperation::TUPLE_OPERATION_DELETE,
                    new DateTimeImmutable('2024-01-01 12:00:00'),
                ),
            ]));
            $secondPageResponse->method('getContinuationToken')->willReturn(null);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->exactly(2))
                ->method('listTupleChanges')
                ->willReturnOnConsecutiveCalls(
                    new Success($firstPageResponse),
                    new Success($secondPageResponse),
                );

            $allChanges = changes($client, $storeId);

            expect($allChanges)->toHaveCount(3);
            expect($allChanges[0])->toBeInstanceOf(TupleChangeInterface::class);
            expect($allChanges[0]->getTupleKey()->getUser())->toBe('user:anne');
            expect($allChanges[0]->getOperation())->toBe(TupleOperation::TUPLE_OPERATION_WRITE);
            expect($allChanges[1]->getTupleKey()->getUser())->toBe('user:bob');
            expect($allChanges[2]->getTupleKey()->getUser())->toBe('user:charlie');
            expect($allChanges[2]->getOperation())->toBe(TupleOperation::TUPLE_OPERATION_DELETE);
        });

        test('lists changes with type filter', function (): void {
            $storeId = 'store-filter';
            $type = 'document';

            $response = test()->createMock(ListTupleChangesResponseInterface::class);
            $response->method('getChanges')->willReturn(new TupleChanges([
                new TupleChange(
                    new TupleKey('user:anne', 'viewer', 'document:1'),
                    TupleOperation::TUPLE_OPERATION_WRITE,
                    new DateTimeImmutable('2024-01-01 10:00:00'),
                ),
            ]));
            $response->method('getContinuationToken')->willReturn(null);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('listTupleChanges')
                ->with(
                    store: $storeId,
                    continuationToken: null,
                    pageSize: 50,
                    type: $type,
                    startTime: null,
                )
                ->willReturn(new Success($response));

            $allChanges = changes($client, $storeId, type: $type);

            expect($allChanges)->toHaveCount(1);
            expect($allChanges[0]->getTupleKey()->getObject())->toBe('document:1');
        });

        test('lists changes with start time filter', function (): void {
            $storeId = 'store-time';
            $startTime = new DateTimeImmutable('2024-01-01 09:00:00');

            $response = test()->createMock(ListTupleChangesResponseInterface::class);
            $response->method('getChanges')->willReturn(new TupleChanges([
                new TupleChange(
                    new TupleKey('user:anne', 'viewer', 'document:1'),
                    TupleOperation::TUPLE_OPERATION_WRITE,
                    new DateTimeImmutable('2024-01-01 10:00:00'),
                ),
            ]));
            $response->method('getContinuationToken')->willReturn(null);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('listTupleChanges')
                ->with(
                    store: $storeId,
                    continuationToken: null,
                    pageSize: 50,
                    type: null,
                    startTime: $startTime,
                )
                ->willReturn(new Success($response));

            $allChanges = changes($client, $storeId, startTime: $startTime);

            expect($allChanges)->toHaveCount(1);
            expect($allChanges[0]->getTimestamp())->toEqual($startTime->modify('+1 hour'));
        });

        test('handles empty results', function (): void {
            $storeId = 'store-empty';

            $response = test()->createMock(ListTupleChangesResponseInterface::class);
            $response->method('getChanges')->willReturn(new TupleChanges([]));
            $response->method('getContinuationToken')->willReturn(null);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('listTupleChanges')
                ->willReturn(new Success($response));

            $allChanges = changes($client, $storeId);

            expect($allChanges)->toHaveCount(0);
            expect($allChanges)->toBe([]);
        });

        test('respects custom page size', function (): void {
            $storeId = 'store-pagesize';
            $customPageSize = 25;

            $response = test()->createMock(ListTupleChangesResponseInterface::class);
            $response->method('getChanges')->willReturn(new TupleChanges([]));
            $response->method('getContinuationToken')->willReturn(null);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('listTupleChanges')
                ->with(
                    store: $storeId,
                    continuationToken: null,
                    pageSize: $customPageSize,
                    type: null,
                    startTime: null,
                )
                ->willReturn(new Success($response));

            changes($client, $storeId, pageSize: $customPageSize);
        });

        test('validates page size is positive', function (): void {
            $storeId = 'store-validation';

            $response = test()->createMock(ListTupleChangesResponseInterface::class);
            $response->method('getChanges')->willReturn(new TupleChanges([]));
            $response->method('getContinuationToken')->willReturn(null);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('listTupleChanges')
                ->with(
                    store: $storeId,
                    continuationToken: null,
                    pageSize: 50, // Should default to 50 when negative value provided
                    type: null,
                    startTime: null,
                )
                ->willReturn(new Success($response));

            changes($client, $storeId, pageSize: -10); // Invalid page size
        });

        test('combines type and start time filters', function (): void {
            $storeId = 'store-combined';
            $type = 'document';
            $startTime = new DateTimeImmutable('2024-01-01 09:00:00');

            $response = test()->createMock(ListTupleChangesResponseInterface::class);
            $response->method('getChanges')->willReturn(new TupleChanges([
                new TupleChange(
                    new TupleKey('user:anne', 'viewer', 'document:1'),
                    TupleOperation::TUPLE_OPERATION_WRITE,
                    new DateTimeImmutable('2024-01-01 10:00:00'),
                ),
            ]));
            $response->method('getContinuationToken')->willReturn(null);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('listTupleChanges')
                ->with(
                    store: $storeId,
                    continuationToken: null,
                    pageSize: 50,
                    type: $type,
                    startTime: $startTime,
                )
                ->willReturn(new Success($response));

            $allChanges = changes($client, $storeId, type: $type, startTime: $startTime);

            expect($allChanges)->toHaveCount(1);
            expect($allChanges[0]->getTupleKey()->getObject())->toBe('document:1');
        });
    });

    describe('allowed() function', function (): void {
        test('returns true when check is allowed', function (): void {
            $storeId = 'store-check';
            $modelId = 'model-check';
            $tuple = new TupleKey('user:anne', 'viewer', 'document:roadmap');

            $response = test()->createMock(CheckResponseInterface::class);
            $response->method('getAllowed')->willReturn(true);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('check')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($modelId),
                    $this->equalTo($tuple),
                    $this->isNull(),
                    $this->isNull(),
                    $this->isNull(),
                    $this->isNull(),
                )
                ->willReturn(new Success($response));

            $result = allowed($tuple, client: $client, store: $storeId, model: $modelId);

            expect($result)->toBe(true);
        });

        test('returns false when check is not allowed', function (): void {
            $storeId = 'store-check';
            $modelId = 'model-check';
            $tuple = new TupleKey('user:bob', 'owner', 'document:confidential');

            $response = test()->createMock(CheckResponseInterface::class);
            $response->method('getAllowed')->willReturn(false);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('check')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($modelId),
                    $this->equalTo($tuple),
                    $this->isNull(),
                    $this->isNull(),
                    $this->isNull(),
                    $this->isNull(),
                )
                ->willReturn(new Success($response));

            $result = allowed($tuple, client: $client, store: $storeId, model: $modelId);

            expect($result)->toBe(false);
        });

        test('returns false when check fails', function (): void {
            $storeId = 'store-fail';
            $modelId = 'model-fail';
            $tuple = new TupleKey('user:anne', 'viewer', 'document:roadmap');
            $exception = new Exception('Check operation failed');

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('check')
                ->willReturn(new Failure($exception));

            $result = allowed($tuple, client: $client, store: $storeId, model: $modelId);

            expect($result)->toBe(false);
        });

        test('works with tuple helper function', function (): void {
            $storeId = 'store-helper';
            $modelId = 'model-helper';
            $tuple = tuple('user:charlie', 'editor', 'folder:shared');

            $response = test()->createMock(CheckResponseInterface::class);
            $response->method('getAllowed')->willReturn(true);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('check')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($modelId),
                    $this->equalTo($tuple),
                    $this->isNull(),
                    $this->isNull(),
                    $this->isNull(),
                    $this->isNull(),
                )
                ->willReturn(new Success($response));

            $result = allowed($tuple, client: $client, store: $storeId, model: $modelId);

            expect($result)->toBe(true);
        });

        test('handles different permission checks', function (): void {
            $storeId = 'store-perms';
            $modelId = 'model-perms';
            $testCases = [
                ['user:anne', 'can_read', 'resource:data', true],
                ['user:bob', 'can_write', 'resource:data', false],
                ['user:charlie', 'can_delete', 'resource:data', false],
                ['group:admins#member', 'admin', 'system:config', true],
                ['user:*', 'viewer', 'document:public', true],
            ];

            foreach ($testCases as [$user, $relation, $object, $expectedResult]) {
                $tuple = new TupleKey($user, $relation, $object);

                $response = test()->createMock(CheckResponseInterface::class);
                $response->method('getAllowed')->willReturn($expectedResult);

                $client = test()->createMock(ClientInterface::class);
                $client->expects($this->once())
                    ->method('check')
                    ->willReturn(new Success($response));

                $result = allowed($tuple, client: $client, store: $storeId, model: $modelId);

                expect($result)->toBe($expectedResult);
            }
        });

        test('works with individual parameters', function (): void {
            $storeId = 'store-individual';
            $modelId = 'model-individual';

            $response = test()->createMock(CheckResponseInterface::class);
            $response->method('getAllowed')->willReturn(true);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('check')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($modelId),
                    $this->callback(fn ($tupleKey) => $tupleKey instanceof TupleKeyInterface
                            && 'user:anne' === $tupleKey->getUser()
                            && 'viewer' === $tupleKey->getRelation()
                            && 'document:budget' === $tupleKey->getObject()),
                    $this->isNull(),
                    $this->isNull(),
                    $this->isNull(),
                    $this->isNull(),
                )
                ->willReturn(new Success($response));

            $result = allowed(user: 'user:anne', relation: 'viewer', object: 'document:budget', client: $client, store: $storeId, model: $modelId);

            expect($result)->toBe(true);
        });

        test('works with individual parameters and condition', function (): void {
            $storeId = 'store-condition';
            $modelId = 'model-condition';
            $condition = new Condition(
                name: 'inRegion',
                expression: 'params.region == "us-east"',
                parameters: new ConditionParameters([
                    new ConditionParameter(typeName: TypeName::STRING),
                ]),
            );

            $response = test()->createMock(CheckResponseInterface::class);
            $response->method('getAllowed')->willReturn(true);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('check')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($modelId),
                    $this->callback(fn ($tupleKey) => $tupleKey instanceof TupleKeyInterface
                            && 'user:bob' === $tupleKey->getUser()
                            && 'editor' === $tupleKey->getRelation()
                            && 'document:regional' === $tupleKey->getObject()
                            && $tupleKey->getCondition() === $condition),
                    $this->isNull(),
                    $this->isNull(),
                    $this->isNull(),
                    $this->isNull(),
                )
                ->willReturn(new Success($response));

            $result = allowed(user: 'user:bob', relation: 'editor', object: 'document:regional', condition: $condition, client: $client, store: $storeId, model: $modelId);

            expect($result)->toBe(true);
        });

        test('works with individual parameters and all options', function (): void {
            $storeId = 'store-full';
            $modelId = 'model-full';
            $contextualTuples = tuples(
                tuple('user:charlie', 'member', 'team:finance'),
            );
            $context = (object) ['time' => '10:00', 'department' => 'finance'];

            $response = test()->createMock(CheckResponseInterface::class);
            $response->method('getAllowed')->willReturn(false);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('check')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($modelId),
                    $this->callback(fn ($tupleKey) => $tupleKey instanceof TupleKeyInterface
                            && 'user:charlie' === $tupleKey->getUser()
                            && 'owner' === $tupleKey->getRelation()
                            && 'document:secret' === $tupleKey->getObject()),
                    $this->equalTo(true),
                    $this->equalTo($context),
                    $this->equalTo($contextualTuples),
                    $this->equalTo(Consistency::HIGHER_CONSISTENCY),
                )
                ->willReturn(new Success($response));

            $result = allowed(
                user: 'user:charlie',
                relation: 'owner',
                object: 'document:secret',
                trace: true,
                context: $context,
                contextualTuples: $contextualTuples,
                consistency: Consistency::HIGHER_CONSISTENCY,
                client: $client,
                store: $storeId,
                model: $modelId,
            );

            expect($result)->toBe(false);
        });

        test('returns false when individual parameters are incomplete', function (): void {
            $storeId = 'store-incomplete';
            $modelId = 'model-incomplete';

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->never())->method('check');

            $result = allowed(user: 'user:anne', object: 'document:budget', client: $client, store: $storeId, model: $modelId);

            expect($result)->toBe(false);
        });

        test('returns false when no tuple or individual parameters provided', function (): void {
            $storeId = 'store-empty';
            $modelId = 'model-empty';

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->never())->method('check');

            $result = allowed(client: $client, store: $storeId, model: $modelId);

            expect($result)->toBe(false);
        });
    });

    describe('model() function', function (): void {
        test('creates authorization model successfully', function (): void {
            $storeId = 'store-model';
            $expectedModelId = 'model-12345';

            $typeDefinitions = new TypeDefinitions([]);
            $conditions = new Conditions([]);

            $authModel = test()->createMock(AuthorizationModelInterface::class);
            $authModel->method('getTypeDefinitions')->willReturn($typeDefinitions);
            $authModel->method('getConditions')->willReturn($conditions);
            $authModel->method('getSchemaVersion')->willReturn(SchemaVersion::V1_1);

            $response = test()->createMock(CreateAuthorizationModelResponseInterface::class);
            $response->method('getModel')->willReturn($expectedModelId);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('createAuthorizationModel')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($typeDefinitions),
                    $this->equalTo($conditions),
                    $this->equalTo(SchemaVersion::V1_1),
                )
                ->willReturn(new Success($response));

            $modelId = model($authModel, $client, $storeId);

            expect($modelId)->toBe($expectedModelId);
        });

        test('throws exception when model creation fails', function (): void {
            $storeId = 'store-fail';
            $exception = new Exception('Model creation failed');

            $typeDefinitions = new TypeDefinitions([]);
            $conditions = new Conditions([]);

            $authModel = test()->createMock(AuthorizationModelInterface::class);
            $authModel->method('getTypeDefinitions')->willReturn($typeDefinitions);
            $authModel->method('getConditions')->willReturn($conditions);
            $authModel->method('getSchemaVersion')->willReturn(SchemaVersion::V1_1);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('createAuthorizationModel')
                ->willReturn(new Failure($exception));

            model($authModel, $client, $storeId);
        })->throws(Exception::class, 'Model creation failed');

        test('handles model without conditions', function (): void {
            $storeId = 'store-no-conditions';
            $expectedModelId = 'model-xyz';

            $typeDefinitions = new TypeDefinitions([]);

            $authModel = test()->createMock(AuthorizationModelInterface::class);
            $authModel->method('getTypeDefinitions')->willReturn($typeDefinitions);
            $authModel->method('getConditions')->willReturn(null);
            $authModel->method('getSchemaVersion')->willReturn(SchemaVersion::V1_1);

            $response = test()->createMock(CreateAuthorizationModelResponseInterface::class);
            $response->method('getModel')->willReturn($expectedModelId);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('createAuthorizationModel')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($typeDefinitions),
                    $this->isNull(),
                    $this->equalTo(SchemaVersion::V1_1),
                )
                ->willReturn(new Success($response));

            $modelId = model($authModel, $client, $storeId);

            expect($modelId)->toBe($expectedModelId);
        });

        test('handles different schema versions', function (): void {
            $storeId = 'store-schema';
            $expectedModelId = 'model-schema';

            $typeDefinitions = new TypeDefinitions([]);
            $conditions = new Conditions([]);

            $authModel = test()->createMock(AuthorizationModelInterface::class);
            $authModel->method('getTypeDefinitions')->willReturn($typeDefinitions);
            $authModel->method('getConditions')->willReturn($conditions);
            $authModel->method('getSchemaVersion')->willReturn(SchemaVersion::V1_0);

            $response = test()->createMock(CreateAuthorizationModelResponseInterface::class);
            $response->method('getModel')->willReturn($expectedModelId);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('createAuthorizationModel')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($typeDefinitions),
                    $this->equalTo($conditions),
                    $this->equalTo(SchemaVersion::V1_0),
                )
                ->willReturn(new Success($response));

            $modelId = model($authModel, $client, $storeId);

            expect($modelId)->toBe($expectedModelId);
        });

        test('returns the created model ID', function (): void {
            $storeId = 'store-return';
            $expectedModelIds = [
                'model-uuid-12345',
                'model-custom-id',
                'model-timestamp-1234567890',
                '01234567-89ab-cdef-0123-456789abcdef',
            ];

            foreach ($expectedModelIds as $expectedModelId) {
                $typeDefinitions = new TypeDefinitions([]);

                $authModel = test()->createMock(AuthorizationModelInterface::class);
                $authModel->method('getTypeDefinitions')->willReturn($typeDefinitions);
                $authModel->method('getConditions')->willReturn(null);
                $authModel->method('getSchemaVersion')->willReturn(SchemaVersion::V1_1);

                $response = test()->createMock(CreateAuthorizationModelResponseInterface::class);
                $response->method('getModel')->willReturn($expectedModelId);

                $client = test()->createMock(ClientInterface::class);
                $client->expects($this->once())
                    ->method('createAuthorizationModel')
                    ->willReturn(new Success($response));

                $modelId = model($authModel, $client, $storeId);

                expect($modelId)->toBe($expectedModelId);
            }
        });
    });

    // ==============================================================================
    // Language Helpers Tests
    // ==============================================================================

    describe('lang() function', function (): void {
        test('returns default language when no locale provided', function (): void {
            $result = lang();

            expect($result)->toBeInstanceOf(Language::class);
            expect($result)->toBe(Language::English);
            expect($result->value)->toBe('en');
        });

        test('returns correct language for valid locale codes', function (): void {
            $testCases = [
                'en' => Language::English,
                'de' => Language::German,
                'es' => Language::Spanish,
                'fr' => Language::French,
                'it' => Language::Italian,
                'ja' => Language::Japanese,
                'ko' => Language::Korean,
                'nl' => Language::Dutch,
                'pt_BR' => Language::PortugueseBrazilian,
                'ru' => Language::Russian,
                'sv' => Language::Swedish,
                'tr' => Language::Turkish,
                'uk' => Language::Ukrainian,
                'zh_CN' => Language::ChineseSimplified,
            ];

            foreach ($testCases as $locale => $expectedLanguage) {
                $result = lang($locale);

                expect($result)->toBe($expectedLanguage);
                expect($result->value)->toBe($locale);
            }
        });

        test('handles hyphenated locale codes', function (): void {
            $testCases = [
                'pt-BR' => Language::PortugueseBrazilian,
                'zh-CN' => Language::ChineseSimplified,
            ];

            foreach ($testCases as $locale => $expectedLanguage) {
                $result = lang($locale);

                expect($result)->toBe($expectedLanguage);
            }
        });

        test('returns default language for invalid locale codes', function (): void {
            $invalidLocales = [
                'invalid',
                'xyz',
                'pt_PT',  // Portuguese Portugal not supported
                'zh_TW',  // Traditional Chinese not supported
                'es_ES',  // Spanish Spain not supported
                '',
                '123',
                'zz_ZZ',
            ];

            foreach ($invalidLocales as $invalidLocale) {
                $result = lang($invalidLocale);

                expect($result)->toBe(Language::English);
                expect($result->value)->toBe('en');
            }
        });

        test('handles null input gracefully', function (): void {
            $result = lang(null);

            expect($result)->toBe(Language::English);
            expect($result->value)->toBe('en');
        });

        test('is case sensitive for locale codes', function (): void {
            $testCases = [
                'EN' => Language::English,  // Should fallback to default
                'De' => Language::English,  // Should fallback to default
                'PT_br' => Language::English,  // Should fallback to default
                'zh_cn' => Language::English,  // Should fallback to default
            ];

            foreach ($testCases as $locale => $expectedLanguage) {
                $result = lang($locale);

                expect($result)->toBe($expectedLanguage);
            }
        });

        test('works with language methods', function (): void {
            $german = lang('de');

            expect($german->displayName())->toBe('German');
            expect($german->nativeName())->toBe('Deutsch');
            expect($german->isoCode())->toBe('de');
            expect($german->regionCode())->toBeNull();
            expect($german->locale())->toBe('de');
            expect($german->isRightToLeft())->toBeFalse();
        });

        test('works with regional variants', function (): void {
            $brazilianPortuguese = lang('pt_BR');

            expect($brazilianPortuguese->displayName())->toBe('Portuguese (Brazilian)');
            expect($brazilianPortuguese->nativeName())->toBe('Português (Brasil)');
            expect($brazilianPortuguese->isoCode())->toBe('pt');
            expect($brazilianPortuguese->regionCode())->toBe('BR');
            expect($brazilianPortuguese->locale())->toBe('pt_BR');
        });

        test('provides convenience for client configuration', function (): void {
            // Simulate how it would be used in Client constructor
            $testCases = [
                ['lang' => lang(), 'expected' => 'en'],                    // Default
                ['lang' => lang('de'), 'expected' => 'de'],               // German
                ['lang' => lang('pt_BR'), 'expected' => 'pt_BR'],         // Brazilian Portuguese
                ['lang' => lang('invalid'), 'expected' => 'en'],          // Fallback to default
            ];

            foreach ($testCases as $case) {
                expect($case['lang']->value)->toBe($case['expected']);
            }
        });

        test('maintains type safety', function (): void {
            $result = lang('fr');

            expect($result)->toBeInstanceOf(Language::class);

            // Test that we can call enum methods
            expect($result->cases())->toBeArray();
            expect($result::default())->toBeInstanceOf(Language::class);
        });

        test('works in practical scenarios', function (): void {
            // Test a realistic workflow
            $userPreferredLanguage = 'de';
            $clientLanguage = lang($userPreferredLanguage);

            expect($clientLanguage)->toBe(Language::German);

            // Test fallback scenario
            $unsupportedLanguage = 'ar';  // Arabic not supported
            $fallbackLanguage = lang($unsupportedLanguage);

            expect($fallbackLanguage)->toBe(Language::English);

            // Test empty/null scenario
            $defaultLanguage = lang();

            expect($defaultLanguage)->toBe(Language::English);
        });
    });

    // ==============================================================================
    // Combined/Integration Tests
    // ==============================================================================

    describe('helper function integration', function (): void {
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

        test('write then check workflow', function (): void {
            $storeId = 'store-workflow';
            $modelId = 'model-workflow';
            $model = test()->createMock(AuthorizationModelInterface::class);
            $tuple = tuple('user:anne', 'editor', 'document:proposal');

            // First, write the tuple
            $writeResponse = test()->createMock(WriteTuplesResponseInterface::class);

            // Then, check if it's allowed
            $checkResponse = test()->createMock(CheckResponseInterface::class);
            $checkResponse->method('getAllowed')->willReturn(true);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('writeTuples')
                ->willReturn(new Success($writeResponse));
            $client->expects($this->once())
                ->method('check')
                ->willReturn(new Success($checkResponse));

            write($tuple, $client, $storeId, $model);
            $isAllowed = allowed($tuple, client: $client, store: $storeId, model: $modelId);

            expect($isAllowed)->toBe(true);
        });

        test('delete then check workflow', function (): void {
            $storeId = 'store-workflow';
            $modelId = 'model-workflow';
            $tuple = tuple('user:bob', 'viewer', 'document:old');

            // First, delete the tuple
            $deleteResponse = test()->createMock(WriteTuplesResponseInterface::class);

            // Then, check if it's still allowed (should be false)
            $checkResponse = test()->createMock(CheckResponseInterface::class);
            $checkResponse->method('getAllowed')->willReturn(false);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('writeTuples')
                ->willReturn(new Success($deleteResponse));
            $client->expects($this->once())
                ->method('check')
                ->willReturn(new Success($checkResponse));

            delete($tuple, $client, $storeId, $modelId);
            $isAllowed = allowed($tuple, client: $client, store: $storeId, model: $modelId);

            expect($isAllowed)->toBe(false);
        });
    });

    describe('users() function', function (): void {
        test('returns array of users when successful', function (): void {
            $storeId = 'store-users';
            $modelId = 'model-users';
            $object = 'document:roadmap';
            $relation = 'viewer';

            $userFilters = test()->createMock(UserTypeFiltersInterface::class);

            // Create User objects with string object values
            $users = [
                new User(object: 'user:anne'),
                new User(object: 'user:bob'),
                new User(object: 'user:charlie'),
            ];
            $usersCollection = new Users($users);

            $response = test()->createMock(ListUsersResponseInterface::class);
            $response->method('getUsers')->willReturn($usersCollection);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('listUsers')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($modelId),
                    $this->equalTo($object),
                    $this->equalTo($relation),
                    $this->equalTo($userFilters),
                    $this->isNull(),
                    $this->isNull(),
                    $this->isNull(),
                )
                ->willReturn(new Success($response));

            $result = \OpenFGA\users($object, $relation, $userFilters, $client, $storeId, $modelId);

            expect($result)->toBeArray();
            expect($result)->toHaveCount(3);
            expect($result)->toBe(['user:anne', 'user:bob', 'user:charlie']);
        });

        test('returns empty array when no users found', function (): void {
            $storeId = 'store-empty';
            $modelId = 'model-empty';
            $object = 'document:private';
            $relation = 'owner';

            $userFilters = test()->createMock(UserTypeFiltersInterface::class);

            // Create an empty users collection
            $usersCollection = new Users([]);

            $response = test()->createMock(ListUsersResponseInterface::class);
            $response->method('getUsers')->willReturn($usersCollection);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('listUsers')
                ->willReturn(new Success($response));

            $result = \OpenFGA\users($object, $relation, $userFilters, $client, $storeId, $modelId);

            expect($result)->toBeArray();
            expect($result)->toBeEmpty();
        });

        test('returns empty array when operation fails', function (): void {
            $storeId = 'store-fail';
            $modelId = 'model-fail';
            $object = 'document:error';
            $relation = 'viewer';

            $userFilters = test()->createMock(UserTypeFiltersInterface::class);
            $exception = new Exception('List users operation failed');

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('listUsers')
                ->willReturn(new Failure($exception));

            $result = \OpenFGA\users($object, $relation, $userFilters, $client, $storeId, $modelId);

            expect($result)->toBeArray();
            expect($result)->toBeEmpty();
        });

        test('works with optional parameters', function (): void {
            $storeId = 'store-optional';
            $modelId = 'model-optional';
            $object = 'folder:shared';
            $relation = 'editor';

            $userFilters = test()->createMock(UserTypeFiltersInterface::class);
            $context = (object) ['time' => '2024-01-01'];
            $contextualTuples = test()->createMock(TupleKeysInterface::class);
            $consistency = Consistency::MINIMIZE_LATENCY;

            // Create users collection with one user
            $users = [new User(object: 'user:dave')];
            $usersCollection = new Users($users);

            $response = test()->createMock(ListUsersResponseInterface::class);
            $response->method('getUsers')->willReturn($usersCollection);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('listUsers')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($modelId),
                    $this->equalTo($object),
                    $this->equalTo($relation),
                    $this->equalTo($userFilters),
                    $this->equalTo($context),
                    $this->equalTo($contextualTuples),
                    $this->equalTo($consistency),
                )
                ->willReturn(new Success($response));

            $result = \OpenFGA\users(
                $object,
                $relation,
                $userFilters,
                $client,
                $storeId,
                $modelId,
                $context,
                $contextualTuples,
                $consistency,
            );

            expect($result)->toBeArray();
            expect($result)->toHaveCount(1);
            expect($result)->toBe(['user:dave']);
        });

        test('handles mixed user types in response', function (): void {
            $storeId = 'store-mixed';
            $modelId = 'model-mixed';
            $object = 'project:website';
            $relation = 'contributor';

            $userFilters = test()->createMock(UserTypeFiltersInterface::class);

            // Create users collection with mixed types
            $users = [
                new User(object: 'user:anne'),
                new User(object: 'group:engineering'),
                new User(object: 'user:bob'),
                new User(object: 'team:frontend#member'),
                new User(object: 'application:ci-bot'),
            ];
            $usersCollection = new Users($users);

            $response = test()->createMock(ListUsersResponseInterface::class);
            $response->method('getUsers')->willReturn($usersCollection);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('listUsers')
                ->willReturn(new Success($response));

            $result = \OpenFGA\users($object, $relation, $userFilters, $client, $storeId, $modelId);

            expect($result)->toBeArray();
            expect($result)->toHaveCount(5);
            expect($result)->toContain('user:anne');
            expect($result)->toContain('group:engineering');
            expect($result)->toContain('team:frontend#member');
        });
    });

    describe('filter() function', function (): void {
        test('creates UserTypeFilter with type only', function (): void {
            $filter = filter('user');

            expect($filter)->toBeInstanceOf(UserTypeFilter::class);
            expect($filter->getType())->toBe('user');
            expect($filter->getRelation())->toBeNull();
        });

        test('creates UserTypeFilter with type and relation', function (): void {
            $filter = filter('group', 'member');

            expect($filter)->toBeInstanceOf(UserTypeFilter::class);
            expect($filter->getType())->toBe('group');
            expect($filter->getRelation())->toBe('member');
        });

        test('handles various user types', function (): void {
            $userTypes = [
                'user',
                'group',
                'organization',
                'team',
                'service_account',
                'application',
            ];

            foreach ($userTypes as $type) {
                $filter = filter($type);
                expect($filter->getType())->toBe($type);
                expect($filter->getRelation())->toBeNull();
            }
        });

        test('handles various relations', function (): void {
            $relations = [
                'member',
                'admin',
                'owner',
                'viewer',
                'editor',
                'contributor',
                'manager',
            ];

            foreach ($relations as $relation) {
                $filter = filter('user', $relation);
                expect($filter->getType())->toBe('user');
                expect($filter->getRelation())->toBe($relation);
            }
        });

        test('works with complex type and relation combinations', function (): void {
            $combinations = [
                ['organization', 'admin'],
                ['team', 'owner'],
                ['group', 'member'],
                ['service_account', null],
                ['application', 'viewer'],
            ];

            foreach ($combinations as [$type, $relation]) {
                $filter = filter($type, $relation);
                expect($filter->getType())->toBe($type);
                expect($filter->getRelation())->toBe($relation);
            }
        });
    });

    describe('filters() function', function (): void {
        test('creates UserTypeFilters from single filter', function (): void {
            $userFilter = filter('user');
            $filters = filters($userFilter);

            expect($filters)->toBeInstanceOf(UserTypeFilters::class);
            expect($filters->count())->toBe(1);
            expect($filters->get(0))->toBe($userFilter);
        });

        test('creates UserTypeFilters from multiple filters', function (): void {
            $userFilter = filter('user');
            $groupFilter = filter('group', 'member');
            $orgFilter = filter('organization', 'admin');

            $filters = filters($userFilter, $groupFilter, $orgFilter);

            expect($filters)->toBeInstanceOf(UserTypeFilters::class);
            expect($filters->count())->toBe(3);
            expect($filters->get(0))->toBe($userFilter);
            expect($filters->get(1))->toBe($groupFilter);
            expect($filters->get(2))->toBe($orgFilter);
        });

        test('creates empty UserTypeFilters with no arguments', function (): void {
            $filters = filters();

            expect($filters)->toBeInstanceOf(UserTypeFilters::class);
            expect($filters->count())->toBe(0);
        });

        test('works with inline filter creation', function (): void {
            $filters = filters(
                filter('user'),
                filter('group', 'member'),
                filter('organization', 'admin'),
                filter('service_account'),
            );

            expect($filters->count())->toBe(4);
            expect($filters->get(0)->getType())->toBe('user');
            expect($filters->get(0)->getRelation())->toBeNull();
            expect($filters->get(1)->getType())->toBe('group');
            expect($filters->get(1)->getRelation())->toBe('member');
            expect($filters->get(2)->getType())->toBe('organization');
            expect($filters->get(2)->getRelation())->toBe('admin');
            expect($filters->get(3)->getType())->toBe('service_account');
            expect($filters->get(3)->getRelation())->toBeNull();
        });

        test('can be iterated over', function (): void {
            $filters = filters(
                filter('user'),
                filter('group', 'member'),
                filter('team', 'owner'),
            );

            $types = [];
            $relations = [];

            foreach ($filters as $filter) {
                $types[] = $filter->getType();
                $relations[] = $filter->getRelation();
            }

            expect($types)->toBe(['user', 'group', 'team']);
            expect($relations)->toBe([null, 'member', 'owner']);
        });

        test('integrates with users() helper function', function (): void {
            $storeId = 'store-integration';
            $modelId = 'model-integration';
            $object = 'document:integration';
            $relation = 'viewer';

            // Create filters using helper functions
            $userFilters = filters(
                filter('user'),
                filter('group', 'member'),
            );

            // Create User objects for the response
            $users = [
                new User(object: 'user:alice'),
                new User(object: 'group:developers'),
            ];
            $usersCollection = new Users($users);

            $response = test()->createMock(ListUsersResponseInterface::class);
            $response->method('getUsers')->willReturn($usersCollection);

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('listUsers')
                ->with(
                    $this->equalTo($storeId),
                    $this->equalTo($modelId),
                    $this->equalTo($object),
                    $this->equalTo($relation),
                    $this->equalTo($userFilters),
                    $this->isNull(),
                    $this->isNull(),
                    $this->isNull(),
                )
                ->willReturn(new Success($response));

            $result = \OpenFGA\users($object, $relation, $userFilters, $client, $storeId, $modelId);

            expect($result)->toBeArray();
            expect($result)->toHaveCount(2);
            expect($result)->toBe(['user:alice', 'group:developers']);
        });
    });
});
