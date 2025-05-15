<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\TupleKeysInterface;
use OpenFGA\Options\WriteTuplesOptionsInterface;

interface WriteTuplesRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): string;

    public function getDeletes(): ?TupleKeysInterface;

    public function getOptions(): ?WriteTuplesOptionsInterface;

    public function getStore(): string;

    public function getWrites(): ?TupleKeysInterface;
}
