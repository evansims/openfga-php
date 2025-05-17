<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type UsersetShape = array{computed_userset?: ObjectRelationShape, tuple_to_userset?: TupleToUsersetShape, union?: list<UsersetShape>, intersection?: list<UsersetShape>, difference?: DifferenceShape, direct?: object}
 */
interface UsersetInterface extends ModelInterface
{
    /**
     * @return null|ObjectRelationInterface
     */
    public function getComputedUserset(): ?ObjectRelationInterface;

    /**
     * @return null|DifferenceV1Interface
     */
    public function getDifference(): ?DifferenceV1Interface;

    /**
     * @return null|object
     */
    public function getDirect(): ?object;

    /**
     * @return null|UsersetsInterface
     */
    public function getIntersection(): ?UsersetsInterface;

    /**
     * @return null|TupleToUsersetV1Interface
     */
    public function getTupleToUserset(): ?TupleToUsersetV1Interface;

    /**
     * @return null|UsersetsInterface
     */
    public function getUnion(): ?UsersetsInterface;

    /**
     * @return UsersetShape
     */
    public function jsonSerialize(): array;
}
