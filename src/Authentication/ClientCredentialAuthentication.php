<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientThrowable};
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\{ResponseInterface, StreamFactoryInterface};
use ReflectionException;
use RuntimeException;

use function json_encode;
use function rtrim;
use function trim;

/**
 * OAuth 2.0 Client Credentials authentication strategy for OpenFGA client.
 *
 * This authentication strategy implements the OAuth 2.0 Client Credentials flow
 * for authenticating with the OpenFGA API. It automatically handles token
 * acquisition, caching, and refresh when tokens expire.
 *
 * The strategy requires client credentials (client ID and secret) along with
 * the OAuth issuer and audience parameters. It automatically requests
 * new tokens when the current token expires.
 *
 * @see https://openfga.dev/docs/getting-started/setup-sdk-client#configuring-authentication Authentication configuration guide
 * @see https://datatracker.ietf.org/doc/html/rfc6749#section-4.4 OAuth 2.0 Client Credentials Grant
 */
final class ClientCredentialAuthentication implements AuthenticationInterface
{
    /**
     * Flag to prevent infinite loop during authentication.
     */
    private bool $isAuthenticating = false;

    /**
     * The current access token, if any.
     */
    private ?AccessTokenInterface $token = null;

    /**
     * Create a new client credentials authentication strategy.
     *
     * Initializes an OAuth 2.0 Client Credentials flow authentication strategy
     * with the required parameters for token acquisition. The strategy
     * automatically handles token requests and refreshes as needed.
     *
     * @param string $clientId     The OAuth client ID obtained from your authorization server
     * @param string $clientSecret The OAuth client secret obtained from your authorization server
     * @param string $audience     The OAuth audience parameter (typically the OpenFGA API address)
     * @param string $issuer       The OAuth issuer address where token requests are sent
     */
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $audience,
        private readonly string $issuer,
    ) {
    }

    /**
     * Clear the current access token and force re-authentication.
     *
     * Removes the stored access token, forcing the authentication strategy
     * to request a new token on the next API call. This is useful for
     * handling authentication errors or forcing token refresh.
     */
    public function clearToken(): void
    {
        $this->token = null;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException If JSON encoding of authentication parameters fails
     */
    #[Override]
    public function getAuthenticationRequest(StreamFactoryInterface $streamFactory): ?RequestContext
    {
        if ($this->shouldSkipAuthentication()) {
            return null;
        }

        $credentials = $this->validateAndPrepareCredentials();

        if (null === $credentials) {
            return null;
        }

        $this->isAuthenticating = true;

        return $this->buildAuthenticationRequest($streamFactory, $credentials);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getAuthorizationHeader(): ?string
    {
        if ($this->token instanceof AccessTokenInterface && ! $this->token->isExpired()) {
            return (string) $this->token;
        }

        return null;
    }

    /**
     * Get the current access token if available.
     *
     * Returns the stored access token, which may be null if no authentication
     * has been performed yet or if the token has been explicitly cleared.
     * The returned token may be expired; use the token's isExpired() method
     * to check validity.
     *
     * @return AccessTokenInterface|null The current access token, or null if not authenticated
     */
    public function getToken(): ?AccessTokenInterface
    {
        return $this->token;
    }

    /**
     * Handle the authentication response and update the stored token.
     *
     * Processes the OAuth token response and creates a new access token
     * from the response data. This method is automatically called by the
     * Client after a successful authentication request.
     *
     * For JWT tokens, this method validates the issuer and audience claims
     * against the OAuth configuration to ensure the token was issued by the
     * expected authorization server and is intended for the correct audience.
     *
     * @param ResponseInterface $response The authentication response from the OAuth server
     *
     * @throws ClientThrowable          If the response format is invalid, contains unexpected data types, or is missing required fields
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws JsonException            If JSON parsing of the response fails
     * @throws ReflectionException      If location capture fails during exception creation
     * @throws RuntimeException         If reading the response body fails
     *
     * @phpstan-ignore-next-line throws.unusedType
     */
    #[Override]
    public function handleAuthenticationResponse(ResponseInterface $response): void
    {
        try {
            $this->token = AccessToken::fromResponse($response, $this->issuer, $this->audience);
        } finally {
            $this->isAuthenticating = false;
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function requiresAuthentication(): bool
    {
        return true;
    }

    /**
     * Build the OAuth authentication request.
     *
     * @param array{clientId: string, clientSecret: string, issuer: string, audience: string} $credentials
     * @param StreamFactoryInterface                                                          $streamFactory
     */
    private function buildAuthenticationRequest(StreamFactoryInterface $streamFactory, array $credentials): RequestContext
    {
        return new RequestContext(
            method: RequestMethod::POST,
            url: rtrim($credentials['issuer'], '/') . '/oauth/token',
            body: $streamFactory->createStream(json_encode([
                'grant_type' => 'client_credentials',
                'client_id' => $credentials['clientId'],
                'client_secret' => $credentials['clientSecret'],
                'audience' => $credentials['audience'],
            ], JSON_THROW_ON_ERROR)),
            headers: [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            useApiUrl: false,
        );
    }

    /**
     * Check if authentication should be skipped.
     */
    private function shouldSkipAuthentication(): bool
    {
        if ($this->token instanceof AccessTokenInterface && ! $this->token->isExpired()) {
            return true;
        }

        return $this->isAuthenticating;
    }

    /**
     * Trim string or return null if empty.
     *
     * @param string $value
     */
    private function trimOrNull(string $value): ?string
    {
        $trimmed = trim($value);

        return '' !== $trimmed ? $trimmed : null;
    }

    /**
     * Validate and prepare authentication credentials.
     *
     * @return array{clientId: string, clientSecret: string, issuer: string, audience: string}|null
     */
    private function validateAndPrepareCredentials(): ?array
    {
        $clientId = $this->trimOrNull($this->clientId);
        $clientSecret = $this->trimOrNull($this->clientSecret);
        $issuer = $this->trimOrNull($this->issuer);
        $audience = $this->trimOrNull($this->audience);

        if (null === $clientId || null === $clientSecret || null === $issuer || null === $audience) {
            return null;
        }

        return [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'issuer' => $issuer,
            'audience' => $audience,
        ];
    }
}
