<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;

interface CheckRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): string;

    public function getConsistency(): ?Consistency;

    public function getContext(): ?object;

    /**
     * @return TupleKeysInterface<TupleKeyInterface>
     */
    public function getContextualTuples(): ?TupleKeysInterface;

    public function getStore(): string;

    public function getTrace(): ?bool;

    public function getTupleKey(): TupleKeyInterface;
}
