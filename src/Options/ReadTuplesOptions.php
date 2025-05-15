<?php

declare(strict_types=1);

namespace OpenFGA\Options;

use OpenFGA\Models\Consistency;

final class ReadTuplesOptions implements ReadTuplesOptionsInterface
{
    use OptionsTrait;

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
}
