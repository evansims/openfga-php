<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use OpenFGA\{ClientInterface};
use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Responses\WriteTuplesResponse;
use OpenFGA\Results\Success;

use function OpenFGA\{delete, tuple, tuples, write};

describe('Helpers write and delete functions', function (): void {
    beforeEach(function (): void {
        $this->mockClient = test()->createMock(ClientInterface::class);
        $this->mockResponse = new WriteTuplesResponse;
    });

    test('write() with single tuple defaults to transactional', function (): void {
        $singleTuple = tuple('user:anne', 'reader', 'document:1');

        $this->mockClient
            ->expects(test()->once())
            ->method('writeTuples')
            ->with(
                store: 'store-id',
                model: 'model-id',
                writes: test()->callback(
                    fn ($writes) => $writes instanceof TupleKeys
                    && 1 === $writes->count()
                    && 'user:anne' === $writes[0]->getUser(),
                ),
                deletes: null,
                transactional: true, // Should default to true
                maxParallelRequests: 1,
                maxTuplesPerChunk: 100,
                maxRetries: 0,
                retryDelaySeconds: 1.0,
                stopOnFirstError: false,
            )
            ->willReturn(new Success($this->mockResponse));

        write($this->mockClient, 'store-id', 'model-id', $singleTuple);
    });

    test('write() with multiple tuples defaults to transactional', function (): void {
        $multipleTuples = tuples(
            tuple('user:anne', 'reader', 'document:1'),
            tuple('user:bob', 'editor', 'document:2'),
        );

        $this->mockClient
            ->expects(test()->once())
            ->method('writeTuples')
            ->with(
                store: 'store-id',
                model: 'model-id',
                writes: test()->callback(
                    fn ($writes) => $writes instanceof TupleKeys
                    && 2 === $writes->count(),
                ),
                deletes: null,
                transactional: true, // Should default to true
                maxParallelRequests: 1,
                maxTuplesPerChunk: 100,
                maxRetries: 0,
                retryDelaySeconds: 1.0,
                stopOnFirstError: false,
            )
            ->willReturn(new Success($this->mockResponse));

        write($this->mockClient, 'store-id', 'model-id', $multipleTuples);
    });

    test('write() can use non-transactional mode', function (): void {
        $tuples = tuples(tuple('user:anne', 'reader', 'document:1'));

        $this->mockClient
            ->expects(test()->once())
            ->method('writeTuples')
            ->with(
                store: 'store-id',
                model: 'model-id',
                writes: test()->isInstanceOf(TupleKeys::class),
                deletes: null,
                transactional: false, // Explicitly set to false
                maxParallelRequests: 1,
                maxTuplesPerChunk: 100,
                maxRetries: 0,
                retryDelaySeconds: 1.0,
                stopOnFirstError: false,
            )
            ->willReturn(new Success($this->mockResponse));

        write($this->mockClient, 'store-id', 'model-id', $tuples, transactional: false);
    });

    test('delete() with single tuple defaults to transactional', function (): void {
        $singleTuple = tuple('user:anne', 'reader', 'document:1');

        $this->mockClient
            ->expects(test()->once())
            ->method('writeTuples')
            ->with(
                store: 'store-id',
                model: 'model-id',
                writes: null,
                deletes: test()->callback(
                    fn ($deletes) => $deletes instanceof TupleKeys
                    && 1 === $deletes->count()
                    && 'user:anne' === $deletes[0]->getUser(),
                ),
                transactional: true, // Should default to true
                maxParallelRequests: 1,
                maxTuplesPerChunk: 100,
                maxRetries: 0,
                retryDelaySeconds: 1.0,
                stopOnFirstError: false,
            )
            ->willReturn(new Success($this->mockResponse));

        delete($this->mockClient, 'store-id', 'model-id', $singleTuple);
    });

    test('delete() can use non-transactional mode', function (): void {
        $tuples = tuples(tuple('user:anne', 'reader', 'document:1'));

        $this->mockClient
            ->expects(test()->once())
            ->method('writeTuples')
            ->with(
                store: 'store-id',
                model: 'model-id',
                writes: null,
                deletes: test()->isInstanceOf(TupleKeys::class),
                transactional: false, // Explicitly set to false
                maxParallelRequests: 1,
                maxTuplesPerChunk: 100,
                maxRetries: 0,
                retryDelaySeconds: 1.0,
                stopOnFirstError: false,
            )
            ->willReturn(new Success($this->mockResponse));

        delete($this->mockClient, 'store-id', 'model-id', $tuples, transactional: false);
    });
});
