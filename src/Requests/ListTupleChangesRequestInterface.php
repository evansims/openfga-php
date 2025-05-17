<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use DateTimeImmutable;

interface ListTupleChangesRequestInterface extends RequestInterface
{
    public function getContinuationToken(): ?string;

    public function getPageSize(): ?int;

    public function getStartTime(): ?DateTimeImmutable;

    public function getStore(): string;

    public function getType(): ?string;
}
