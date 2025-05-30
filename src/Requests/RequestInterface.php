<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientThrowable};
use OpenFGA\Network\RequestContext;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Base interface for all OpenFGA API request objects.
 *
 * This interface defines the core contract that all OpenFGA API requests must implement.
 * Request objects encapsulate the parameters and configuration needed for specific API
 * operations, providing a structured way to prepare HTTP requests for the OpenFGA service.
 *
 * Each request implementation handles:
 * - Parameter validation and constraints
 * - Request body serialization and formatting
 * - HTTP method and endpoint determination
 * - Header configuration and content negotiation
 * - URL path construction with proper parameter encoding
 *
 * The interface follows the Command pattern, where each request object represents
 * a specific operation to be performed against the OpenFGA API. This design enables
 * consistent request handling, validation, and testing across all API operations.
 *
 * @see RequestContext HTTP request context abstraction
 * @see StreamFactoryInterface PSR-7 stream factory for request bodies
 * @see https://openfga.dev/docs/api OpenFGA API Documentation
 */
interface RequestInterface
{
    /**
     * Build a request context for HTTP execution.
     *
     * Transforms the request object into a standardized HTTP request context that
     * can be executed by the OpenFGA HTTP client. This method handles all aspects
     * of request preparation including parameter serialization, URL construction,
     * header configuration, and body stream creation.
     *
     * The method validates that all required parameters are present and properly
     * formatted, serializes complex objects to JSON, constructs the appropriate
     * API endpoint URL, and creates the necessary HTTP message body streams.
     *
     * @param StreamFactoryInterface $streamFactory PSR-7 stream factory for creating request body streams from serialized data
     *
     * @throws InvalidArgumentException If request parameters are invalid
     * @throws JsonException            If request serialization fails
     * @throws ClientThrowable          If request validation fails or required parameters are missing
     *
     * @return RequestContext The prepared request context containing HTTP method, URL, headers, and body ready for execution
     */
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext;
}
