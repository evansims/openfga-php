<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{Consistency, TupleKeyInterface, TupleKeysInterface};

interface ExpandRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): ?string;

    public function getConsistency(): ?Consistency;

    public function getContextualTuples(): ?TupleKeysInterface;

    public function getStore(): string;

    public function getTupleKey(): TupleKeyInterface;
}
