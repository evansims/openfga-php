<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\ClientInterface;
use OpenFGA\Configuration\ClientConfigurationInterface;

abstract class Endpoint
{
    protected function getClient(): ClientInterface
    {
        /** @var ClientInterface $this */
        return $this;
    }
}
