<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\UsersetTreeInterface;
use OpenFGA\Schema\SchemaInterface;

/**
 * Interface for relationship expansion response objects.
 *
 * This interface defines the contract for responses returned when expanding relationships
 * in OpenFGA. An expand response contains a tree structure that shows all the users and
 * usersets that have a particular relationship with an object, providing a comprehensive
 * view of the authorization graph.
 *
 * Relationship expansion is useful for understanding complex authorization structures,
 * debugging permission issues, and visualizing how relationships are resolved.
 *
 * @see UsersetTreeInterface The tree structure containing expansion results
 * @see https://openfga.dev/docs/interacting/relationship-queries OpenFGA Expand API Documentation
 */
interface ExpandResponseInterface extends ResponseInterface
{
    /**
     * Get the schema definition for this response.
     *
     * Returns the schema that defines the structure and validation rules for relationship
     * expansion response data, ensuring consistent parsing and validation of API responses.
     *
     * @return SchemaInterface The schema definition for response validation
     */
    public static function schema(): SchemaInterface;

    /**
     * Get the expansion tree for the queried relationship.
     *
     * Returns a hierarchical tree structure that represents all users and usersets
     * that have the specified relationship with the target object. The tree shows
     * both direct relationships and computed relationships through other relations.
     *
     * @return UsersetTreeInterface|null The relationship expansion tree, or null if no relationships found
     */
    public function getTree(): ?UsersetTreeInterface;
}
