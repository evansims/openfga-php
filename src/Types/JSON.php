<?php

declare(strict_types=1);

namespace OpenFGA\Types;

final class JSON
{
    public function __construct(
        private string $json,
    ) {
    }
}
