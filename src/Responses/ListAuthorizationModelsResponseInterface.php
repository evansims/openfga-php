<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AuthorizationModelInterface;
use OpenFGA\Models\Collections\AuthorizationModelsInterface;
use OpenFGA\Schema\SchemaInterface;

interface ListAuthorizationModelsResponseInterface extends ResponseInterface
{
    public function getContinuationToken(): ?string;

    /**
     * @return AuthorizationModelsInterface<AuthorizationModelInterface>
     */
    public function getModels(): AuthorizationModelsInterface;

    public static function schema(): SchemaInterface;
}
