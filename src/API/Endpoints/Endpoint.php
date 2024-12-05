<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\ClientInterface;
use OpenFGA\SDK\Configuration\ClientConfigurationInterface;

abstract class Endpoint
{
    protected function getClient(): ClientInterface
    {
        /** @var ClientInterface $this */
        return $this;
    }
}
