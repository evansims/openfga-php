<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

/**
 * Interface for store deletion response objects.
 *
 * This interface defines the contract for responses returned when deleting stores
 * from OpenFGA. Store deletion responses typically contain no additional data beyond
 * the successful HTTP status, indicating that the store has been marked for deletion.
 *
 * Store deletion is a destructive operation that removes all authorization data
 * associated with the store, including relationship tuples and authorization models.
 *
 * @see https://openfga.dev/api/service OpenFGA Delete Store API Documentation
 */
interface DeleteStoreResponseInterface extends ResponseInterface
{
}
