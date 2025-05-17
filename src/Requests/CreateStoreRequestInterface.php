<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

interface CreateStoreRequestInterface extends RequestInterface
{
    public function getName(): string;
}
