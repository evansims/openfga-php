<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Options\DeleteStoreOptionsInterface;

interface DeleteStoreRequestInterface extends RequestInterface
{
    public function getOptions(): ?DeleteStoreOptionsInterface;

    public function getStore(): string;
}
