<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Schemas\SchemaInterface;

/**
 * Interface for permission check response objects.
 *
 * This interface defines the contract for responses returned when performing permission
 * checks in OpenFGA. A check response indicates whether a specific user has a particular
 * permission on a given object, based on the authorization model and current relationship data.
 *
 * Permission checking is the core operation of OpenFGA, allowing applications to make
 * authorization decisions by evaluating user permissions against the defined relationship
 * model and stored tuples.
 *
 * @see https://openfga.dev/docs/interacting/relationship-queries OpenFGA Check API Documentation
 */
interface CheckResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * This method returns the schema that defines the structure and validation rules
     * for check response data, ensuring consistent parsing and validation.
     *
     * @return SchemaInterface The schema definition for check response validation
     */
    public static function schema(): SchemaInterface;

    /**
     * Get whether the permission check was allowed.
     *
     * This is the primary result of the permission check operation, indicating whether the
     * specified user has the requested permission on the given object according to the
     * authorization model and current relationship data.
     *
     * @return bool|null True if permission is granted, false if denied, or null if the result is indeterminate
     */
    public function getAllowed(): ?bool;

    /**
     * Get the resolution details for the permission decision.
     *
     * This provides additional information about how the permission decision was reached,
     * which can be useful for understanding complex authorization logic or debugging
     * permission issues.
     *
     * @return string|null The resolution details explaining the permission decision, or null if not provided
     */
    public function getResolution(): ?string;
}
