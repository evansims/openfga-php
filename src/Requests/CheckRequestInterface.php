<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{TupleKeyInterface, TupleKeysInterface};
use OpenFGA\Options\CheckOptionsInterface;

interface CheckRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): string;

    public function getContext(): ?object;

    public function getContextualTuples(): ?TupleKeysInterface;

    public function getOptions(): ?CheckOptionsInterface;

    public function getStore(): string;

    public function getTrace(): ?bool;

    public function getTupleKey(): TupleKeyInterface;
}
