<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

/**
 * Base interface for all OpenFGA SDK exceptions.
 *
 * Extends the standard PHP Throwable interface with additional methods
 * to provide detailed context about SDK-specific errors including HTTP
 * request/response information and categorized error types.
 *
 * This interface ensures consistent error handling across all exception
 * types in the OpenFGA SDK, enabling developers to access rich context
 * information for debugging and error reporting. All OpenFGA exceptions
 * implement this interface to provide a unified error handling experience.
 *
 * OpenFGA exceptions are categorized into specific types:
 * - Authentication errors (token expired, invalid credentials)
 * - Configuration errors (missing PSR components, invalid setup)
 * - Network errors (HTTP failures, timeouts, API unavailability)
 * - Serialization errors (JSON parsing, schema validation failures)
 * - Client errors (general validation and usage errors)
 *
 * @see https://openfga.dev/docs/getting-started/setup-openfga OpenFGA Setup Guide
 * @see Throwable Base PHP throwable interface
 */
interface ClientThrowable extends Throwable
{
    /**
     * Get additional context information about the exception.
     *
     * Provides access to contextual data that was available when the exception
     * occurred, such as parameter values, configuration details, API response
     * data, or other relevant debugging information. This context is essential
     * for understanding the circumstances that led to the error and can be
     * used for logging, debugging, and error reporting.
     *
     * @return array<string, mixed> Associative array of context data including parameter values, error details, and debugging information
     */
    public function context(): array;

    /**
     * Get the specific error category for this exception.
     *
     * Returns the error classification that indicates the general category
     * of the problem (authentication, configuration, network, etc.), allowing
     * for categorized error handling and reporting. This categorization helps
     * applications implement appropriate retry logic, user messaging, and
     * error recovery strategies based on the type of failure.
     *
     * @return AuthenticationError|ClientError|ConfigurationError|NetworkError|SerializationError The error category enum indicating the type of failure
     */
    public function kind(): ClientError | AuthenticationError | ConfigurationError | NetworkError | SerializationError;

    /**
     * Get the previous exception that caused this one.
     *
     * Provides access to the exception chain for cases where this exception
     * was triggered by another underlying exception. This maintains the full
     * context of error propagation and is essential for root cause analysis
     * when exceptions are wrapped or transformed during processing.
     *
     * @return Throwable|null The previous exception in the chain, or null if this is the root exception
     */
    public function previous(): ?Throwable;

    /**
     * Get the HTTP request associated with this exception.
     *
     * Returns the PSR-7 HTTP request that was being processed when this exception
     * occurred. This is particularly useful for debugging API call failures,
     * allowing developers to inspect the request URL, headers, body, and method
     * that led to the error condition.
     *
     * @return RequestInterface|null The PSR-7 HTTP request that triggered the exception, or null if not applicable
     */
    public function request(): ?RequestInterface;

    /**
     * Get the HTTP response associated with this exception.
     *
     * Returns the PSR-7 HTTP response that was received when this exception occurred,
     * providing access to status codes, headers, and response body for debugging.
     * This is especially valuable for understanding API-level failures and can
     * contain detailed error messages from the OpenFGA service.
     *
     * @return ResponseInterface|null The PSR-7 HTTP response received from the API, or null if no response was received
     */
    public function response(): ?ResponseInterface;
}
