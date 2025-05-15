<?php

declare(strict_types=1);

namespace OpenFGA\Options;

use OpenFGA\Models\Consistency;

interface ListObjectsOptionsInterface extends OptionsInterface
{
    public function getConsistency(): ?Consistency;
}
