<?php

declare(strict_types=1);

namespace OpenFGA\Integration;

use ArrayAccess;
use OpenFGA\{ClientInterface, Transformer, TransformerInterface};
use OpenFGA\Network\{RequestManagerInterface};
use OpenFGA\Observability\{NoOpTelemetryProvider, TelemetryInterface};
use OpenFGA\Schema\{SchemaValidator, SchemaValidatorInterface};

/**
 * Service provider for automatic dependency injection container registration.
 *
 * This class enables automatic registration of OpenFGA services in frameworks
 * that support the tbachert/spi service provider interface pattern.
 *
 * @see https://packagist.org/packages/tbachert/spi
 */
final readonly class ServiceProvider
{
    /**
     * Register OpenFGA services with the dependency injection container.
     *
     * This method registers the core OpenFGA interfaces with their default
     * implementations, enabling automatic dependency resolution in SPI-compatible
     * frameworks.
     *
     * Note: This service provider registers basic implementations that work
     * without configuration. For production use, you should override these
     * registrations with properly configured instances.
     *
     * Services registered:
     * - TelemetryInterface: No-op telemetry provider (can be overridden)
     * - TransformerInterface: DSL to model transformation
     * - SchemaValidatorInterface: JSON schema validation for models
     *
     * Services NOT registered (require configuration):
     * - ClientInterface: Requires URL and authentication configuration
     * - RequestManagerInterface: Requires URL and retry configuration
     *
     * @param object $container The dependency injection container
     */
    public function register(object $container): void
    {
        // Register telemetry (defaults to no-op, can be overridden)
        $this->registerService($container, TelemetryInterface::class, static fn (): NoOpTelemetryProvider => new NoOpTelemetryProvider);

        // Register DSL transformer
        $this->registerService($container, TransformerInterface::class, static fn (): Transformer => new Transformer);

        // Register schema validator
        $this->registerService($container, SchemaValidatorInterface::class, static fn (): SchemaValidator => new SchemaValidator);
    }

    /**
     * Register a service with the container using common DI container methods.
     *
     * This method attempts to register services using common dependency injection
     * container interfaces and methods. It gracefully handles containers that
     * may not implement all methods.
     *
     * @param object             $container The dependency injection container
     * @param string             $interface The service interface or class name
     * @param callable(): object $factory   Factory function to create the service instance
     */
    private function registerService(object $container, string $interface, callable $factory): void
    {
        // Try PSR-11 Container interface methods
        if (method_exists($container, 'set')) {
            $container->set($interface, $factory);

            return;
        }

        // Try common DI container methods
        if (method_exists($container, 'bind')) {
            $container->bind($interface, $factory);

            return;
        }

        if (method_exists($container, 'singleton')) {
            $container->singleton($interface, $factory);

            return;
        }

        if (method_exists($container, 'register')) {
            $container->register($interface, $factory);

            return;
        }

        // For containers that support array access
        if ($container instanceof ArrayAccess) {
            $container[$interface] = $factory;

            return;
        }

        // If no supported method is found, silently skip registration
        // This allows the service provider to be loaded without breaking
        // in environments that don't support automatic registration
    }
}
