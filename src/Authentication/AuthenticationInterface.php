<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

use OpenFGA\Network\RequestContext;
use Psr\Http\Message\{ResponseInterface, StreamFactoryInterface};

/**
 * Interface for OpenFGA authentication strategies.
 *
 * This interface defines the contract for different authentication methods
 * that can be used with the OpenFGA client. Authentication strategies handle
 * the generation and management of authorization headers for API requests.
 *
 * @see https://openfga.dev/docs/getting-started/setup-sdk-client#configuring-authentication Authentication configuration guide
 */
interface AuthenticationInterface
{
    /**
     * Get an authentication request context if this strategy requires token acquisition.
     *
     * Returns a RequestContext for making an authentication request (such as OAuth token
     * request) if the strategy needs to obtain tokens dynamically. Returns null for
     * strategies that don't require authentication requests (like pre-shared tokens).
     *
     * @param  StreamFactoryInterface $streamFactory Factory for creating request body streams
     * @return RequestContext|null    The authentication request context, or null if not needed
     */
    public function getAuthenticationRequest(StreamFactoryInterface $streamFactory): ?RequestContext;

    /**
     * Get the authorization header value for API requests.
     *
     * Returns the authorization header value to be included in HTTP requests
     * to the OpenFGA API. The format and content depend on the specific
     * authentication strategy implementation.
     *
     * For strategies that need to perform authentication requests (like OAuth),
     * this method may trigger an authentication flow using getAuthenticationRequest().
     *
     * @return string|null The authorization header value, or null if no authentication is needed
     */
    public function getAuthorizationHeader(): ?string;

    /**
     * Handle the authentication response and update internal state.
     *
     * This method is called by the Client after successfully sending an
     * authentication request to update stored tokens or other authentication state.
     * Implementations that don't require response handling can provide an empty implementation.
     *
     * @param ResponseInterface $response The authentication response
     */
    public function handleAuthenticationResponse(ResponseInterface $response): void;

    /**
     * Check if authentication is required for this strategy.
     *
     * @return bool True if this strategy provides authentication, false for no-auth strategies
     */
    public function requiresAuthentication(): bool;
}
