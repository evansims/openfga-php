<?php

declare(strict_types=1);

namespace OpenFGA\Observability;

use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Metrics\MeterInterface;
use OpenTelemetry\API\Trace\TracerInterface;
use Throwable;

/**
 * Factory for creating telemetry providers with OpenTelemetry integration.
 *
 * This factory provides convenient methods for setting up observability
 * with the OpenFGA SDK. It handles the conditional creation of OpenTelemetry
 * providers when the dependencies are available, and falls back to no-op
 * implementations when they are not.
 *
 * The factory follows the principle of graceful degradation, ensuring that
 * the SDK remains functional even when OpenTelemetry is not installed or
 * configured in the host application.
 */
final class TelemetryFactory
{
    /**
     * Create a telemetry provider using OpenTelemetry if available.
     *
     * Attempts to create an OpenTelemetry-based telemetry provider using
     * the global tracer and meter providers. If OpenTelemetry classes are
     * not available or not configured, returns a no-op implementation.
     *
     * This method uses class_exists checks to determine availability rather
     * than try-catch blocks, providing better performance when OpenTelemetry
     * is not installed.
     *
     * @param  string                                      $serviceName    The service name for telemetry identification
     * @param  string                                      $serviceVersion The service version for telemetry identification
     * @return NoOpTelemetryProvider|OpenTelemetryProvider A telemetry provider instance
     */
    public static function create(
        string $serviceName = 'openfga-php-sdk',
        string $serviceVersion = '1.0.0',
    ): NoOpTelemetryProvider | OpenTelemetryProvider {
        // Check if OpenTelemetry API classes are available
        if (! class_exists('OpenTelemetry\API\Globals')) {
            return new NoOpTelemetryProvider;
        }

        try {
            // Attempt to get global tracer and meter providers
            $tracerProvider = Globals::tracerProvider();
            $meterProvider = Globals::meterProvider();

            $tracer = $tracerProvider->getTracer($serviceName, $serviceVersion);
            $meter = $meterProvider->getMeter($serviceName, $serviceVersion);

            return new OpenTelemetryProvider($tracer, $meter);
        } catch (Throwable) {
            // Fall back to no-op if OpenTelemetry is not properly configured
            return new NoOpTelemetryProvider;
        }
    }

    /**
     * Create a no-operation telemetry provider.
     *
     * Returns a no-op implementation that performs no telemetry operations.
     * This is useful for testing, or when you explicitly want to disable
     * observability features.
     *
     * @return NoOpTelemetryProvider A no-op telemetry provider
     */
    public static function createNoOp(): NoOpTelemetryProvider
    {
        return new NoOpTelemetryProvider;
    }

    /**
     * Create a telemetry provider with custom OpenTelemetry instances.
     *
     * @param  TracerInterface       $tracer The OpenTelemetry tracer instance
     * @param  MeterInterface        $meter  The OpenTelemetry meter instance
     * @return OpenTelemetryProvider A telemetry provider instance
     */
    public static function createWithCustomProviders(
        TracerInterface $tracer,
        MeterInterface $meter,
    ): OpenTelemetryProvider {
        return new OpenTelemetryProvider($tracer, $meter);
    }
}
