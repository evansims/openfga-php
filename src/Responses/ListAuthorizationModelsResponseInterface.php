<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AuthorizationModelInterface;
use OpenFGA\Models\Collections\AuthorizationModelsInterface;
use OpenFGA\Schema\SchemaInterface;

interface ListAuthorizationModelsResponseInterface extends ResponseInterface
{
    /**
     * @return AuthorizationModelsInterface<AuthorizationModelInterface>
     */
    public function getAuthorizationModels(): AuthorizationModelsInterface;

    public function getContinuationToken(): ?string;

    public static function schema(): SchemaInterface;
}
