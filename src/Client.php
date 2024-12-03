<?php

declare(strict_types=1);

namespace OpenFGA;

use OpenFGA\API\Endpoints\{StoresEndpoint};
use OpenFGA\SDK\Configuration\ClientConfigurationInterface;

final class Client extends StoresEndpoint
{
    public const string VERSION = '0.1.0';

    public function __construct(
        public ClientConfigurationInterface $configuration,
    ) {
    }
}
