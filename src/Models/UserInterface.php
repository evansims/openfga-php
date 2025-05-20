<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface UserInterface extends ModelInterface
{
    public function getObject(): ?object;

    public function getUserset(): ?UsersetUserInterface;

    public function getWildcard(): ?TypedWildcardInterface;

    /**
     * @return array{object?: mixed, userset?: array{type: string, id: string, relation: string}, wildcard?: array{type: string}}
     */
    public function jsonSerialize(): array;
}
