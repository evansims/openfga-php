<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Authentication\{AuthenticationInterface, ClientCredentialAuthentication};
use OpenFGA\Exceptions\{NetworkError, NetworkException};
use OpenFGA\Network\RequestContext;
use Override;
use Psr\Http\Message\{ResponseInterface as HttpResponseInterface, StreamFactoryInterface};
use Throwable;

/**
 * Service implementation for managing authentication in OpenFGA operations.
 *
 * This service encapsulates all authentication-related logic, providing a clean
 * abstraction over the underlying authentication strategies. It handles token
 * management, authentication request flows, and integrates with telemetry for
 * monitoring authentication performance and failures.
 *
 * The service supports multiple authentication strategies through the
 * AuthenticationInterface, automatically handling token refresh and error
 * recovery patterns. It provides consistent error handling and telemetry
 * integration across all authentication operations.
 *
 * @see AuthenticationServiceInterface Service interface
 * @see AuthenticationInterface Authentication strategy interface
 */
final readonly class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * Create a new authentication service instance.
     *
     * @param AuthenticationInterface|null   $authentication   The authentication strategy to use
     * @param TelemetryServiceInterface|null $telemetryService Optional telemetry service for monitoring
     */
    public function __construct(
        private ?AuthenticationInterface $authentication = null,
        private ?TelemetryServiceInterface $telemetryService = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getAuthorizationHeader(
        StreamFactoryInterface $streamFactory,
        ?callable $requestSender = null,
    ): ?string {
        if (! $this->authentication instanceof AuthenticationInterface) {
            return null;
        }

        $authentication = $this->authentication;

        // Try to get cached header first
        $header = $authentication->getAuthorizationHeader();

        if (null !== $header) {
            return $header;
        }

        // Build authentication request
        $authRequest = $authentication->getAuthenticationRequest($streamFactory);

        if (! $authRequest instanceof RequestContext) {
            return null;
        }

        // If no request sender provided, we can't proceed with authentication
        if (null === $requestSender) {
            return null;
        }

        // Record authentication attempt start
        $startTime = microtime(true);

        try {
            // Send authentication request directly (without double telemetry)
            $response = $requestSender($authRequest);

            // Ensure we have a valid response
            if (! $response instanceof HttpResponseInterface) {
                throw new NetworkException(NetworkError::Request, null, null, ['message' => 'Invalid response type received from request sender'], );
            }

            // Handle authentication response for OAuth2 flows
            if ($authentication instanceof ClientCredentialAuthentication) {
                $authentication->handleAuthenticationResponse($response);

                // Record successful authentication
                $duration = microtime(true) - $startTime;
                $this->telemetryService?->recordAuthenticationEvent(
                    'token_request',
                    true,
                    $duration,
                    ['strategy' => 'client_credentials'],
                );

                return $authentication->getAuthorizationHeader();
            }

            // Record successful authentication for other strategies
            $duration = microtime(true) - $startTime;
            $this->telemetryService?->recordAuthenticationEvent(
                'auth_request',
                true,
                $duration,
                ['strategy' => $authentication::class],
            );

            return $authentication->getAuthorizationHeader();
        } catch (Throwable $throwable) {
            // Record failed authentication
            $duration = microtime(true) - $startTime;
            $this->telemetryService?->recordAuthenticationEvent(
                'auth_failure',
                false,
                $duration,
                [
                    'strategy' => $authentication::class,
                    'error' => $throwable->getMessage(),
                ],
            );

            // Silently fail authentication attempts - this maintains backward compatibility
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function sendAuthenticationRequest(RequestContext $context, callable $requestSender): HttpResponseInterface
    {
        $startTime = microtime(true);

        try {
            // Send the authentication request using the provided callback
            $response = $requestSender($context);

            // Record successful request
            $duration = microtime(true) - $startTime;
            $this->telemetryService?->recordAuthenticationEvent(
                'auth_http_request',
                true,
                $duration,
                [
                    'method' => $context->getMethod()->value,
                    'url' => $context->getUrl(),
                ],
            );

            return $response;
        } catch (Throwable $throwable) {
            // Record failed request
            $duration = microtime(true) - $startTime;
            $this->telemetryService?->recordAuthenticationEvent(
                'auth_http_request',
                false,
                $duration,
                [
                    'method' => $context->getMethod()->value,
                    'url' => $context->getUrl(),
                    'error' => $throwable->getMessage(),
                ],
            );

            throw $throwable;
        }
    }
}
