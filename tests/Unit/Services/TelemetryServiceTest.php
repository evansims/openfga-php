<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use DateTimeImmutable;
use Exception;
use OpenFGA\Models\{AuthorizationModel, Store};
use OpenFGA\Models\Collections\TypeDefinitions;
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Observability\TelemetryInterface;
use OpenFGA\Services\{TelemetryContext, TelemetryService, TelemetryServiceInterface};
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use stdClass;

use function is_float;

beforeEach(function (): void {
    $this->mockTelemetry = test()->createMock(TelemetryInterface::class);

    $this->service = new TelemetryService($this->mockTelemetry);

    $this->store = new Store(
        'store-123',
        'Test Store',
        new DateTimeImmutable,
        new DateTimeImmutable,
    );

    $this->model = new AuthorizationModel(
        'model-456',
        SchemaVersion::V1_1,
        new TypeDefinitions([]),
    );

    $this->mockRequest = test()->createMock(RequestInterface::class);
    $this->mockResponse = test()->createMock(ResponseInterface::class);
});

describe('TelemetryService', function (): void {
    it('implements TelemetryServiceInterface', function (): void {
        expect($this->service)->toBeInstanceOf(TelemetryServiceInterface::class);
    });

    describe('startOperation', function (): void {
        it('creates telemetry context with operation details', function (): void {
            $mockSpan = new stdClass;
            $this->mockTelemetry
                ->expects(test()->once())
                ->method('startOperation')
                ->with('check', $this->store, $this->model, ['key' => 'value'])
                ->willReturn($mockSpan);

            $context = $this->service->startOperation(
                'check',
                $this->store,
                $this->model,
                ['key' => 'value'],
            );

            expect($context)->toBeInstanceOf(TelemetryContext::class);
            expect($context->operation)->toBe('check');
            expect($context->store)->toBe($this->store);
            expect($context->model)->toBe($this->model);
            expect($context->span)->toBe($mockSpan);
            expect($context->attributes)->toBe(['key' => 'value']);
            expect($context->startTime)->toBeFloat();
        });

        it('works with string store and no model', function (): void {
            $mockSpan = new stdClass;
            $this->mockTelemetry
                ->expects(test()->once())
                ->method('startOperation')
                ->with('expand', 'store-id', null, [])
                ->willReturn($mockSpan);

            $context = $this->service->startOperation('expand', 'store-id');

            expect($context->operation)->toBe('expand');
            expect($context->store)->toBe('store-id');
            expect($context->model)->toBeNull();
        });
    });

    describe('recordSuccess', function (): void {
        it('records successful operation completion', function (): void {
            $context = new TelemetryContext(
                'check',
                $this->store,
                $this->model,
                microtime(true) - 0.1, // 100ms ago
                new stdClass,
                ['test' => true],
            );

            $this->mockTelemetry
                ->expects(test()->once())
                ->method('endOperation')
                ->with(
                    new stdClass,
                    true,
                    null,
                    test()->callback(fn ($attrs) => isset($attrs['test']) && true === $attrs['test'] && isset($attrs['result_type'])),
                );

            $this->mockTelemetry
                ->expects(test()->once())
                ->method('recordOperationMetrics')
                ->with(
                    'check',
                    test()->callback(fn ($duration) => is_float($duration) && 0 < $duration),
                    $this->store,
                    $this->model,
                    ['test' => true],
                );

            $this->service->recordSuccess($context, ['result' => 'data']);
        });
    });

    describe('recordFailure', function (): void {
        it('records failed operation with exception details', function (): void {
            $context = new TelemetryContext(
                'write',
                'store-id',
                null,
                microtime(true) - 0.05, // 50ms ago
                new stdClass,
                [],
            );

            $exception = new Exception('Test error');

            $this->mockTelemetry
                ->expects(test()->once())
                ->method('endOperation')
                ->with(
                    new stdClass,
                    false,
                    $exception,
                    test()->callback(fn ($attrs) => isset($attrs['error_class']) && Exception::class === $attrs['error_class']),
                );

            $this->mockTelemetry
                ->expects(test()->once())
                ->method('recordOperationMetrics')
                ->with(
                    'write',
                    test()->callback(fn ($duration) => is_float($duration) && 0 < $duration),
                    'store-id',
                    null,
                    test()->callback(fn ($attrs) => isset($attrs['error']) && true === $attrs['error']),
                );

            $this->service->recordFailure($context, $exception);
        });
    });

    describe('recordHttpRequest', function (): void {
        it('records standalone HTTP request', function (): void {
            $mockSpan = new stdClass;

            $this->mockTelemetry
                ->expects(test()->once())
                ->method('startHttpRequest')
                ->with($this->mockRequest)
                ->willReturn($mockSpan);

            $this->mockTelemetry
                ->expects(test()->once())
                ->method('endHttpRequest')
                ->with($mockSpan, $this->mockResponse, null);

            $this->service->recordHttpRequest($this->mockRequest, $this->mockResponse);
        });

        it('records HTTP request with exception', function (): void {
            $mockSpan = new stdClass;
            $exception = new Exception('Network error');

            $this->mockTelemetry
                ->expects(test()->once())
                ->method('startHttpRequest')
                ->with($this->mockRequest)
                ->willReturn($mockSpan);

            $this->mockTelemetry
                ->expects(test()->once())
                ->method('endHttpRequest')
                ->with($mockSpan, null, $exception);

            $this->service->recordHttpRequest($this->mockRequest, null, $exception);
        });
    });

    describe('recordAuthenticationEvent', function (): void {
        it('delegates to underlying telemetry provider', function (): void {
            $this->mockTelemetry
                ->expects(test()->once())
                ->method('recordAuthenticationEvent')
                ->with('token_request', true, 0.5, ['endpoint' => '/oauth/token']);

            $this->service->recordAuthenticationEvent(
                'token_request',
                true,
                0.5,
                ['endpoint' => '/oauth/token'],
            );
        });
    });

    describe('recordOperationMetrics', function (): void {
        it('delegates to underlying telemetry provider', function (): void {
            $this->mockTelemetry
                ->expects(test()->once())
                ->method('recordOperationMetrics')
                ->with('list_objects', 1.2, $this->store, $this->model, ['limit' => 100]);

            $this->service->recordOperationMetrics(
                'list_objects',
                1.2,
                $this->store,
                $this->model,
                ['limit' => 100],
            );
        });
    });
});

