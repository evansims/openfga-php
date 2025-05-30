<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use InvalidArgumentException;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use ReflectionException;
use Throwable;

/**
 * Network error types for the OpenFGA SDK.
 *
 * Defines specific network and HTTP-related failures that can occur when
 * communicating with the OpenFGA API, including HTTP status code errors,
 * request failures, and timeouts. Each case provides a factory method
 * to create the corresponding NetworkException.
 *
 * Network errors represent failures in HTTP communication with the OpenFGA
 * service, ranging from client-side request errors to server-side failures.
 * These errors often contain valuable debugging information in the HTTP
 * response, including error messages and suggested remediation steps.
 *
 * @see https://openfga.dev/docs/getting-started/setup-openfga OpenFGA Setup Guide
 * @see NetworkException Concrete exception implementation
 */
enum NetworkError: string
{
    use ExceptionLocationTrait;

    /**
     * HTTP 409 Conflict - Resource conflicts with current state.
     *
     * Indicates that the request conflicts with the current state of the
     * target resource, often due to concurrent modifications.
     */
    case Conflict = 'http_409';

    /**
     * HTTP 403 Forbidden - Access denied to the requested resource.
     *
     * The server understood the request but refuses to authorize it,
     * typically due to insufficient permissions.
     */
    case Forbidden = 'http_403';

    /**
     * HTTP 400 Bad Request - Invalid request format or parameters.
     *
     * The server cannot process the request due to malformed syntax,
     * invalid parameters, or missing required data.
     */
    case Invalid = 'http_400';

    /**
     * General request failure not related to HTTP status codes.
     *
     * Represents network-level failures such as DNS resolution errors,
     * connection timeouts, or other transport-level issues.
     */
    case Request = 'request_failed';

    /**
     * HTTP 500 Internal Server Error - Server-side processing failure.
     *
     * Indicates that the server encountered an unexpected condition
     * that prevented it from fulfilling the request.
     */
    case Server = 'http_500';

    /**
     * HTTP 422 Unprocessable Entity - Request timeout or processing limit exceeded.
     *
     * The server understands the request but cannot process it due to
     * timeout constraints or processing limits being exceeded.
     */
    case Timeout = 'http_422';

    /**
     * HTTP 401 Unauthorized - Authentication required or failed.
     *
     * The request requires valid authentication credentials that
     * were not provided or are no longer valid.
     */
    case Unauthenticated = 'http_401';

    /**
     * HTTP 404 Not Found - Requested endpoint or resource does not exist.
     *
     * The server cannot find the requested resource, which may indicate
     * an invalid API endpoint or a resource that has been deleted.
     */
    case UndefinedEndpoint = 'http_404';

    /**
     * Unexpected network error that doesn't fit other categories.
     *
     * Represents unusual network conditions or errors that are not
     * covered by the standard HTTP status code categories.
     */
    case Unexpected = 'unexpected';

    /**
     * Create a new NetworkException for this error type.
     *
     * Factory method that creates a NetworkException instance with the current
     * error type and provided context information. This provides a convenient
     * way to generate typed exceptions with proper error categorization and
     * rich debugging context for OpenFGA network failures.
     *
     * The exception will automatically capture the correct file and line location
     * where this method was called (typically where `throw` occurs), ensuring
     * debuggers show the actual throw location rather than this factory method.
     *
     * @param RequestInterface|null  $request  The PSR-7 HTTP request that triggered the network failure, if applicable
     * @param ResponseInterface|null $response The PSR-7 HTTP response containing error details, if applicable
     * @param array<string, mixed>   $context  Additional context data including network details, error information, and debugging data
     * @param Throwable|null         $prev     The previous throwable used for exception chaining, if any
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If location capture fails
     *
     * @return NetworkException The newly created NetworkException instance with comprehensive error context
     */
    public function exception(
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
        array $context = [],
        ?Throwable $prev = null,
    ): NetworkException {
        $exception = new NetworkException($this, $request, $response, $context, $prev);
        self::captureThrowLocation($exception);

        return $exception;
    }
}
