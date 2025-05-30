<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

use OpenFGA\Network\RequestContext;
use Override;
use Psr\Http\Message\{ResponseInterface, StreamFactoryInterface};

use function is_string;

/**
 * Token-based authentication strategy for OpenFGA client.
 *
 * This authentication strategy uses a pre-shared token (such as a Bearer token
 * or API key) for authentication with the OpenFGA API. The token is provided
 * during construction and used as-is for all requests.
 *
 * This strategy is suitable for scenarios where you have a long-lived token
 * or when implementing custom token refresh logic externally. The strategy
 * accepts either a string token or an AccessTokenInterface instance for
 * maximum flexibility.
 *
 * @see https://openfga.dev/docs/getting-started/setup-sdk-client#configuring-authentication Authentication configuration guide
 */
final readonly class TokenAuthentication implements AuthenticationInterface
{
    /**
     * Create a new token authentication strategy.
     *
     * Accepts either a string token (for simple bearer tokens or API keys)
     * or an AccessTokenInterface instance (for tokens with expiration tracking).
     * String tokens are treated as never-expiring credentials.
     *
     * @param AccessTokenInterface|string $token The authentication token to use for API requests
     */
    public function __construct(
        private AccessTokenInterface | string $token,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getAuthenticationRequest(StreamFactoryInterface $streamFactory): ?RequestContext
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getAuthorizationHeader(): ?string
    {
        if (is_string($this->token)) {
            return $this->token;
        }

        if ($this->token->isExpired()) {
            return null;
        }

        return (string) $this->token;
    }

    /**
     * Get the current authentication token.
     *
     * Returns the token that was provided during construction. This can be
     * either a string token or an AccessTokenInterface instance depending
     * on what was originally provided to the constructor.
     *
     * @return AccessTokenInterface|string The authentication token used by this strategy
     */
    public function getToken(): AccessTokenInterface | string
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function handleAuthenticationResponse(ResponseInterface $response): void
    {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function requiresAuthentication(): bool
    {
        return true;
    }
}
