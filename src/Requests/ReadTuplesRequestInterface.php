<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Options\ReadTuplesOptionsInterface;

interface ReadTuplesRequestInterface extends RequestInterface
{
    public function getOptions(): ?ReadTuplesOptionsInterface;

    public function getStore(): string;

    public function getTupleKey(): TupleKeyInterface;
}
