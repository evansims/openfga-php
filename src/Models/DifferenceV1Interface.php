<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Defines a difference operation between two usersets in authorization models.
 *
 * DifferenceV1 represents a set operation that computes "base minus subtract",
 * effectively granting access to users in the base userset while explicitly
 * denying access to users in the subtract userset. This enables complex
 * authorization patterns like "all employees except contractors" or
 * "organization members except suspended users".
 *
 * Use this interface when implementing authorization logic that requires
 * explicit exclusion of certain users from a broader permission set.
 */
interface DifferenceV1Interface extends ModelInterface
{
    /**
     * Get the base userset from which users will be subtracted.
     *
     * This represents the initial set of users or relationships from which
     * the subtract userset will be removed to compute the final difference.
     *
     * @return UsersetInterface The base userset for the difference operation
     */
    public function getBase(): UsersetInterface;

    /**
     * Get the userset of users to subtract from the base userset.
     *
     * This represents the set of users or relationships that should be removed
     * from the base userset to compute the final result of the difference operation.
     *
     * @return UsersetInterface The userset to subtract from the base
     */
    public function getSubtract(): UsersetInterface;

    /**
     * @return array{base: array{
     *     computedUserset?: array{object?: string, relation?: string},
     *     tupleToUserset?: array{tupleset: array{object?: string, relation?: string}, computedUserset: array{object?: string, relation?: string}},
     *     union?: array<mixed>,
     *     intersection?: array<mixed>,
     *     difference?: array{base: array<mixed>, subtract: array<mixed>},
     *     this?: object,
     * }, subtract: array{
     *     computedUserset?: array{object?: string, relation?: string},
     *     tupleToUserset?: array{tupleset: array{object?: string, relation?: string}, computedUserset: array{object?: string, relation?: string}},
     *     union?: array<mixed>,
     *     intersection?: array<mixed>,
     *     difference?: array{base: array<mixed>, subtract: array<mixed>},
     *     this?: object,
     * }}
     */
    #[Override]
    public function jsonSerialize(): array;
}
