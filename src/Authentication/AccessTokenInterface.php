<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientThrowable};
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Represents an access token for OpenFGA API authentication.
 *
 * Access tokens are credentials used to authenticate requests to the OpenFGA API.
 * They are typically obtained through OAuth 2.0 flows (such as client credentials)
 * and have a limited lifespan defined by their expiration time.
 *
 * Access tokens provide secure, time-limited access to OpenFGA resources without
 * requiring the transmission of long-lived credentials with each request. This
 * interface defines the contract for managing these tokens, including:
 *
 * - Token value retrieval for Authorization headers
 * - Expiration checking to determine when token refresh is needed
 * - Scope validation for permission boundaries
 * - Token parsing from OAuth server responses
 *
 * @see https://openfga.dev/docs/getting-started/setup-openfga OpenFGA Setup Guide
 * @see https://tools.ietf.org/html/rfc6749#section-1.4 OAuth 2.0 Access Tokens
 */
interface AccessTokenInterface
{
    /**
     * Convert the access token to its string representation for use in Authorization headers.
     *
     * This method returns the raw token value that should be used in HTTP Authorization
     * headers when making authenticated requests to the OpenFGA API. The returned value
     * is typically used in the format "Bearer {token}."
     *
     * @return string The access token value suitable for Authorization headers
     */
    public function __toString(): string;

    /**
     * Create an access token instance from an OAuth server response.
     *
     * This factory method parses an HTTP response from an OAuth authorization server
     * and extracts the access token information. The response should contain a JSON
     * payload with the standard OAuth 2.0 token response fields including access_token,
     * expires_in, and optionally scope.
     *
     * If the access token is a JWT and expectedIssuer/expectedAudience are provided,
     * the JWT is validated to ensure the issuer and audience claims match the
     * expected values from the OAuth client configuration.
     *
     * @param ResponseInterface $response         The HTTP response from the OAuth token endpoint
     * @param string|null       $expectedIssuer   Optional expected issuer for JWT validation
     * @param string|null       $expectedAudience Optional expected audience for JWT validation
     *
     * @throws RuntimeException         If reading the response body fails
     * @throws JsonException            If the response contains invalid JSON
     * @throws InvalidArgumentException If response validation fails
     * @throws ClientThrowable          If the response format is invalid, contains unexpected data types, or is missing required fields
     *
     * @return self A new access token instance created from the response data
     */
    public static function fromResponse(ResponseInterface $response, ?string $expectedIssuer = null, ?string $expectedAudience = null): self;

    /**
     * Get the Unix timestamp when this access token expires.
     *
     * The expiration timestamp indicates when the token is no longer valid
     * for API requests. Applications should check this value before making requests
     * and refresh the token when necessary to avoid authentication failures.
     *
     * @return int Unix timestamp representing when the token expires
     */
    public function getExpires(): int;

    /**
     * Get the scope that defines the permissions granted by this access token.
     *
     * The scope represents the extent of access granted to the token bearer.
     * Different scopes may provide access to different OpenFGA operations or
     * resources. A null scope typically indicates full access or that scope
     * restrictions are not applicable for this token.
     *
     * @return string|null The token scope defining granted permissions, or null if no scope is specified
     */
    public function getScope(): ?string;

    /**
     * Get the raw access token value.
     *
     * This method returns the actual token string that was issued by the
     * authentication server. This is the same value returned by __toString()
     * but provided as an explicit getter method for clarity.
     *
     * @return string The raw access token value
     */
    public function getToken(): string;

    /**
     * Check whether this access token has expired and needs to be refreshed.
     *
     * This method compares the token's expiration time against the current time
     * to determine if the token is still valid. Expired tokens cannot be used
     * for API requests as they result in authentication failures.
     *
     * @return bool True if the token has expired and should be refreshed, false if still valid
     */
    public function isExpired(): bool;
}
