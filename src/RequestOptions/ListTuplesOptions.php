<?php

declare(strict_types=1);

namespace OpenFGA\RequestOptions;

use OpenFGA\Models\Consistency;
use OpenFGA\Models\ContinuationTokenInterface;

final class ListTuplesOptions extends RequestOptions
{
    use RequestOptionsTrait;

    public function __construct(
        private ?ContinuationTokenInterface $continuationToken = null,
        private ?int $pageSize = null,
        private ?Consistency $consistency = null,
    ) {
    }

    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
    }

    public function getContinuationToken(): ?ContinuationTokenInterface
    {
        return $this->continuationToken;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }
}
