<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Options\CreateStoreOptionsInterface;

interface CreateStoreRequestInterface extends RequestInterface
{
    public function getName(): string;

    public function getOptions(): ?CreateStoreOptionsInterface;
}
