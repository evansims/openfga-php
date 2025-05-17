<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{Consistency, TupleKeysInterface};

interface ListObjectsRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): ?string;

    public function getConsistency(): ?Consistency;

    public function getContext(): ?object;

    public function getContextualTuples(): ?TupleKeysInterface;

    public function getRelation(): string;

    public function getStore(): string;

    public function getType(): string;

    public function getUser(): string;
}
