<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{ConditionsInterface, SchemaVersion, TypeDefinitionsInterface};
use OpenFGA\Options\CreateAuthorizationModelOptionsInterface;

interface CreateAuthorizationModelRequestInterface extends RequestInterface
{
    public function getConditions(): ?ConditionsInterface;

    public function getOptions(): ?CreateAuthorizationModelOptionsInterface;

    public function getSchemaVersion(): SchemaVersion;

    public function getStore(): string;

    public function getTypeDefinitions(): TypeDefinitionsInterface;
}