describe('TelemetryContext', function (): void {
    beforeEach(function (): void {
        $this->store = new Store(
            'store-abc',
            'Context Store',
            new DateTimeImmutable,
            new DateTimeImmutable,
        );

        $this->model = new AuthorizationModel(
            'model-xyz',
            SchemaVersion::V1_1,
            new TypeDefinitions([]),
        );

        $this->context = new TelemetryContext(
            'test_operation',
            $this->store,
            $this->model,
            microtime(true) - 0.25, // 250ms ago
            new stdClass,
            ['test' => 'context'],
        );
    });

    it('calculates duration correctly', function (): void {
        $duration = $this->context->getDuration();

        expect($duration)->toBeFloat();
        expect($duration)->toBeGreaterThan(0.2); // At least 200ms
        expect($duration)->toBeLessThan(1.0); // Less than 1 second
    });

    it('extracts store ID from store object', function (): void {
        expect($this->context->getStoreId())->toBe('store-abc');
    });

    it('extracts store ID from string store', function (): void {
        $stringContext = new TelemetryContext(
            'test',
            'string-store-id',
            null,
            microtime(true),
            new stdClass,
        );

        expect($stringContext->getStoreId())->toBe('string-store-id');
    });

    it('extracts model ID from model object', function (): void {
        expect($this->context->getModelId())->toBe('model-xyz');
    });

    it('handles null model', function (): void {
        $contextWithoutModel = new TelemetryContext(
            'test',
            $this->store,
            null,
            microtime(true),
            new stdClass,
        );

        expect($contextWithoutModel->getModelId())->toBeNull();
    });

    it('extracts model ID from string model', function (): void {
        $stringModelContext = new TelemetryContext(
            'test',
            $this->store,
            'string-model-id',
            microtime(true),
            new stdClass,
        );

        expect($stringModelContext->getModelId())->toBe('string-model-id');
    });
});
