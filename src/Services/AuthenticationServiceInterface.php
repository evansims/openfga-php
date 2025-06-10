<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Authentication\AuthenticationInterface;
use OpenFGA\Exceptions\{ClientThrowable, NetworkException};
use OpenFGA\Network\RequestContext;
use Psr\Http\Message\{ResponseInterface as HttpResponseInterface, StreamFactoryInterface};
use ReflectionException;
use Throwable;

/**
 * Service interface for managing authentication in OpenFGA operations.
 *
 * This service abstracts authentication concerns from the Client class, handling
 * the complexities of token management, authentication flows, and authorization
 * header generation. It provides a clean interface for different authentication
 * strategies while encapsulating the details of token refresh and error handling.
 *
 * ## Core Functionality
 *
 * The service manages the complete authentication lifecycle:
 * - Authorization header generation with automatic token refresh
 * - Authentication request handling with proper error management
 * - Support for multiple authentication strategies (OAuth2, pre-shared keys)
 * - Integration with telemetry for authentication monitoring
 *
 * ## Usage Example
 *
 * ```php
 * $authService = new AuthenticationService($authentication, $telemetryService);
 *
 * // Get authorization header (with automatic refresh if needed)
 * $authHeader = $authService->getAuthorizationHeader($streamFactory);
 *
 * // Handle authentication requests
 * $response = $authService->sendAuthenticationRequest($context, $requestManager);
 * ```
 *
 * @see AuthenticationInterface Authentication strategy interface
 * @see TelemetryServiceInterface Telemetry integration
 */
interface AuthenticationServiceInterface
{
    /**
     * Get the authorization header for API requests.
     *
     * Retrieves the current authorization header, automatically handling token
     * refresh if the current token is expired or missing. This method encapsulates
     * the complexity of different authentication flows and provides a simple
     * interface for obtaining valid authorization credentials.
     *
     * @param StreamFactoryInterface                               $streamFactory Stream factory for building authentication requests
     * @param callable(RequestContext): HttpResponseInterface|null $requestSender Optional callback to send authentication requests
     *
     * @throws ClientThrowable     If authentication configuration is invalid
     * @throws NetworkException    If authentication request fails
     * @throws ReflectionException If exception creation fails
     * @throws Throwable           If an unexpected error occurs during authentication
     *
     * @return string|null The authorization header value, or null if no authentication configured
     */
    public function getAuthorizationHeader(
        StreamFactoryInterface $streamFactory,
        ?callable $requestSender = null,
    ): ?string;

    /**
     * Send an authentication request using a pre-built RequestContext.
     *
     * Handles the complete lifecycle of authentication requests, including request
     * building, sending, response handling, and telemetry tracking. This method
     * provides a centralized point for all authentication-related HTTP operations.
     *
     * @param RequestContext                                  $context       The authentication request context
     * @param callable(RequestContext): HttpResponseInterface $requestSender Callback that takes RequestContext and returns HttpResponseInterface
     *
     * @throws ClientThrowable     If request building or sending fails
     * @throws NetworkException    If the authentication request fails
     * @throws ReflectionException If exception creation fails
     * @throws Throwable           If an unexpected error occurs
     *
     * @return HttpResponseInterface The authentication response
     */
    public function sendAuthenticationRequest(RequestContext $context, callable $requestSender): HttpResponseInterface;
}
