<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use InvalidArgumentException;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use ReflectionException;
use Throwable;

/**
 * General client error types for the OpenFGA SDK.
 *
 * Defines high-level error categories that can occur when using the SDK,
 * providing a way to classify different types of failures such as
 * authentication, configuration, network, serialization, and validation errors.
 * Each case provides a factory method to create the corresponding ClientException.
 *
 * These error categories help applications implement appropriate error handling
 * strategies, retry logic, and user messaging based on the type of failure:
 *
 * - Authentication: Token-related issues, expired credentials, OAuth failures
 * - Configuration: Missing PSR components, invalid SDK setup, configuration errors
 * - Network: HTTP communication failures, timeouts, API connectivity issues
 * - Serialization: JSON parsing errors, schema validation failures, data format issues
 * - Validation: Parameter validation failures, invalid API usage, constraint violations
 *
 * @see https://openfga.dev/docs/getting-started/setup-openfga OpenFGA Setup Guide
 * @see ClientException Concrete exception implementation
 */
enum ClientError: string
{
    use ExceptionLocationTrait;

    /**
     * Authentication-related failures when communicating with OpenFGA.
     *
     * Includes token expiration, invalid credentials, OAuth flow failures,
     * or other authentication mechanism issues.
     */
    case Authentication = 'authentication';

    /**
     * SDK configuration issues that prevent proper operation.
     *
     * Includes missing PSR HTTP components, invalid configuration parameters,
     * or improper SDK setup that prevents API communication.
     */
    case Configuration = 'configuration';

    /**
     * Network and HTTP communication failures with the OpenFGA API.
     *
     * Includes connectivity issues, timeouts, HTTP errors, or other
     * network-level problems that prevent successful API requests.
     */
    case Network = 'network';

    /**
     * Data serialization and deserialization failures.
     *
     * Includes JSON parsing errors, schema validation failures, data type
     * conversion issues, or other data format problems.
     */
    case Serialization = 'serialization';

    /**
     * Parameter validation and API usage errors.
     *
     * Includes invalid parameter values, constraint violations, improper
     * API usage, or other validation failures before sending requests.
     */
    case Validation = 'validation';

    /**
     * Create a new ClientException for this error type.
     *
     * Factory method that creates a ClientException instance with the current
     * error type and provided context information. This provides a convenient
     * way to generate typed exceptions with proper error categorization and
     * rich debugging context for OpenFGA API failures.
     *
     * The exception will automatically capture the correct file and line location
     * where this method was called (typically where `throw` occurs), ensuring
     * debuggers show the actual throw location rather than this factory method.
     *
     * @param RequestInterface|null  $request  The PSR-7 HTTP request that triggered the exception, if applicable
     * @param ResponseInterface|null $response The PSR-7 HTTP response received, if applicable
     * @param array<string, mixed>   $context  Additional context data including error details, parameter values, and debugging information
     * @param Throwable|null         $prev     The previous throwable used for exception chaining, if any
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If location capture fails
     *
     * @return ClientException The newly created ClientException instance with comprehensive error context
     */
    public function exception(
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
        array $context = [],
        ?Throwable $prev = null,
    ): ClientException {
        $exception = new ClientException($this, $request, $response, $context, $prev);
        self::captureThrowLocation($exception);

        return $exception;
    }
}
