<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;

interface ExpandRequestInterface extends RequestInterface
{
    public function getConsistency(): ?Consistency;

    /**
     * @return TupleKeysInterface<TupleKeyInterface>
     */
    public function getContextualTuples(): ?TupleKeysInterface;

    public function getModel(): ?string;

    public function getStore(): string;

    public function getTupleKey(): TupleKeyInterface;
}
