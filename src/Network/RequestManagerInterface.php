<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientThrowable};
use OpenFGA\Requests\RequestInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface as HttpRequestInterface, ResponseFactoryInterface, ResponseInterface, StreamFactoryInterface};

/**
 * Manages HTTP requests and responses for OpenFGA API communication.
 *
 * This interface defines the core HTTP communication layer for the OpenFGA SDK,
 * responsible for translating high-level API operations into HTTP requests and
 * processing the responses. It handles all aspects of HTTP communication including
 * request construction, authentication, error handling, and response processing.
 *
 * The RequestManager serves as the bridge between the OpenFGA client's domain-specific
 * operations and the underlying HTTP transport layer. It abstracts away the complexities
 * of HTTP communication while providing a clean, testable interface for API interactions.
 *
 * Key responsibilities include:
 * - Converting OpenFGA requests to PSR-7 HTTP requests
 * - Managing HTTP client configuration and PSR component integration
 * - Handling authentication headers and API credentials
 * - Executing HTTP requests with retry logic and timeout management
 * - Processing HTTP responses and converting errors to appropriate exceptions
 * - Providing comprehensive error context for debugging and monitoring
 *
 * The implementation uses PSR-7 HTTP message interfaces for maximum compatibility
 * with existing PHP HTTP ecosystems and follows PSR-18 HTTP client standards
 * for pluggable HTTP transport implementations.
 *
 * @see RequestContextInterface Request context abstraction
 * @see ClientInterface PSR-18 HTTP client standard
 * @see HttpRequestInterface PSR-7 request messages
 * @see ResponseInterface PSR-7 response messages
 * @see https://openfga.dev/docs/api OpenFGA API Documentation
 */
interface RequestManagerInterface
{
    /**
     * Handle error responses by throwing appropriate exceptions.
     *
     * Analyzes HTTP error responses to determine the specific type of error and
     * throws the most appropriate exception with comprehensive context information.
     * This method processes:
     * - HTTP status codes (400, 401, 403, 404, 409, 422, 500, etc.)
     * - Error response bodies containing detailed error information
     * - OpenFGA-specific error codes and messages
     * - Request context for debugging purposes
     *
     * The method provides structured error information that applications can use
     * for error handling, user messaging, and debugging. Different exception types
     * are thrown based on the error category to enable appropriate handling strategies.
     *
     * Common error scenarios include:
     * - 400 Bad Request: Invalid request parameters or malformed data
     * - 401 Unauthorized: Missing or invalid authentication credentials
     * - 403 Forbidden: Valid credentials but insufficient permissions
     * - 404 Not Found: Requested resource (store, model) does not exist
     * - 409 Conflict: Request conflicts with current resource state
     * - 422 Unprocessable Entity: Request timeout or processing limits exceeded
     * - 500 Internal Server Error: Server-side processing failures
     *
     * @param ResponseInterface         $response The HTTP error response to analyze and convert to an exception
     * @param HttpRequestInterface|null $request  Optional request context for enhanced error reporting
     *
     * @throws ClientThrowable For all HTTP error status codes with detailed error context, request information, and suggested remediation
     *
     * @return never This method always throws an exception and never returns normally
     */
    public static function handleResponseException(ResponseInterface $response, ?HttpRequestInterface $request = null): never;

    /**
     * Get the configured PSR-18 HTTP client.
     *
     * Returns the HTTP client instance used for executing requests to the OpenFGA API.
     * The client handles the actual network communication and can be any PSR-18 compatible
     * implementation such as Guzzle, cURL, or others. If no client was explicitly provided
     * during construction, the RequestManager will attempt to discover one automatically
     * using PSR Discovery.
     *
     * The HTTP client is responsible for network-level concerns including connection
     * management, SSL/TLS handling, timeout enforcement, and low-level HTTP protocol
     * implementation.
     *
     * @throws ClientThrowable If no HTTP client is configured and auto-discovery fails
     *
     * @return ClientInterface The PSR-18 HTTP client instance for executing requests
     */
    public function getHttpClient(): ClientInterface;

