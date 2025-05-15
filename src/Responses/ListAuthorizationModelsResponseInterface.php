<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AuthorizationModelsInterface;
use OpenFGA\Schema\SchemaInterface;

interface ListAuthorizationModelsResponseInterface extends ResponseInterface
{
    public function getAuthorizationModels(): AuthorizationModelsInterface;

    public function getContinuationToken(): ?string;

    public static function Schema(): SchemaInterface;
}
