<?php

declare(strict_types=1);

namespace OpenFGA\RequestOptions;

use OpenFGA\Models\Consistency;

final class ListTuplesOptions extends RequestOptions
{
    public function __construct(
        private ?string $continuationToken = null,
        private ?int $pageSize = null,
        private ?Consistency $consistency = null,
    ) {
    }

    public function getConsistency(): ?Consistency
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
