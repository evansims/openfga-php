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
     *     computed_userset?: array{object?: string, relation?: string},
     *     tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}},
     *     union?: array<mixed>,
     *     intersection?: array<mixed>,
     *     difference?: array{base: array<mixed>, subtract: array<mixed>},
     *     direct?: object,
     * }
     */
    #[Override]
    public function jsonSerialize(): array;
}
