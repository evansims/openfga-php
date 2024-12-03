<?php

declare(strict_types=1);

namespace OpenFGA\SDK;

use OpenFGA\Client;

final class Network
{
    public string $user_agent = sprintf('openfga-sdk php/%s', Client::VERSION);
}
