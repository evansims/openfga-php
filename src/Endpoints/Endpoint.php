<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\ClientInterface;

/**
 * @template T of ClientInterface
 */
abstract class Endpoint
{
    /**
     * Get the client instance.
     *
     * This method is intended to be used by traits that extend classes implementing ClientInterface.
     * The implementing class is expected to be a ClientInterface instance.
     *
     * @return T
     */
    abstract protected function getClient(): ClientInterface;
}
