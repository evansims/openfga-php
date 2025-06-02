<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use Override;

/**
 * Defines the contract for assertion tuple keys used in authorization model testing.
 *
 * An assertion tuple key specifies the user, relation, and object combination
 * that should be tested in authorization model assertions. This is used to
 * verify that your authorization model behaves correctly for specific scenarios.
 *
 * Use this when creating test cases to validate your authorization rules
 * and ensure your permission model works as expected.
 */
interface AssertionTupleKeyInterface extends ModelInterface
{
    /**
     * Get the object being tested in the assertion.
     *
     * This represents the resource or entity that the assertion is testing
     * access to. In assertion testing, this is the object part of the
     * tuple being validated against the authorization model.
     *
     * @return string The object identifier being tested
     */
    public function getObject(): string;

    /**
     * Get the relation being tested in the assertion.
     *
     * This represents the type of relationship or permission being tested
     * in the assertion. It defines what kind of access is being validated
     * between the user and object.
     *
     * @return string The relation name being tested
     */
    public function getRelation(): string;

    /**
     * Get the user being tested in the assertion.
     *
     * This represents the subject (user, group, role, etc.) whose access
     * is being tested in the assertion. It's the entity for which we're
     * validating whether they have the specified relation to the object.
     *
     * @return string The user identifier being tested
     */
    public function getUser(): string;

    /**
     * @return array{
     *     user: string,
     *     relation: string,
     *     object: string,
     * }
     */
    #[Override]
    public function jsonSerialize(): array;
}
