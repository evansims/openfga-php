<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Options\GetStoreOptionsInterface;

interface GetStoreRequestInterface extends RequestInterface
{
    public function getOptions(): ?GetStoreOptionsInterface;

    public function getStore(): string;
}
