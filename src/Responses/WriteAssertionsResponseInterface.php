<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

/**
 * Interface for assertions writing response objects.
 *
 * This interface defines the contract for responses returned when writing assertions
 * to an OpenFGA authorization model. Assertion writing responses typically contain no
 * additional data beyond the successful HTTP status, indicating that the assertions
 * have been successfully stored.
 *
 * Assertions are test cases that validate the behavior of authorization models by
 * specifying expected permission check results, helping ensure model correctness.
 *
 * @see https://openfga.dev/api/service OpenFGA Write Assertions API Documentation
 */
interface WriteAssertionsResponseInterface extends ResponseInterface
{
}
