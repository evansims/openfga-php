<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Configuration;

interface ConfigurationInterface
{
    public function validate(): void;
}
