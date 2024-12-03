<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Types;

final class JSON
{
    public function __construct(
        private string $json,
    ) {
        echo 'OpenFGA class loaded';
    }
}
