<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;

interface ListObjectsRequestInterface extends RequestInterface
{
    public function getConsistency(): ?Consistency;

    public function getContext(): ?object;

    /**
     * @return TupleKeysInterface<TupleKeyInterface>
     */
    public function getContextualTuples(): ?TupleKeysInterface;

    public function getModel(): ?string;

    public function getRelation(): string;

    public function getStore(): string;

    public function getType(): string;

    public function getUser(): string;
}
