<?php

declare(strict_types=1);

namespace OpenFGA\Network;

/**
 * HTTP request methods supported by the OpenFGA API.
 *
 * This enum defines the specific HTTP methods used for communicating with the OpenFGA
 * service, following standard HTTP semantics for different types of operations. Each
 * method corresponds to specific types of API operations based on their intended
 * semantic meaning and expected behavior.
 *
 * The OpenFGA API uses different HTTP methods to indicate the nature of the operation
 * being performed, following RESTful principles:
 * - GET for retrieving data without side effects
 * - POST for creating resources or performing operations with side effects
 * - PUT for updating or replacing existing resources
 * - DELETE for removing resources from the system
 *
 * Using the appropriate HTTP method ensures proper caching behavior, idempotency
 * characteristics, and compatibility with HTTP infrastructure components like
 * proxies, load balancers, and CDNs.
 *
 * @see https://openfga.dev/docs/api OpenFGA API Documentation
 * @see https://tools.ietf.org/html/rfc7231#section-4 HTTP Method Definitions
 */
enum RequestMethod: string
{
    /**
     * DELETE method for removing resources.
     *
     * Used for operations that remove resources from the OpenFGA system,
     * such as deleting stores, removing relationship tuples, or clearing
     * authorization data. DELETE operations are idempotent, meaning that
     * multiple identical requests have the same effect as a single request.
     *
     * Common OpenFGA operations using DELETE:
     * - Deleting authorization stores
     * - Removing relationship tuples
     * - Clearing assertion data
     */
    case DELETE = 'DELETE';

    /**
     * GET method for retrieving data.
     *
     * Used for operations that retrieve information from the OpenFGA system
     * without causing any side effects or state changes. GET requests are
     * safe and idempotent, making them suitable for caching and repeated
     * execution without concern for unintended consequences.
     *
     * Common OpenFGA operations using GET:
     * - Listing authorization stores
     * - Reading relationship tuples
     * - Retrieving authorization models
     * - Fetching store metadata
     */
    case GET = 'GET';

    /**
     * POST method for creating resources and performing operations.
     *
     * Used for operations that create new resources or perform actions that
     * may have side effects on the OpenFGA system. POST requests are neither
     * safe nor idempotent, as each request may create new resources or trigger
     * different system behaviors.
     *
     * Common OpenFGA operations using POST:
     * - Performing authorization checks
     * - Writing relationship tuples
     * - Creating authorization models
     * - Creating new stores
     * - Expanding relationship queries
     */
    case POST = 'POST';

    /**
     * PUT method for updating or replacing resources.
     *
     * Used for operations that update existing resources or create resources
     * with client-specified identifiers. PUT requests are idempotent, meaning
     * that multiple identical requests result in the same final system state.
     *
     * Common OpenFGA operations using PUT:
     * - Updating store metadata
     * - Replacing authorization model configurations
     * - Updating assertion data
     */
    case PUT = 'PUT';

    /**
     * Check if this HTTP method typically expects a request body.
     *
     * Useful for client implementations to determine whether to include
     * request body serialization and content-type headers.
     *
     * @return bool True if the method typically has a request body, false otherwise
     */
    public function hasRequestBody(): bool
    {
        return match ($this) {
            self::POST, self::PUT => true,
            self::GET, self::DELETE => false,
        };
    }

    /**
     * Check if this HTTP method is idempotent.
     *
     * Idempotent methods can be called multiple times with the same effect.
     * This is useful for retry logic and caching decisions in HTTP clients.
     *
     * @return bool True if the method is idempotent, false otherwise
     */
    public function isIdempotent(): bool
    {
        return match ($this) {
            self::GET, self::PUT, self::DELETE => true,
            self::POST => false,
        };
    }

    /**
     * Check if this HTTP method is safe.
     *
     * Safe methods do not modify server state and can be cached.
     * This is important for HTTP middleware and caching strategies.
     *
     * @return bool True if the method is safe, false otherwise
     */
    public function isSafe(): bool
    {
        return match ($this) {
            self::GET => true,
            self::POST, self::PUT, self::DELETE => false,
        };
    }
}
