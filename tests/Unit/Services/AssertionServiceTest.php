<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use DateTimeImmutable;
use Exception;
use OpenFGA\Models\{Assertion, AssertionTupleKey, Store};
use OpenFGA\Models\Collections\Assertions;
use OpenFGA\Repositories\AssertionRepositoryInterface;
use OpenFGA\Results\{Failure, Success};
use OpenFGA\Services\{AssertionService, AssertionServiceInterface};

beforeEach(function (): void {
    $this->mockAssertionRepository = test()->createMock(AssertionRepositoryInterface::class);

    $this->service = new AssertionService(
        $this->mockAssertionRepository,
        'en',
    );

    $this->store = new Store(
        'store-123',
        'Test Store',
        new DateTimeImmutable,
        new DateTimeImmutable,
    );

    $this->tupleKey = new AssertionTupleKey(
        'user:anne',
        'reader',
        'document:budget',
    );

    $this->assertion = new Assertion(
        $this->tupleKey,
        true,
    );

    $this->assertions = new Assertions([$this->assertion]);
});

describe('AssertionService', function (): void {
    it('implements AssertionServiceInterface', function (): void {
        expect($this->service)->toBeInstanceOf(AssertionServiceInterface::class);
    });

    describe('validateAssertions', function (): void {
        it('validates successful with valid assertions', function (): void {
            $result = $this->service->validateAssertions($this->assertions, 'model-123');

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeTrue();
        });

        it('fails validation for empty assertions collection', function (): void {
            $emptyAssertions = new Assertions([]);

            $result = $this->service->validateAssertions($emptyAssertions, 'model-123');

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('validates tuple key components', function (): void {
            // Create assertion with empty user
            $invalidTupleKey = new AssertionTupleKey('', 'reader', 'document:budget');
            $invalidAssertion = new Assertion($invalidTupleKey, true);
            $invalidAssertions = new Assertions([$invalidAssertion]);

            $result = $this->service->validateAssertions($invalidAssertions, 'model-123');

            expect($result)->toBeInstanceOf(Failure::class);
        });
    });

    describe('readAssertions', function (): void {
        it('delegates to repository for reading assertions', function (): void {
            $this->mockAssertionRepository
                ->expects(test()->once())
                ->method('read')
                ->with('model-123')
                ->willReturn(new Success($this->assertions));

            $result = $this->service->readAssertions($this->store, 'model-123');

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBe($this->assertions);
        });

        it('handles repository failure', function (): void {
            $repositoryFailure = new Failure(new Exception('Repository error'));
            $this->mockAssertionRepository
                ->expects(test()->once())
                ->method('read')
                ->willReturn($repositoryFailure);

            $result = $this->service->readAssertions($this->store, 'model-123');

            expect($result)->toBeInstanceOf(Failure::class);
        });
    });

    describe('writeAssertions', function (): void {
        it('validates before writing assertions', function (): void {
            $emptyAssertions = new Assertions([]);

            $result = $this->service->writeAssertions($this->store, 'model-123', $emptyAssertions);

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('delegates to repository after successful validation', function (): void {
            $this->mockAssertionRepository
                ->expects(test()->once())
                ->method('write')
                ->with('model-123', $this->assertions)
                ->willReturn(new Success(true));

            $result = $this->service->writeAssertions($this->store, 'model-123', $this->assertions);

            expect($result)->toBeInstanceOf(Success::class);
        });
    });

    describe('executeAssertions', function (): void {
        it('validates assertions before execution', function (): void {
            $emptyAssertions = new Assertions([]);

            $result = $this->service->executeAssertions($this->store, 'model-123', $emptyAssertions);

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('returns execution results for valid assertions', function (): void {
            $result = $this->service->executeAssertions($this->store, 'model-123', $this->assertions);

            expect($result)->toBeInstanceOf(Success::class);

            $executionResults = $result->unwrap();
            expect($executionResults)->toHaveKey('total');
            expect($executionResults)->toHaveKey('passed');
            expect($executionResults)->toHaveKey('failed');
            expect($executionResults)->toHaveKey('success_rate');
            expect($executionResults)->toHaveKey('results');
        });
    });

    describe('clearAssertions', function (): void {
        it('clears assertions by writing empty collection', function (): void {
            $this->mockAssertionRepository
                ->expects(test()->once())
                ->method('write')
                ->with('model-123', test()->callback(fn ($assertions) => $assertions instanceof Assertions && 0 === $assertions->count()))
                ->willReturn(new Success(true));

            $result = $this->service->clearAssertions($this->store, 'model-123');

            expect($result)->toBeInstanceOf(Success::class);
        });
    });

    describe('getAssertionStatistics', function (): void {
        it('returns statistics for assertions', function (): void {
            $this->mockAssertionRepository
                ->expects(test()->once())
                ->method('read')
                ->with('model-123')
                ->willReturn(new Success($this->assertions));

            $result = $this->service->getAssertionStatistics($this->store, 'model-123');

            expect($result)->toBeInstanceOf(Success::class);

            $statistics = $result->unwrap();
            expect($statistics)->toHaveKey('total_assertions');
            expect($statistics)->toHaveKey('store_id');
            expect($statistics)->toHaveKey('model_id');
            expect($statistics)->toHaveKey('coverage_metrics');
        });

        it('handles repository failure when generating statistics', function (): void {
            $repositoryFailure = new Failure(new Exception('Repository error'));
            $this->mockAssertionRepository
                ->expects(test()->once())
                ->method('read')
                ->willReturn($repositoryFailure);

            $result = $this->service->getAssertionStatistics($this->store, 'model-123');

            expect($result)->toBeInstanceOf(Failure::class);
        });
    });
});
