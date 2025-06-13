<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use DateTimeImmutable;
use OpenFGA\Language;
use OpenFGA\Models\{AuthorizationModel, Store, TupleKey};
use OpenFGA\Models\Collections\{TupleChanges, Tuples};
use OpenFGA\Models\Collections\{TupleKeys, TypeDefinitions};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Repositories\TupleRepositoryInterface;
use OpenFGA\Responses\{ListTupleChangesResponse, ReadTuplesResponse};
use OpenFGA\Results\{Failure, Success};
use OpenFGA\Services\{TupleService, TupleServiceInterface};

beforeEach(function (): void {
    $this->mockTupleRepository = test()->createMock(TupleRepositoryInterface::class);

    $this->service = new TupleService(
        $this->mockTupleRepository,
        Language::English,
    );

    $this->store = new Store(
        'store-123',
        'Test Store',
        new DateTimeImmutable,
        new DateTimeImmutable,
    );

    $this->model = new AuthorizationModel(
        'model-123',
        SchemaVersion::V1_1,
        new TypeDefinitions([]),
    );

    $this->tupleKey = new TupleKey('user:anne', 'reader', 'document:budget-2024');
    $this->tupleKeys = new TupleKeys([$this->tupleKey]);
});

describe('TupleService', function (): void {
    it('implements TupleServiceInterface', function (): void {
        expect($this->service)->toBeInstanceOf(TupleServiceInterface::class);
    });

    describe('write', function (): void {
        it('validates user is not empty', function (): void {
            $result = $this->service->write(
                $this->store,
                '',
                'reader',
                'document:budget-2024',
            );

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('validates relation is not empty', function (): void {
            $result = $this->service->write(
                $this->store,
                'user:anne',
                '',
                'document:budget-2024',
            );

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('validates object is not empty', function (): void {
            $result = $this->service->write(
                $this->store,
                'user:anne',
                'reader',
                '',
            );

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('returns success for valid tuple', function (): void {
            $result = $this->service->write(
                $this->store,
                'user:anne',
                'reader',
                'document:budget-2024',
            );

            expect($result)->toBeInstanceOf(Success::class);
        });
    });

    describe('writeBatch', function (): void {
        it('handles empty batch', function (): void {
            $emptyTupleKeys = new TupleKeys([]);

            $result = $this->service->writeBatch(
                $this->store,
                $this->model,
                $emptyTupleKeys,
                null,
            );

            expect($result)->toBeInstanceOf(Success::class);
        });

        it('validates transactional limit', function (): void {
            // Create 101 tuple keys to exceed the limit
            $manyTupleKeys = [];

            for ($i = 0; 101 > $i; $i++) {
                $manyTupleKeys[] = new TupleKey("user:user{$i}", 'reader', 'document:test');
            }
            $largeBatch = new TupleKeys($manyTupleKeys);

            $result = $this->service->writeBatch(
                $this->store,
                $this->model,
                $largeBatch,
                null,
                true, // transactional
            );

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('allows large batch in non-transactional mode', function (): void {
            // Create 101 tuple keys
            $manyTupleKeys = [];

            for ($i = 0; 101 > $i; $i++) {
                $manyTupleKeys[] = new TupleKey("user:user{$i}", 'reader', 'document:test');
            }
            $largeBatch = new TupleKeys($manyTupleKeys);

            // Mock the repository to return success
            $this->mockTupleRepository
                ->expects(test()->once())
                ->method('writeAndDelete')
                ->willReturn(new Success(true));

            $result = $this->service->writeBatch(
                $this->store,
                $this->model,
                $largeBatch,
                null,
                false, // non-transactional
            );

            expect($result)->toBeInstanceOf(Success::class);
        });

        it('filters duplicate tuples when requested', function (): void {
            $duplicateTupleKeys = new TupleKeys([
                new TupleKey('user:anne', 'reader', 'document:test'),
                new TupleKey('user:anne', 'reader', 'document:test'), // Duplicate
                new TupleKey('user:bob', 'writer', 'document:test'),
            ]);

            // Mock the repository to return success
            $this->mockTupleRepository
                ->expects(test()->once())
                ->method('writeAndDelete')
                ->willReturn(new Success(true));

            $result = $this->service->writeBatch(
                $this->store,
                $this->model,
                $duplicateTupleKeys,
                null,
                true, // transactional
                1, // maxParallelRequests
                100, // maxTuplesPerChunk
                0, // maxRetries
                1.0, // retryDelaySeconds
                false, // stopOnFirstError
            );

            expect($result)->toBeInstanceOf(Success::class);
        });
    });

    describe('delete', function (): void {
        it('validates user is not empty', function (): void {
            $result = $this->service->delete(
                $this->store,
                '',
                'reader',
                'document:budget-2024',
            );

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('validates relation is not empty', function (): void {
            $result = $this->service->delete(
                $this->store,
                'user:anne',
                '',
                'document:budget-2024',
            );

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('validates object is not empty', function (): void {
            $result = $this->service->delete(
                $this->store,
                'user:anne',
                'reader',
                '',
            );

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('returns success for valid tuple deletion', function (): void {
            $result = $this->service->delete(
                $this->store,
                'user:anne',
                'reader',
                'document:budget-2024',
            );

            expect($result)->toBeInstanceOf(Success::class);
        });
    });

    describe('deleteBatch', function (): void {
        it('validates batch is not empty', function (): void {
            $emptyTupleKeys = new TupleKeys([]);

            $result = $this->service->deleteBatch(
                $this->store,
                $emptyTupleKeys,
            );

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('validates transactional limit', function (): void {
            // Create 101 tuple keys to exceed the limit
            $manyTupleKeys = [];

            for ($i = 0; 101 > $i; $i++) {
                $manyTupleKeys[] = new TupleKey("user:user{$i}", 'reader', 'document:test');
            }
            $largeBatch = new TupleKeys($manyTupleKeys);

            $result = $this->service->deleteBatch(
                $this->store,
                $largeBatch,
                true, // transactional
            );

            expect($result)->toBeInstanceOf(Failure::class);
        });
    });

    describe('basic operations', function (): void {
        it('read returns success', function (): void {
            $this->mockTupleRepository
                ->expects(test()->once())
                ->method('read')
                ->willReturn(new Success(new ReadTuplesResponse(
                    tuples: new Tuples([]),
                    continuationToken: null,
                )));

            $result = $this->service->read($this->store);
            expect($result)->toBeInstanceOf(Success::class);
        });

        it('listChanges returns success', function (): void {
            $this->mockTupleRepository
                ->expects(test()->once())
                ->method('listChanges')
                ->willReturn(new Success(new ListTupleChangesResponse(
                    changes: new TupleChanges([]),
                    continuationToken: null,
                )));

            $result = $this->service->listChanges($this->store);
            expect($result)->toBeInstanceOf(Success::class);
        });

        it('exists returns success', function (): void {
            $result = $this->service->exists($this->store, 'user:anne', 'reader', 'document:test');
            expect($result)->toBeInstanceOf(Success::class);
        });

        it('getStatistics returns success', function (): void {
            $result = $this->service->getStatistics($this->store);
            expect($result)->toBeInstanceOf(Success::class);

            $stats = $result->unwrap();
            expect($stats)->toHaveKey('total_tuples');
            expect($stats)->toHaveKey('types');
            expect($stats)->toHaveKey('relations');
            expect($stats)->toHaveKey('users');
        });
    });
});
