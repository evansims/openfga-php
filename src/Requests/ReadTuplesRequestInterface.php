<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;

interface ReadTuplesRequestInterface extends RequestInterface
{
    public function getConsistency(): ?Consistency;

    public function getContinuationToken(): ?string;

    public function getPageSize(): ?int;

    public function getStore(): string;

    public function getTupleKey(): TupleKeyInterface;
}
