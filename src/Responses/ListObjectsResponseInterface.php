<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Schemas\SchemaInterface;

/**
 * Interface for object listing response objects.
 *
 * This interface defines the contract for responses returned when listing objects
 * that a user has access to in OpenFGA. This is the inverse of permission checking -
 * instead of asking "can this user access this object?", it asks "what objects can
 * this user access?".
 *
 * Object listing is particularly useful for building user interfaces that need to
 * display only the resources a user can access, such as file listings, document
 * repositories, or administrative dashboards.
 *
 * @see https://openfga.dev/docs/interacting/relationship-queries OpenFGA List Objects API Documentation
 */
interface ListObjectsResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * Returns the schema that defines the structure and validation rules for object
     * listing response data, ensuring consistent parsing and validation of API responses.
     *
     * @return SchemaInterface The schema definition for response validation
     */
    public static function schema(): SchemaInterface;

    /**
     * Get the array of object identifiers the user has access to.
     *
     * Returns an array of object identifiers that the queried user has the specified
     * relationship with. Each string represents an object ID of the requested type
     * that the user can access through the specified relation.
     *
     * @return array<int, string> Array of object identifiers the user has access to
     */
    public function getObjects(): array;
}
