<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface AuthorizationModelInterface extends ModelInterface
{
    public function getConditions(): ?ConditionsInterface;

    public function getId(): string;

    public function getSchemaVersion(): string;

    public function getTypeDefinitions(): TypeDefinitionsInterface;
}
