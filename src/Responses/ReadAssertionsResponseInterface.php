<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{AssertionsInterface, AuthorizationModelIdInterface};

/**
 * @psalm-type ReadAssertionsResponseShape = array{authorization_model_id: string, assertions?: AssertionsInterface}
 */
interface ReadAssertionsResponseInterface extends ResponseInterface
{
    public function getAssertions(): ?AssertionsInterface;

    public function getAuthorizationModelId(): AuthorizationModelIdInterface;
}
