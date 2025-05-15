<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\TupleKeysInterface;
use OpenFGA\Options\ListObjectsOptionsInterface;

interface ListObjectsRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): ?string;

    public function getContext(): ?object;

    public function getContextualTuples(): ?TupleKeysInterface;

    public function getOptions(): ?ListObjectsOptionsInterface;

    public function getRelation(): string;

    public function getStore(): string;

    public function getType(): string;

    public function getUser(): string;
}
