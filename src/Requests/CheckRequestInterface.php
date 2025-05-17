<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{Consistency, TupleKeyInterface, TupleKeysInterface};

interface CheckRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): string;

    public function getConsistency(): ?Consistency;

    public function getContext(): ?object;

    public function getContextualTuples(): ?TupleKeysInterface;

    public function getStore(): string;

    public function getTrace(): ?bool;

    public function getTupleKey(): TupleKeyInterface;
}
