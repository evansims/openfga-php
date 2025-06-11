<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Observability;

use OpenFGA\Observability\{TelemetryFactory, TelemetryInterface};

beforeEach(function (): void {
    // Ensure clean state for each test
});

describe('TelemetryFactory', function (): void {
    describe('create()', function (): void {
        it('returns a TelemetryInterface implementation or null', function (): void {
            $telemetry = TelemetryFactory::create();

            expect(null === $telemetry || $telemetry instanceof TelemetryInterface)->toBeTrue();
        });

        it('accepts custom service name and version', function (): void {
            $telemetry = TelemetryFactory::create('custom-service', '2.0.0');

            expect(null === $telemetry || $telemetry instanceof TelemetryInterface)->toBeTrue();
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
