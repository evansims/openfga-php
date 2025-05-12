<?php

declare(strict_types=1);

namespace OpenFGA\RequestOptions;

use OpenFGA\Models\ConsistencyPreference;

final class ListTuplesOptions extends RequestOptions
{
    public function __construct(
        private ?string $continuationToken = null,
        private ?int $pageSize = null,
        private ?ConsistencyPreference $consistency = null,
    ) {
    }

    public function getConsistency(): ?ConsistencyPreference
    {
        return $this->consistency;
    }

    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    public function getQueryParameters(): array
    {
        return [];
    }
}
