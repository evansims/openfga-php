<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use InvalidArgumentException;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use ReflectionException;
use Throwable;

/**
 * Configuration error types for the OpenFGA SDK.
 *
 * Defines specific configuration-related failures that can occur when
 * setting up or using the SDK, typically related to missing required
 * PSR HTTP components or invalid configuration parameters.
 * Each case provides a factory method to create the corresponding ConfigurationException.
 *
 * Configuration errors usually occur during SDK initialization when required
 * dependencies are missing or improperly configured. These errors indicate
 * that the SDK cannot operate properly due to missing PSR-7/PSR-17/PSR-18
 * components or invalid configuration settings.
 *
 * @see https://openfga.dev/docs/getting-started/setup-openfga OpenFGA Setup Guide
 * @see ConfigurationException Concrete exception implementation
 */
enum ConfigurationError: string
{
    use ExceptionLocationTrait;

    case HttpClientMissing = 'http_client_missing';

    case HttpRequestFactoryMissing = 'http_request_factory_missing';

    case HttpResponseFactoryMissing = 'http_response_factory_missing';

    case HttpStreamFactoryMissing = 'http_stream_factory_missing';

    case InvalidLanguage = 'invalid_language';

    case InvalidRetryCount = 'invalid_retry_count';

    case InvalidUrl = 'invalid_url';

    /**
     * Create a new ConfigurationException for this error type.
     *
     * Factory method that creates a ConfigurationException instance with the
     * current error type and provided context information. This provides a
     * convenient way to generate typed exceptions with proper error categorization
     * and rich debugging context for OpenFGA configuration failures.
     *
     * The exception will automatically capture the correct file and line location
     * where this method was called (typically where `throw` occurs), ensuring
     * debuggers show the actual throw location rather than this factory method.
     *
     * @param RequestInterface|null  $request  The PSR-7 HTTP request being processed when configuration error occurred, if applicable
     * @param ResponseInterface|null $response The PSR-7 HTTP response received, if applicable
     * @param array<string, mixed>   $context  Additional context data including configuration details, missing components, and debugging information
     * @param Throwable|null         $prev     The previous throwable used for exception chaining, if any
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If location capture fails
     *
     * @return ConfigurationException The newly created ConfigurationException instance with comprehensive error context
     */
    public function exception(
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
        array $context = [],
        ?Throwable $prev = null,
    ): ConfigurationException {
        $exception = new ConfigurationException($this, $request, $response, $context, $prev);
        self::captureThrowLocation($exception);

        return $exception;
    }

    /**
     * Get the recommended PSR interface for this configuration error.
     *
     * Provides the specific PSR interface name that should be implemented
     * to resolve this configuration error, useful for error messages and documentation.
     *
     * @return string The PSR interface name
     */
    public function getRequiredPsrInterface(): string
    {
        return match ($this) {
            self::HttpClientMissing => 'Psr\\Http\\Client\\ClientInterface',
            self::HttpRequestFactoryMissing => 'Psr\\Http\\Message\\RequestFactoryInterface',
            self::HttpResponseFactoryMissing => 'Psr\\Http\\Message\\ResponseFactoryInterface',
            self::HttpStreamFactoryMissing => 'Psr\\Http\\Message\\StreamFactoryInterface',
            self::InvalidUrl,
            self::InvalidLanguage,
            self::InvalidRetryCount => '',
        };
    }

    /**
     * Check if this configuration error is related to missing PSR HTTP components.
     *
     * Useful for providing specific error handling and setup guidance
     * when PSR HTTP dependencies are not properly configured.
     *
     * @return bool True if the error is related to missing HTTP components, false otherwise
     */
    public function isHttpComponentMissing(): bool
    {
        return match ($this) {
            self::HttpClientMissing,
            self::HttpRequestFactoryMissing,
            self::HttpResponseFactoryMissing,
            self::HttpStreamFactoryMissing => true,
            self::InvalidUrl,
            self::InvalidLanguage,
            self::InvalidRetryCount => false,
        };
    }
}
