<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

interface UserInterface extends ModelInterface
{
    public function getDifference(): ?DifferenceV1Interface;

    public function getObject(): null | UserObjectInterface | string;

    public function getUserset(): ?UsersetUserInterface;

    public function getWildcard(): ?TypedWildcardInterface;

    /**
     * @return array{object?: mixed, userset?: array{type: string, id: string, relation: string}, wildcard?: array{type: string}, difference?: array<string, mixed>}
     */
    #[Override]
    public function jsonSerialize(): array;
}
