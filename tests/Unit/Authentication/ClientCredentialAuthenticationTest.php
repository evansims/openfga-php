<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Authentication;

use const JSON_THROW_ON_ERROR;

use OpenFGA\Authentication\{AccessTokenInterface, AuthenticationInterface, ClientCredentialAuthentication};
use OpenFGA\Exceptions\SerializationException;
use OpenFGA\Messages;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Psr\Http\Message\{ResponseInterface, StreamFactoryInterface, StreamInterface};

// Helper functions for JWT testing in ClientCredentialAuthentication
function createMockResponseForClientCreds(array $data): ResponseInterface
{
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('getContents')
        ->willReturn(json_encode($data));

    $response = test()->createMock(ResponseInterface::class);
    $response->method('getBody')
        ->willReturn($stream);

    return $response;
}

function base64url_encodeForClientCreds(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function createValidJwtForClientCreds(array $header, array $payload): string
{
    $headerEncoded = base64url_encodeForClientCreds(json_encode($header));
    $payloadEncoded = base64url_encodeForClientCreds(json_encode($payload));

    // For testing purposes, we don't need a valid signature
    $signature = base64url_encodeForClientCreds('fake_signature');

    return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
}

describe('ClientCredentialAuthentication', function (): void {
    test('implements AuthenticationInterface', function (): void {
        $auth = new ClientCredentialAuthentication(
            'client_id',
            'client_secret',
            'audience',
            'issuer',
        );

        expect($auth)->toBeInstanceOf(AuthenticationInterface::class);
    });

    test('constructs with required parameters', function (): void {
        $clientId = 'test_client_id';
        $clientSecret = 'test_client_secret';
        $audience = 'https://api.example.com';
        $issuer = 'https://auth.example.com';

        $auth = new ClientCredentialAuthentication(
            $clientId,
            $clientSecret,
            $audience,
            $issuer,
        );

        expect($auth)->toBeInstanceOf(ClientCredentialAuthentication::class);
    });

    test('getRequest returns correct RequestContext', function (): void {
        $clientId = 'test_client_id';
        $clientSecret = 'test_client_secret';
        $audience = 'https://api.example.com';
        $issuer = 'https://auth.example.com';

        $auth = new ClientCredentialAuthentication(
            $clientId,
            $clientSecret,
            $audience,
            $issuer,
        );

        $expectedBody = json_encode([
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'audience' => $audience,
        ], JSON_THROW_ON_ERROR);

        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with($expectedBody)
            ->willReturn($stream);

        $requestContext = $auth->getAuthenticationRequest($streamFactory);

        expect($requestContext)->toBeInstanceOf(RequestContext::class);
        expect($requestContext->getMethod())->toBe(RequestMethod::POST);
        expect($requestContext->getUrl())->toBe('https://auth.example.com/oauth/token');
        expect($requestContext->useApiUrl())->toBe(false);
        expect($requestContext->getBody())->toBe($stream);
        expect($requestContext->getHeaders())->toBe([
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
    });

    test('special characters in credentials', function (): void {
        $clientId = 'client!@#$%^&*()_+';
        $clientSecret = 'secret{}"\'\\';
        $audience = 'https://api.example.com/v1';
        $issuer = 'https://auth.example.com/oauth';

        $auth = new ClientCredentialAuthentication(
            $clientId,
            $clientSecret,
            $audience,
            $issuer,
        );

        $expectedBody = json_encode([
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'audience' => $audience,
        ], JSON_THROW_ON_ERROR);

        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with($expectedBody)
            ->willReturn($stream);

        $requestContext = $auth->getAuthenticationRequest($streamFactory);

        expect($requestContext)->toBeInstanceOf(RequestContext::class);
    });

    test('empty strings return null', function (): void {
        $auth = new ClientCredentialAuthentication('', '', '', '');

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->never())
            ->method('createStream');

        $requestContext = $auth->getAuthenticationRequest($streamFactory);

        expect($requestContext)->toBeNull();
    });

    test('very long credentials', function (): void {
        $longString = str_repeat('a', 1000);
        $auth = new ClientCredentialAuthentication(
            $longString,
            $longString,
            $longString,
            $longString,
        );

        $expectedBody = json_encode([
            'grant_type' => 'client_credentials',
            'client_id' => $longString,
            'client_secret' => $longString,
            'audience' => $longString,
        ], JSON_THROW_ON_ERROR);

        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with($expectedBody)
            ->willReturn($stream);

        $requestContext = $auth->getAuthenticationRequest($streamFactory);

        expect($requestContext)->toBeInstanceOf(RequestContext::class);
    });

    test('returns null on second call while authenticating', function (): void {
        $auth = new ClientCredentialAuthentication(
            'client_id',
            'client_secret',
            'audience',
            'issuer',
        );

        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->willReturn($stream);

        $request1 = $auth->getAuthenticationRequest($streamFactory);
        $request2 = $auth->getAuthenticationRequest($streamFactory);

        expect($request1)->toBeInstanceOf(RequestContext::class);
        expect($request2)->toBeNull(); // Second call returns null while authenticating
    });

    describe('JWT authentication integration', function (): void {
        test('handleAuthenticationResponse stores valid JWT token with issuer and audience validation', function (): void {
            $issuer = 'https://auth.example.com';
            $audience = 'https://api.example.com';

            $auth = new ClientCredentialAuthentication(
                'client_id',
                'client_secret',
                $audience,
                $issuer,
            );

            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'iss' => $issuer,
                'aud' => $audience,
                'exp' => $currentTime + 3600,
                'iat' => $currentTime,
            ];

            $jwtToken = createValidJwtForClientCreds($header, $payload);
            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponseForClientCreds($responseData);

            $auth->handleAuthenticationResponse($response);

            $token = $auth->getToken();
            expect($token)->toBeInstanceOf(AccessTokenInterface::class);
            expect($token->getToken())->toBe($jwtToken);
            expect($token->isExpired())->toBeFalse();
        });

        test('handleAuthenticationResponse validates JWT issuer matches client configuration', function (): void {
            $expectedIssuer = 'https://auth.example.com';
            $expectedAudience = 'https://api.example.com';

            $auth = new ClientCredentialAuthentication(
                'client_id',
                'client_secret',
                $expectedAudience,
                $expectedIssuer,
            );

            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'iss' => 'https://wrong-issuer.com', // Wrong issuer
                'aud' => $expectedAudience,
                'exp' => $currentTime + 3600,
                'iat' => $currentTime,
            ];

            $jwtToken = createValidJwtForClientCreds($header, $payload);
            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponseForClientCreds($responseData);

            $auth->handleAuthenticationResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_ISSUER));

        test('handleAuthenticationResponse validates JWT audience matches client configuration', function (): void {
            $expectedIssuer = 'https://auth.example.com';
            $expectedAudience = 'https://api.example.com';

            $auth = new ClientCredentialAuthentication(
                'client_id',
                'client_secret',
                $expectedAudience,
                $expectedIssuer,
            );

            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'iss' => $expectedIssuer,
                'aud' => 'https://wrong-audience.com', // Wrong audience
                'exp' => $currentTime + 3600,
                'iat' => $currentTime,
            ];

            $jwtToken = createValidJwtForClientCreds($header, $payload);
            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponseForClientCreds($responseData);

            $auth->handleAuthenticationResponse($response);
        })->throws(SerializationException::class, trans(Messages::JWT_INVALID_AUDIENCE));

        test('handleAuthenticationResponse accepts non-JWT tokens without validation', function (): void {
            $auth = new ClientCredentialAuthentication(
                'client_id',
                'client_secret',
                'https://api.example.com',
                'https://auth.example.com',
            );

            $responseData = [
                'access_token' => 'simple_bearer_token',
                'expires_in' => 3600,
            ];

            $response = createMockResponseForClientCreds($responseData);

            $auth->handleAuthenticationResponse($response);

            $token = $auth->getToken();
            expect($token)->toBeInstanceOf(AccessTokenInterface::class);
            expect($token->getToken())->toBe('simple_bearer_token');
        });

        test('getAuthorizationHeader returns Bearer token when available and not expired', function (): void {
            $auth = new ClientCredentialAuthentication(
                'client_id',
                'client_secret',
                'https://api.example.com',
                'https://auth.example.com',
            );

            // Simulate authentication response
            $responseData = [
                'access_token' => 'test_token_123',
                'expires_in' => 3600,
            ];
            $response = createMockResponseForClientCreds($responseData);
            $auth->handleAuthenticationResponse($response);

            $authHeader = $auth->getAuthorizationHeader();
            expect($authHeader)->toBe('test_token_123');
        });

        test('getAuthorizationHeader returns null when token is expired', function (): void {
            $auth = new ClientCredentialAuthentication(
                'client_id',
                'client_secret',
                'https://api.example.com',
                'https://auth.example.com',
            );

            // Simulate authentication response with expired token
            $responseData = [
                'access_token' => 'expired_token',
                'expires_in' => -3600, // Already expired
            ];
            $response = createMockResponseForClientCreds($responseData);
            $auth->handleAuthenticationResponse($response);

            $authHeader = $auth->getAuthorizationHeader();
            expect($authHeader)->toBeNull();
        });

        test('clearToken removes stored token and forces re-authentication', function (): void {
            $auth = new ClientCredentialAuthentication(
                'client_id',
                'client_secret',
                'https://api.example.com',
                'https://auth.example.com',
            );

            // First authenticate
            $responseData = [
                'access_token' => 'test_token',
                'expires_in' => 3600,
            ];
            $response = createMockResponseForClientCreds($responseData);
            $auth->handleAuthenticationResponse($response);

            expect($auth->getToken())->not->toBeNull();
            expect($auth->getAuthorizationHeader())->toBe('test_token');

            // Clear token
            $auth->clearToken();

            expect($auth->getToken())->toBeNull();
            expect($auth->getAuthorizationHeader())->toBeNull();
        });

        test('requiresAuthentication always returns true', function (): void {
            $auth = new ClientCredentialAuthentication(
                'client_id',
                'client_secret',
                'https://api.example.com',
                'https://auth.example.com',
            );

            expect($auth->requiresAuthentication())->toBeTrue();
        });

        test('getAuthenticationRequest returns null when token exists and is not expired', function (): void {
            $auth = new ClientCredentialAuthentication(
                'client_id',
                'client_secret',
                'https://api.example.com',
                'https://auth.example.com',
            );

            // First authenticate
            $responseData = [
                'access_token' => 'valid_token',
                'expires_in' => 3600,
            ];
            $response = createMockResponseForClientCreds($responseData);
            $auth->handleAuthenticationResponse($response);

            $streamFactory = test()->createMock(StreamFactoryInterface::class);
            $streamFactory->expects(test()->never())
                ->method('createStream');

            $request = $auth->getAuthenticationRequest($streamFactory);
            expect($request)->toBeNull();
        });

        test('handleAuthenticationResponse resets authentication flag after processing', function (): void {
            $auth = new ClientCredentialAuthentication(
                'client_id',
                'client_secret',
                'https://api.example.com',
                'https://auth.example.com',
            );

            $streamFactory = test()->createMock(StreamFactoryInterface::class);
            $streamFactory->method('createStream')
                ->willReturn(test()->createMock(StreamInterface::class));

            // Trigger authentication request
            $request1 = $auth->getAuthenticationRequest($streamFactory);
            expect($request1)->toBeInstanceOf(RequestContext::class);

            // Should return null while authenticating
            $request2 = $auth->getAuthenticationRequest($streamFactory);
            expect($request2)->toBeNull();

            // Process authentication response
            $responseData = [
                'access_token' => 'new_token',
                'expires_in' => 3600,
            ];
            $response = createMockResponseForClientCreds($responseData);
            $auth->handleAuthenticationResponse($response);

            // Clear token to allow new authentication request
            $auth->clearToken();

            // Should now allow new authentication request
            $request3 = $auth->getAuthenticationRequest($streamFactory);
            expect($request3)->toBeInstanceOf(RequestContext::class);
        });

        test('handleAuthenticationResponse with JWT validates array audience claim', function (): void {
            $expectedIssuer = 'https://auth.example.com';
            $expectedAudience = 'https://api.example.com';

            $auth = new ClientCredentialAuthentication(
                'client_id',
                'client_secret',
                $expectedAudience,
                $expectedIssuer,
            );

            $currentTime = time();
            $header = ['typ' => 'JWT', 'alg' => 'HS256'];
            $payload = [
                'iss' => $expectedIssuer,
                'aud' => [$expectedAudience, 'https://other.example.com'], // Array audience
                'exp' => $currentTime + 3600,
                'iat' => $currentTime,
            ];

            $jwtToken = createValidJwtForClientCreds($header, $payload);
            $responseData = [
                'access_token' => $jwtToken,
                'expires_in' => 3600,
            ];

            $response = createMockResponseForClientCreds($responseData);

            $auth->handleAuthenticationResponse($response);

            $token = $auth->getToken();
            expect($token)->toBeInstanceOf(AccessTokenInterface::class);
            expect($token->getToken())->toBe($jwtToken);
        });
    });
});
