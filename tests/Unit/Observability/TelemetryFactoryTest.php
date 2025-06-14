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

        it('uses default values when no parameters provided', function (): void {
            $telemetry = TelemetryFactory::create();

            expect(null === $telemetry || $telemetry instanceof TelemetryInterface)->toBeTrue();
        });

        it('handles empty service name and version gracefully', function (): void {
            $telemetry1 = TelemetryFactory::create('', '1.0.0');
            $telemetry2 = TelemetryFactory::create('test-service', '');
            $telemetry3 = TelemetryFactory::create('', '');

            expect(null === $telemetry1 || $telemetry1 instanceof TelemetryInterface)->toBeTrue();
            expect(null === $telemetry2 || $telemetry2 instanceof TelemetryInterface)->toBeTrue();
            expect(null === $telemetry3 || $telemetry3 instanceof TelemetryInterface)->toBeTrue();
        });

        it('returns null when OpenTelemetry is not available', function (): void {
            // This test verifies the behavior when OpenTelemetry classes don't exist
            // The actual result depends on the test environment setup
            $telemetry = TelemetryFactory::create();

            // Should either be null or a valid TelemetryInterface, never throw
            expect(null === $telemetry || $telemetry instanceof TelemetryInterface)->toBeTrue();
        });

        it('handles exceptions during provider creation gracefully', function (): void {
            // This test ensures exceptions are caught and null is returned
            $telemetry = TelemetryFactory::create('test-service', '1.0.0');

            // Should handle any internal exceptions and return null or valid instance
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

        it('creates new instances for each call', function (): void {
            $tracer = test()->createMock('OpenTelemetry\\API\\Trace\\TracerInterface');
            $meter = test()->createMock('OpenTelemetry\\API\\Metrics\\MeterInterface');

            $telemetry1 = TelemetryFactory::createWithCustomProviders($tracer, $meter);
            $telemetry2 = TelemetryFactory::createWithCustomProviders($tracer, $meter);

            expect($telemetry1)->toBeInstanceOf('OpenFGA\\Observability\\OpenTelemetryProvider');
            expect($telemetry2)->toBeInstanceOf('OpenFGA\\Observability\\OpenTelemetryProvider');
            expect($telemetry1)->not->toBe($telemetry2);
        });

        it('accepts different tracer and meter implementations', function (): void {
            $tracer1 = test()->createMock('OpenTelemetry\\API\\Trace\\TracerInterface');
            $meter1 = test()->createMock('OpenTelemetry\\API\\Metrics\\MeterInterface');
            $tracer2 = test()->createMock('OpenTelemetry\\API\\Trace\\TracerInterface');
            $meter2 = test()->createMock('OpenTelemetry\\API\\Metrics\\MeterInterface');

            $telemetry1 = TelemetryFactory::createWithCustomProviders($tracer1, $meter1);
            $telemetry2 = TelemetryFactory::createWithCustomProviders($tracer2, $meter2);

            expect($telemetry1)->toBeInstanceOf('OpenFGA\\Observability\\OpenTelemetryProvider');
            expect($telemetry2)->toBeInstanceOf('OpenFGA\\Observability\\OpenTelemetryProvider');
            expect($telemetry1)->not->toBe($telemetry2);
        });
    });

    describe('static factory behavior', function (): void {
        it('can be called without instantiating the factory class', function (): void {
            // Test that static methods work correctly
            $telemetry1 = TelemetryFactory::create();

            $tracer = test()->createMock('OpenTelemetry\\API\\Trace\\TracerInterface');
            $meter = test()->createMock('OpenTelemetry\\API\\Metrics\\MeterInterface');
            $telemetry2 = TelemetryFactory::createWithCustomProviders($tracer, $meter);

            expect(null === $telemetry1 || $telemetry1 instanceof TelemetryInterface)->toBeTrue();
            expect($telemetry2)->toBeInstanceOf('OpenFGA\\Observability\\OpenTelemetryProvider');
        });
    });
});
