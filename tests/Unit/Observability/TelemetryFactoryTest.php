<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Observability;

use OpenFGA\Observability\{NoOpTelemetryProvider, TelemetryFactory, TelemetryInterface};

beforeEach(function (): void {
    // Ensure clean state for each test
});

describe('TelemetryFactory', function (): void {
    describe('create()', function (): void {
        it('returns a TelemetryInterface implementation', function (): void {
            $telemetry = TelemetryFactory::create();

            expect($telemetry)->toBeInstanceOf(TelemetryInterface::class);
        });

        it('accepts custom service name and version', function (): void {
            $telemetry = TelemetryFactory::create('custom-service', '2.0.0');

            expect($telemetry)->toBeInstanceOf(TelemetryInterface::class);
        });
    });

    describe('createNoOp()', function (): void {
        it('returns NoOpTelemetryProvider', function (): void {
            $telemetry = TelemetryFactory::createNoOp();

            expect($telemetry)->toBeInstanceOf(NoOpTelemetryProvider::class);
            expect($telemetry)->toBeInstanceOf(TelemetryInterface::class);
        });
    });

    describe('createWithCustomProviders()', function (): void {
        it('returns OpenTelemetryProvider with valid providers', function (): void {
            $tracer = test()->createMock('OpenTelemetry\\API\\Trace\\TracerInterface');
            $meter = test()->createMock('OpenTelemetry\\API\\Metrics\\MeterInterface');

            $telemetry = TelemetryFactory::createWithCustomProviders($tracer, $meter);

            expect($telemetry)->toBeInstanceOf('OpenFGA\\Observability\\OpenTelemetryProvider');
        });
    });
});
