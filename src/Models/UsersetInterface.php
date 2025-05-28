<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\UsersetsInterface;
use Override;

interface UsersetInterface extends ModelInterface
{
    public function getComputedUserset(): ?ObjectRelationInterface;

    public function getDifference(): ?DifferenceV1Interface;

    public function getDirect(): ?object;

    /**
     * @return null|UsersetsInterface<UsersetInterface>
     */
    public function getIntersection(): ?UsersetsInterface;

    public function getTupleToUserset(): ?TupleToUsersetV1Interface;

    /**
     * @return null|UsersetsInterface<UsersetInterface>
     */
    public function getUnion(): ?UsersetsInterface;

    /**
     * @return array{
     *     computedUserset?: array{object?: string, relation?: string},
     *     tupleToUserset?: array{tupleset: array{object?: string, relation?: string}, computedUserset: array{object?: string, relation?: string}},
     *     union?: array<mixed>,
     *     intersection?: array<mixed>,
     *     difference?: array{base: array<mixed>, subtract: array<mixed>},
     *     this?: object,
     * }
     */
    #[Override]
    public function jsonSerialize(): array;
}
