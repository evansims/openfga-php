<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{ConditionsInterface, SchemaVersion, TypeDefinitionsInterface};

interface CreateAuthorizationModelRequestInterface extends RequestInterface
{
    public function getConditions(): ?ConditionsInterface;

    public function getSchemaVersion(): SchemaVersion;

    public function getStore(): string;

    public function getTypeDefinitions(): TypeDefinitionsInterface;
}
