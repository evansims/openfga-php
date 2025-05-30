<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Observability;

use DateTimeImmutable;
use Exception;
use OpenFGA\Models\{Store, TupleKey};
use OpenFGA\Observability\{NoOpTelemetryProvider, TelemetryInterface};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

beforeEach(function (): void {
    $this->telemetry = new NoOpTelemetryProvider;
    $now = new DateTimeImmutable;
    $this->store = new Store('test-store-id', 'Test Store', $now, $now);
    $this->tupleKey = new TupleKey('document:readme', 'reader', 'user:bob');
});

describe('NoOpTelemetryProvider', function (): void {
    it('implements TelemetryInterface', function (): void {
        expect($this->telemetry)->toBeInstanceOf(TelemetryInterface::class);
    });

    describe('startOperation()', function (): void {
        it('returns null and does not throw', function (): void {
            $result = $this->telemetry->startOperation('check', $this->store, 'model-123');

            expect($result)->toBeNull();
        });

        it('accepts string store parameter', function (): void {
            $result = $this->telemetry->startOperation('check', 'store-id');

            expect($result)->toBeNull();
        });

        it('accepts attributes array', function (): void {
            $result = $this->telemetry->startOperation(
                'check',
                $this->store,
                'model-123',
                ['custom' => 'attribute'],
            );

            expect($result)->toBeNull();
        });
    });

    describe('endOperation()', function (): void {
        it('does not throw when called with null span', function (): void {
            $this->telemetry->endOperation(null, true);
            expect(true)->toBe(true); // If we reach this line, no exception was thrown
        });

        it('does not throw when called with exception', function (): void {
            $exception = new Exception('Test exception');

            $this->telemetry->endOperation(null, false, $exception);
            expect(true)->toBe(true); // If we reach this line, no exception was thrown
        });
    });

    describe('startHttpRequest()', function (): void {
        it('returns null and does not throw', function (): void {
            $request = test()->createMock(RequestInterface::class);

            $result = $this->telemetry->startHttpRequest($request);

            expect($result)->toBeNull();
        });
    });

    describe('endHttpRequest()', function (): void {
        it('does not throw when called with null span', function (): void {
            $response = test()->createMock(ResponseInterface::class);

            $this->telemetry->endHttpRequest(null, $response);
            expect(true)->toBe(true); // If we reach this line, no exception was thrown
        });

        it('does not throw when called with exception', function (): void {
            $exception = new Exception('Test exception');

            $this->telemetry->endHttpRequest(null, null, $exception);
            expect(true)->toBe(true); // If we reach this line, no exception was thrown
        });
    });

    describe('recordRetryAttempt()', function (): void {
        it('does not throw', function (): void {
            $this->telemetry->recordRetryAttempt(
                'https://api.fga.example/stores',
                2,
                1000,
                'retry',
            );
            expect(true)->toBe(true); // If we reach this line, no exception was thrown
        });

        it('does not throw with exception', function (): void {
            $exception = new Exception('Network error');

            $this->telemetry->recordRetryAttempt(
                'https://api.fga.example/stores',
                3,
                2000,
                'failure',
                $exception,
            );
            expect(true)->toBe(true); // If we reach this line, no exception was thrown
        });
    });

    describe('recordCircuitBreakerState()', function (): void {
        it('does not throw', function (): void {
            $this->telemetry->recordCircuitBreakerState(
                'https://api.fga.example/stores',
                'open',
                5,
                0.75,
            );
            expect(true)->toBe(true); // If we reach this line, no exception was thrown
        });
    });

    describe('recordAuthenticationEvent()', function (): void {
        it('does not throw', function (): void {
            $this->telemetry->recordAuthenticationEvent(
                'token_request',
                true,
                0.250,
            );
            expect(true)->toBe(true); // If we reach this line, no exception was thrown
        });

        it('does not throw with attributes', function (): void {
            $this->telemetry->recordAuthenticationEvent(
                'token_refresh',
                false,
                1.500,
                ['error_code' => 'invalid_grant'],
            );
            expect(true)->toBe(true); // If we reach this line, no exception was thrown
        });
    });

    describe('recordOperationMetrics()', function (): void {
        it('does not throw', function (): void {
            $this->telemetry->recordOperationMetrics(
                'check',
                0.123,
                $this->store,
                'model-123',
            );
            expect(true)->toBe(true); // If we reach this line, no exception was thrown
        });

        it('does not throw with string store', function (): void {
            $this->telemetry->recordOperationMetrics(
                'expand',
                0.456,
                'store-id',
            );
            expect(true)->toBe(true); // If we reach this line, no exception was thrown
        });

        it('does not throw with attributes', function (): void {
            $this->telemetry->recordOperationMetrics(
                'write_tuples',
                0.789,
                $this->store,
                'model-123',
                ['operation_count' => 10],
            );
            expect(true)->toBe(true); // If we reach this line, no exception was thrown
        });
    });
});
