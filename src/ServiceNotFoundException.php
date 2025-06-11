<?php

declare(strict_types=1);

namespace OpenFGA;

use InvalidArgumentException;

use function sprintf;

/**
 * Exception thrown when a requested service is not found in the configuration.
 *
 * This exception is thrown by configuration providers when attempting to retrieve
 * a service that has not been registered or is not available.
 */
final class ServiceNotFoundException extends InvalidArgumentException
{
    /**
     * Create a new service not found exception.
     *
     * @param string $serviceId The identifier of the service that was not found
     */
    public function __construct(string $serviceId)
    {
        parent::__construct(sprintf(
            'Service "%s" not found. Please ensure the service is registered with the configuration provider.',
            $serviceId,
        ));
    }
}
