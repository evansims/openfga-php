<?php

declare(strict_types=1);

namespace OpenFGA\Options;

use OpenFGA\Models\Consistency;

final class ListObjectsOptions implements ListObjectsOptionsInterface
{
    use OptionsTrait;

    public function __construct(
        private ?Consistency $consistency = null,
    ) {
    }

    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
    }
}
