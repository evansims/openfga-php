<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

interface GetAuthorizationModelRequestInterface extends RequestInterface
{
    public function getModel(): string;

    public function getStore(): string;
}
