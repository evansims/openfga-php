<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

/**
 * Interface for tuple writing response objects.
 *
 * This interface defines the contract for responses returned when writing relationship
 * tuples to an OpenFGA store. Tuple writing responses typically contain no additional
 * data beyond the successful HTTP status, indicating that the write and delete operations
 * have been successfully applied.
 *
 * Tuple writing operations are transactional, meaning either all changes succeed or
 * all changes are rolled back, ensuring data consistency in the authorization graph.
 *
 * @see https://openfga.dev/docs/interacting/managing-relationships-between-objects OpenFGA Write Tuples API Documentation
 */
interface WriteTuplesResponseInterface extends ResponseInterface
{
}
