<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

interface GetStoreRequestInterface extends RequestInterface
{
    public function getStore(): string;
}
