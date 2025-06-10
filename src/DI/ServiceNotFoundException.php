<?php

declare(strict_types=1);

namespace OpenFGA\DI;

use InvalidArgumentException;

use function sprintf;

/**
 * Exception thrown when a requested service is not found in the service provider.
 *
 * This exception is thrown when attempting to retrieve a service that has not
 * been registered with the service provider. It helps identify configuration
 * issues and missing service registrations during development.
 */
final class ServiceNotFoundException extends InvalidArgumentException
{
    /**
     * Create a new service not found exception.
     *
     * @param string $serviceId The service identifier that was not found
     */
    public function __construct(string $serviceId)
    {
        parent::__construct(sprintf("Service '%s' not found in service provider", $serviceId));
    }
}
