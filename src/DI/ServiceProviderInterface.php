<?php

declare(strict_types=1);

namespace OpenFGA\DI;

/**
 * Interface for dependency injection service providers.
 *
 * This interface defines the contract for service providers that manage
 * service registration, instantiation, and retrieval in the OpenFGA SDK.
 * It provides a clean abstraction for dependency injection while maintaining
 * simplicity and performance.
 *
 * Implementations should provide lazy loading of services and support both
 * singleton and factory patterns for service creation.
 */
interface ServiceProviderInterface
{
    /**
     * Register a service factory function.
     *
     * Registers a factory function that will be called to create the service
     * instance when first requested. The factory should return an object
     * instance of the expected service type.
     *
     * @param string              $serviceId The service identifier
     * @param callable(): ?object $factory   The factory function that returns an object or null
     */
    public function factory(string $serviceId, callable $factory): void;

    /**
     * Get a service instance by identifier.
     *
     * Retrieves the service instance for the specified identifier, creating
     * it if necessary using the registered factory. Services are cached after
     * first creation for performance.
     *
     * @param string $serviceId The service identifier
     *
     * @throws ServiceNotFoundException If the service is not registered
     *
     * @return object|null The service instance, or null if the service is optional and not available
     */
    public function get(string $serviceId): ?object;

    /**
     * Check if a service is registered.
     *
     * Returns true if the service identifier has been registered with the
     * provider, either as a concrete instance or as a factory function.
     *
     * @param  string $serviceId The service identifier to check
     * @return bool   True if the service is registered
     */
    public function has(string $serviceId): bool;

    /**
     * Register a concrete service instance.
     *
     * Registers a pre-instantiated service instance with the provider.
     * This is useful for registering singletons or test doubles.
     *
     * @param string $serviceId The service identifier
     * @param object $service   The service instance
     */
    public function set(string $serviceId, object $service): void;
}
