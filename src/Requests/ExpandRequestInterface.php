<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{TupleKeyInterface, TupleKeysInterface};
use OpenFGA\Options\ExpandOptionsInterface;

interface ExpandRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): ?string;

    public function getContextualTuples(): ?TupleKeysInterface;

    public function getOptions(): ?ExpandOptionsInterface;

    public function getStore(): string;

    public function getTupleKey(): TupleKeyInterface;
}
