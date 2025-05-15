<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Options\ReadAssertionsOptionsInterface;

interface ReadAssertionsRequestInterface extends RequestInterface
{
    public function getAuthorizationModel(): string;

    public function getOptions(): ?ReadAssertionsOptionsInterface;

    public function getStore(): string;
}
