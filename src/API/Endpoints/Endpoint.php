<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\SDK\Configuration\ClientConfigurationInterface;

abstract class Endpoint
{
    public ClientConfigurationInterface $configuration;
}
