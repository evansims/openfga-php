<?php

declare(strict_types=1);

namespace OpenFGA;

use OpenFGA\SDK\Configuration\ClientConfigurationInterface;

final class SDK
{
    public const string VERSION = '0.1.0';

    public function __construct(
        private ClientConfigurationInterface $configuration,
    ) {
        echo 'OpenFGA class loaded';
    }
}
