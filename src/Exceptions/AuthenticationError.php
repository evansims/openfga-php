<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use InvalidArgumentException;
use OpenFGA\{Messages, Translation\Translator};
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use ReflectionException;
use Throwable;

/**
 * Authentication error types for the OpenFGA SDK.
 *
 * Defines specific authentication failure scenarios that can occur
 * when interacting with the OpenFGA API, such as expired or invalid
 * tokens. Each case provides a factory method to create the corresponding
 * AuthenticationException with appropriate context.
 *
 * Authentication errors typically occur during the OAuth 2.0 flow or when
 * using access tokens with OpenFGA API requests. These errors indicate that
 * the provided credentials are no longer valid or were never valid, requiring
 * token refresh or re-authentication.
 *
 * @see https://openfga.dev/docs/getting-started/setup-openfga OpenFGA Setup Guide
 * @see AuthenticationException Concrete exception implementation
 */
enum AuthenticationError: string
{
    use ExceptionLocationTrait;

    /**
     * Access token has expired and needs to be refreshed.
     *
     * Occurs when an access token's expiration time has passed,
     * requiring a new token to be obtained through the OAuth flow.
     */
    case TokenExpired = 'token_expired';

    /**
     * Access token is invalid or malformed.
     *
     * Occurs when the provided token is not recognized by the
     * authorization server or has an invalid format.
     */
    case TokenInvalid = 'token_invalid';

    /**
     * Create a new AuthenticationException for this error type.
     *
     * Factory method that creates an AuthenticationException instance with the
     * current error type and provided context information. This provides a
     * convenient way to generate typed exceptions with proper error categorization
     * and rich debugging context for OpenFGA authentication failures.
     *
     * The exception will automatically capture the correct file and line location
     * where this method was called (typically where `throw` occurs), ensuring
     * debuggers show the actual throw location rather than this factory method.
     *
     * @param RequestInterface|null  $request  The PSR-7 HTTP request that triggered the authentication failure, if applicable
     * @param ResponseInterface|null $response The PSR-7 HTTP response containing authentication error details, if applicable
     * @param array<string, mixed>   $context  Additional context data including token information, error details, and debugging information
     * @param Throwable|null         $prev     The previous throwable used for exception chaining, if any
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If location capture fails
     *
     * @return AuthenticationException The newly created AuthenticationException instance with comprehensive error context
     */
    public function exception(
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
        array $context = [],
        ?Throwable $prev = null,
    ): AuthenticationException {
        $exception = new AuthenticationException($this, $request, $response, $context, $prev);
        self::captureThrowLocation($exception);

        return $exception;
    }

    /**
     * Get a user-friendly error message for this authentication error.
     *
     * Provides appropriate messaging for different authentication failures
     * that can be displayed to end users or used in error logs.
     * Messages are localized using the translation system.
     *
     * @param  string|null $locale Optional locale override for message translation
     * @return string      A descriptive, localized error message
     */
    public function getUserMessage(?string $locale = null): string
    {
        return match ($this) {
            self::TokenExpired => Translator::trans(Messages::AUTH_USER_MESSAGE_TOKEN_EXPIRED, [], $locale),
            self::TokenInvalid => Translator::trans(Messages::AUTH_USER_MESSAGE_TOKEN_INVALID, [], $locale),
        };
    }

    /**
     * Check if this authentication error indicates the token should be refreshed.
     *
     * Useful for implementing automatic token refresh logic in OAuth flows.
     *
     * @return bool True if token refresh should be attempted, false otherwise
     */
    public function isTokenRefreshable(): bool
    {
        return match ($this) {
            self::TokenExpired => true,
            self::TokenInvalid => false,
        };
    }
}
