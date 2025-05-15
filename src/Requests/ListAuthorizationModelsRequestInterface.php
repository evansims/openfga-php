<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Options\ListAuthorizationModelsOptionsInterface;

interface ListAuthorizationModelsRequestInterface extends RequestInterface
{
    public function getOptions(): ?ListAuthorizationModelsOptionsInterface;

    public function getStore(): string;
}
