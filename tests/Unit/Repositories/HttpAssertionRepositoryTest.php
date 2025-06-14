<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Repositories;

use OpenFGA\Models\{Assertion, AssertionTupleKey};
use OpenFGA\Models\Collections\{Assertions, AssertionsInterface};
use OpenFGA\Repositories\HttpAssertionRepository;
use OpenFGA\Requests\{ReadAssertionsRequest, WriteAssertionsRequest};
use OpenFGA\Results\{Failure};
use OpenFGA\Schemas\SchemaValidatorInterface;
use OpenFGA\Services\HttpServiceInterface;
use Psr\Http\Message\{ResponseInterface};
use RuntimeException;

describe('HttpAssertionRepository', function (): void {
    beforeEach(function (): void {
        $this->httpService = test()->createMock(HttpServiceInterface::class);
        $this->validator = test()->createMock(SchemaValidatorInterface::class);
        $this->storeId = 'store-123';

        $this->repository = new HttpAssertionRepository(
            $this->httpService,
            $this->validator,
            $this->storeId,
        );
    });

    describe('constructor', function (): void {
        test('creates repository with dependencies', function (): void {
            expect($this->repository)->toBeInstanceOf(HttpAssertionRepository::class);
        });
    });

    describe('read', function (): void {
        test('returns failure when HTTP service fails', function (): void {
            $modelId = 'model-456';
            $exception = new RuntimeException('HTTP error');

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willThrowException($exception);

            $result = $this->repository->read($modelId);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBe($exception);
        });

        test('returns failure when no request is available', function (): void {
            $modelId = 'model-456';
            $response = test()->createMock(ResponseInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($response);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn(null);

            $result = $this->repository->read($modelId);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBeInstanceOf(RuntimeException::class);
            expect($result->err()->getMessage())->toBe('No HTTP request available');
        });

        test('sends correct request for read operation', function (): void {
            $modelId = 'model-456';
            $response = test()->createMock(ResponseInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(fn ($req) => $req instanceof ReadAssertionsRequest
                           && $req->getStore() === $this->storeId
                           && $req->getModel() === $modelId))
                ->willThrowException(new RuntimeException('Expected failure for testing'));

            $result = $this->repository->read($modelId);

            expect($result)->toBeInstanceOf(Failure::class);
        });
    });

    describe('write', function (): void {
        test('returns failure when HTTP service fails', function (): void {
            $modelId = 'model-456';
            $assertions = test()->createMock(AssertionsInterface::class);
            $exception = new RuntimeException('HTTP error');

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willThrowException($exception);

            $result = $this->repository->write($modelId, $assertions);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBe($exception);
        });

        test('returns failure when no request is available', function (): void {
            $modelId = 'model-456';
            $assertions = test()->createMock(AssertionsInterface::class);
            $response = test()->createMock(ResponseInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willReturn($response);

            $this->httpService
                ->expects(test()->once())
                ->method('getLastRequest')
                ->willReturn(null);

            $result = $this->repository->write($modelId, $assertions);

            expect($result)->toBeInstanceOf(Failure::class);
            expect($result->err())->toBeInstanceOf(RuntimeException::class);
            expect($result->err()->getMessage())->toBe('No HTTP request available');
        });

        test('sends correct request for write operation', function (): void {
            $modelId = 'model-456';
            $assertions = new Assertions([
                new Assertion(
                    new AssertionTupleKey('user:alice', 'reader', 'document:budget'),
                    true,
                ),
            ]);
            $response = test()->createMock(ResponseInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(fn ($req) => $req instanceof WriteAssertionsRequest
                           && $req->getStore() === $this->storeId
                           && $req->getModel() === $modelId
                           && $req->getAssertions() === $assertions))
                ->willThrowException(new RuntimeException('Expected failure for testing'));

            $result = $this->repository->write($modelId, $assertions);

            expect($result)->toBeInstanceOf(Failure::class);
        });

        test('handles various assertion types', function (): void {
            $modelId = 'model-456';
            $assertions = new Assertions([
                new Assertion(
                    new AssertionTupleKey('user:alice', 'reader', 'document:budget'),
                    true,
                ),
                new Assertion(
                    new AssertionTupleKey('user:bob', 'writer', 'document:budget'),
                    false,
                ),
            ]);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->willThrowException(new RuntimeException('Expected failure for testing'));

            $result = $this->repository->write($modelId, $assertions);

            expect($result)->toBeInstanceOf(Failure::class);
        });

        test('validates store ID is used in requests', function (): void {
            $modelId = 'model-456';
            $assertions = test()->createMock(AssertionsInterface::class);

            $this->httpService
                ->expects(test()->once())
                ->method('send')
                ->with(test()->callback(fn ($req) => $req instanceof WriteAssertionsRequest
                           && $req->getStore() === $this->storeId))
                ->willThrowException(new RuntimeException('Expected failure for testing'));

            $result = $this->repository->write($modelId, $assertions);

            expect($result)->toBeInstanceOf(Failure::class);
        });
    });
});
