<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

interface ReadAssertionsRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): string;

    public function getStore(): string;
}