    /**
     * Get the configured PSR-17 HTTP request factory.
     *
     * Returns the factory used for creating PSR-7 HTTP request objects. This factory
     * is used to construct HTTP requests from OpenFGA request contexts, including
     * setting the appropriate method, URI, headers, and body content.
     *
     * If no factory was explicitly provided during construction, the RequestManager
     * will attempt to discover one automatically using PSR Discovery.
     *
     * @throws ClientThrowable If no request factory is configured and auto-discovery fails
     *
     * @return RequestFactoryInterface The PSR-17 factory for creating HTTP request objects
     */
    public function getHttpRequestFactory(): RequestFactoryInterface;

    /**
     * Get the configured PSR-17 HTTP response factory.
     *
     * Returns the factory used for creating PSR-7 HTTP response objects. This is
     * primarily used for testing and mocking scenarios where custom responses
     * need to be constructed programmatically.
     *
     * If no factory was explicitly provided during construction, the RequestManager
     * will attempt to discover one automatically using PSR Discovery.
     *
     * @throws ClientThrowable If no response factory is configured and auto-discovery fails
     *
     * @return ResponseFactoryInterface The PSR-17 factory for creating HTTP response objects
     */
    public function getHttpResponseFactory(): ResponseFactoryInterface;

    /**
     * Get the configured PSR-17 HTTP stream factory.
     *
     * Returns the factory used for creating PSR-7 stream objects for HTTP message
     * bodies. This factory is used to convert request data (such as JSON payloads)
     * into stream objects that can be attached to HTTP requests.
     *
     * If no factory was explicitly provided during construction, the RequestManager
     * will attempt to discover one automatically using PSR Discovery.
     *
     * @throws ClientThrowable If no stream factory is configured and auto-discovery fails
     *
     * @return StreamFactoryInterface The PSR-17 factory for creating HTTP message body streams
     */
    public function getHttpStreamFactory(): StreamFactoryInterface;

    /**
     * Convert an OpenFGA request into a PSR-7 HTTP request.
     *
     * Transforms high-level OpenFGA API requests into standardized PSR-7 HTTP requests
     * that can be executed by any PSR-18 compliant HTTP client. This process includes:
     * - Building the complete request URL from the base API URL and endpoint path
     * - Setting appropriate HTTP method based on the operation type
     * - Adding authentication headers using configured credentials
     * - Serializing request data to JSON and creating appropriate body streams
     * - Setting required headers (Content-Type, User-Agent, etc.)
     *
     * The conversion process ensures that all OpenFGA API requirements are met,
     * including proper content negotiation, authentication, and request formatting
     * according to the OpenFGA API specification.
     *
     * @param RequestInterface $request The high-level OpenFGA API request to convert
     *
     * @throws JsonException            If request data serialization fails
     * @throws InvalidArgumentException If request parameters are invalid
     * @throws ClientThrowable          If request conversion fails due to invalid parameters, serialization errors, or configuration issues
     *
     * @return HttpRequestInterface The PSR-7 HTTP request ready for execution
     */
    public function request(RequestInterface $request): HttpRequestInterface;

    /**
     * Send an HTTP request and return the response.
     *
     * Executes the provided PSR-7 HTTP request using the configured HTTP client
     * with comprehensive error handling and retry logic. This method handles:
     * - Network-level errors (connection failures, timeouts, DNS issues)
     * - HTTP-level errors (4xx and 5xx status codes)
     * - Automatic retry logic for transient failures
     * - Response validation and error context extraction
     *
     * The method provides detailed error information for debugging, including
     * request/response details, error codes, and suggested remediation steps
     * when requests fail. Successful responses are returned as-is for further
     * processing by the calling code.
     *
     * @param HttpRequestInterface $request The PSR-7 HTTP request to execute
     *
     * @throws ClientThrowable If the request fails due to client configuration, authentication, validation issues, network connectivity, server errors, or API-level failures
     *
     * @return ResponseInterface The HTTP response from the OpenFGA API
     */
    public function send(HttpRequestInterface $request): ResponseInterface;
}
