<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Psr\Http\Message\StreamInterface;

/**
 * Represents the context for an HTTP request to the OpenFGA API.
 *
 * This interface encapsulates all the essential components needed to construct and
 * execute HTTP requests to the OpenFGA service. It provides a structured way to
 * manage request metadata including HTTP method, target URL, request body, headers,
 * and routing configurations that determine how the request is processed.
 *
 * The request context serves as an abstraction layer between the high-level OpenFGA
 * operations (like authorization checks, relationship writes, etc.) and the low-level
 * HTTP communication details. This separation enables:
 * - Consistent request formatting across different API operations
 * - Centralized management of authentication and headers
 * - Flexible URL routing and API endpoint resolution
 * - Testable and mockable HTTP communication layer
 *
 * Request contexts are typically created by the OpenFGA client during API operations
 * and passed to the RequestManager for actual HTTP execution. The context includes
 * both required elements (method, URL) and optional elements (body, custom headers)
 * that may vary depending on the specific API operation being performed.
 *
 * @see RequestManagerInterface HTTP request execution
 * @see StreamInterface PSR-7 message body streams
 * @see https://openfga.dev/docs/api OpenFGA API Documentation
 */
interface RequestContextInterface
{
    /**
     * Get the request body stream.
     *
     * Returns the PSR-7 stream containing the request body data for operations
     * that require sending data to the OpenFGA API. The body typically contains
     * JSON-encoded request parameters for operations like writing relationships,
     * creating authorization models, or checking permissions.
     *
     * Operations that only retrieve data (such as reading relationships or
     * listing stores) typically have no body content and will return null.
     *
     * @return StreamInterface|null The request body stream containing JSON data, or null for operations without body content
     */
    public function getBody(): ?StreamInterface;

    /**
     * Get the request headers.
     *
     * Returns an associative array of HTTP headers that should be included with
     * the request. This typically includes content-type headers, authentication
     * headers, and any custom headers required for specific API operations.
     *
     * Headers are merged with default headers provided by the RequestManager,
     * with context-specific headers taking precedence over defaults. Common
     * headers include Content-Type for JSON requests and Authorization for
     * API authentication.
     *
     * @return array<string, string> Associative array mapping header names to their values
     */
    public function getHeaders(): array;

    /**
     * Get the HTTP method for the request.
     *
     * Returns the HTTP method that should be used for this API operation.
     * Different OpenFGA operations use different HTTP methods based on their
     * semantic meaning:
     * - GET for reading data (listing stores, reading relationships)
     * - POST for creating or querying (authorization checks, writing relationships)
     * - PUT for updating existing resources
     * - DELETE for removing resources
     *
     * @return RequestMethod The HTTP method enum value indicating the request type
     */
    public function getMethod(): RequestMethod;

    /**
     * Get the URL for the request.
     *
     * Returns the target URL path for this API operation. This is typically
     * a relative path that gets combined with the base API URL to form the
     * complete request URL. For example, "/stores" for listing stores or
     * "/stores/{store_id}/check" for authorization checks.
     *
     * The URL may contain path parameters that have been resolved with actual
     * values (like store IDs or model IDs) before being included in the context.
     *
     * @return string The target URL path for the API operation
     */
    public function getUrl(): string;

    /**
     * Determine if the API URL should be used as a prefix.
     *
     * Controls whether the base API URL should be prepended to the request URL.
     * Most OpenFGA API operations use the standard API base URL, but some
     * operations (like health checks or custom endpoints) might use alternative
     * base URLs or absolute URLs.
     *
     * When true, the RequestManager will prepend the configured API base URL
     * to the request URL. When false, the URL is used as-is, allowing for
     * complete URL override when necessary.
     *
     * @return bool True if the API base URL should be prepended to the request URL, false to use the URL as-is
     */
    public function useApiUrl(): bool;
}
