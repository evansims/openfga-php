<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Options\GetAuthorizationModelOptionsInterface;

interface GetAuthorizationModelRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): string;

    public function getOptions(): ?GetAuthorizationModelOptionsInterface;

    public function getStore(): string;
}
