<?php

declare(strict_types=1);

namespace OpenFGA\RequestOptions;

use OpenFGA\Models\ConsistencyPreference;

final class CheckOptions extends RequestOptions
{
    public function __construct(
        private ?ConsistencyPreference $consistency = null,
    ) {
    }

    public function getConsistency(): ?ConsistencyPreference
    {
        return $this->consistency;
    }

    public function getQueryParameters(): array
    {
        return [];
    }
}
