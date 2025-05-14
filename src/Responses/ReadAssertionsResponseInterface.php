<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{AssertionsInterface, AuthorizationModelIdInterface};

interface ReadAssertionsResponseInterface extends ResponseInterface
{
    public function getAssertions(): ?AssertionsInterface;

    public function getAuthorizationModelId(): AuthorizationModelIdInterface;
}
