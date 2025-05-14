<?php

declare(strict_types=1);

namespace OpenFGA\RequestOptions;

use OpenFGA\Models\Consistency;

final class ListObjectsOptions extends RequestOptions
{
    public function __construct(
        private ?Consistency $consistency = null,
    ) {
    }

    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
    }

    public function getQueryParameters(): array
    {
        return [];
    }
}
