<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Options\ListTupleChangesOptionsInterface;

interface ListTupleChangesRequestInterface extends RequestInterface
{
    public function getOptions(): ?ListTupleChangesOptionsInterface;

    public function getStore(): string;
}
