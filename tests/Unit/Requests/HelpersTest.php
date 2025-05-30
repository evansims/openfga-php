<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Requests;

use Exception;
use OpenFGA\ClientInterface;
use OpenFGA\Models\{AuthorizationModelInterface, TupleKey};
use OpenFGA\Models\Collections\{Conditions, TupleKeys, TypeDefinitions};
use OpenFGA\Models\Enums\{SchemaVersion};
use OpenFGA\Responses\{CheckResponseInterface, CreateAuthorizationModelResponseInterface, WriteTuplesResponseInterface};

use OpenFGA\Results\{Failure, Success};

use function OpenFGA\Models\tuple;
use function OpenFGA\Requests\{allowed, delete, model, write};

describe('Requests Helper Functions', function (): void {
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
            for ($i = 0; $i < 100; ++$i) {
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

            expect(fn () => allowed($client, $storeId, $modelId, $tuple))
                ->toThrow(Exception::class, 'Check operation failed');
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

    describe('combined usage', function (): void {
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

            expect(fn () => model($client, $storeId, $authModel))
                ->toThrow(Exception::class, 'Model creation failed');
        });

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
});
