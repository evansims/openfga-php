<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\{ConditionInterface, TypeDefinitionInterface};
use OpenFGA\Models\Enums\SchemaVersion;

interface CreateAuthorizationModelRequestInterface extends RequestInterface
{
    /**
     * @return ConditionsInterface<ConditionInterface>
     */
    public function getConditions(): ?ConditionsInterface;

    public function getSchemaVersion(): SchemaVersion;

    public function getStore(): string;

    /**
     * @return TypeDefinitionsInterface<TypeDefinitionInterface>
     */
    public function getTypeDefinitions(): TypeDefinitionsInterface;
}
