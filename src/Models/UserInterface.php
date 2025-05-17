<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type UserShape = array{object?: object, userset?: UsersetUserShape, wildcard?: TypedWildcardShape, difference?: DifferenceV1Shape}
 */
interface UserInterface extends ModelInterface
{
    public function getDifference(): ?DifferenceV1Interface;

    public function getObject(): ?object;

    public function getUserset(): ?UsersetUserInterface;

    public function getWildcard(): ?TypedWildcardInterface;

    /**
     * @return UserShape
     */
    public function jsonSerialize(): array;
}
