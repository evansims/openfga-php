<?php

declare(strict_types=1);

namespace OpenFGA;

use OpenFGA\SDK\Configuration\ClientConfigurationInterface;

final class Client
{
    public function __construct(
        public ClientConfigurationInterface $configuration,
    ) {
    }
}
