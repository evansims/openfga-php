<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use Exception;
use OpenFGA\{ClientInterface, Language};
use OpenFGA\Exceptions\{ClientError, ClientException, NetworkError};
use OpenFGA\Models\{AuthorizationModelInterface, Condition, ConditionParameter, TupleKey};
use OpenFGA\Models\Collections\{
    ConditionParameters,
    Conditions,
    TupleKeys,
    TypeDefinitions
};
use OpenFGA\Models\Enums\{SchemaVersion, TypeName};
use OpenFGA\Responses\{
    CheckResponseInterface,
    CreateAuthorizationModelResponseInterface,
    CreateStoreResponseInterface,
    WriteTuplesResponseInterface
};
use OpenFGA\Results\{Failure, Success};
use RuntimeException;
use Throwable;

use function OpenFGA\{
    allowed,
    delete,
    dsl,
    err,
    failure,
    lang,
    model,
    ok,
    result,
    store,
    success,
    tuple,
    tuples,
    unwrap,
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

            store($client, 'My Test Store');
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

            dsl($client, $dslString);
        })->throws(Exception::class, 'Invalid DSL syntax');

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

            write($client, $storeId, $model, $tuple);

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

            write($client, $storeId, $model, $tuples);

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
            write($client, $storeId, $model, $tuple);

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

                write($client, $storeId, $model, $testTuple);
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

            delete($client, $storeId, $modelId, $tuple);

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

            delete($client, $storeId, $modelId, $tuples);

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
            delete($client, $storeId, $modelId, $tuple);

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

            delete($client, $storeId, $modelId, $tuples);

            // If we get here without throwing, the batch delete was successful
            expect(true)->toBe(true);
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

            $result = allowed($client, $storeId, $modelId, $tuple);

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

            $result = allowed($client, $storeId, $modelId, $tuple);

            expect($result)->toBe(false);
        });

        test('throws exception when check fails', function (): void {
            $storeId = 'store-fail';
            $modelId = 'model-fail';
            $tuple = new TupleKey('user:anne', 'viewer', 'document:roadmap');
            $exception = new Exception('Check operation failed');

            $client = test()->createMock(ClientInterface::class);
            $client->expects($this->once())
                ->method('check')
                ->willReturn(new Failure($exception));

            allowed($client, $storeId, $modelId, $tuple);
        })->throws(Exception::class, 'Check operation failed');

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

            $result = allowed($client, $storeId, $modelId, $tuple);

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

                $result = allowed($client, $storeId, $modelId, $tuple);

                expect($result)->toBe($expectedResult);
            }
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

            $modelId = model($client, $storeId, $authModel);

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

            model($client, $storeId, $authModel);
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

            $modelId = model($client, $storeId, $authModel);

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

            $modelId = model($client, $storeId, $authModel);

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

                $modelId = model($client, $storeId, $authModel);

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

            write($client, $storeId, $model, $tuple);
            $isAllowed = allowed($client, $storeId, $modelId, $tuple);

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

            delete($client, $storeId, $modelId, $tuple);
            $isAllowed = allowed($client, $storeId, $modelId, $tuple);

            expect($isAllowed)->toBe(false);
        });
    });
});
