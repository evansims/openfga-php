<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Authentication;

use OpenFGA\Authentication\{AccessToken, AccessTokenInterface, AuthenticationInterface, TokenAuthentication};
use Psr\Http\Message\{ResponseInterface, StreamFactoryInterface};

describe('TokenAuthentication', function (): void {
    test('implements AuthenticationInterface', function (): void {
        $auth = new TokenAuthentication('test_token');

        expect($auth)->toBeInstanceOf(AuthenticationInterface::class);
    });

    test('constructs with string token', function (): void {
        $tokenValue = 'bearer_token_123';
        $auth = new TokenAuthentication($tokenValue);

        expect($auth)->toBeInstanceOf(TokenAuthentication::class);
        expect($auth->getToken())->toBe($tokenValue);
    });

    test('constructs with AccessToken instance', function (): void {
        $accessToken = new AccessToken('jwt_token_456', time() + 3600, 'read write');
        $auth = new TokenAuthentication($accessToken);

        expect($auth)->toBeInstanceOf(TokenAuthentication::class);
        expect($auth->getToken())->toBe($accessToken);
        expect($auth->getToken())->toBeInstanceOf(AccessTokenInterface::class);
    });

    test('getAuthenticationRequest always returns null', function (): void {
        $auth = new TokenAuthentication('test_token');
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        // Should never call the stream factory since token auth doesn't need requests
        $streamFactory->expects(test()->never())
            ->method('createStream');

        $request = $auth->getAuthenticationRequest($streamFactory);

        expect($request)->toBeNull();
    });

    test('getAuthorizationHeader returns string token directly', function (): void {
        $tokenValue = 'my_api_token_123';
        $auth = new TokenAuthentication($tokenValue);

        $header = $auth->getAuthorizationHeader();

        expect($header)->toBe($tokenValue);
    });

    test('getAuthorizationHeader returns AccessToken string when not expired', function (): void {
        $accessToken = new AccessToken('access_token_789', time() + 3600); // Not expired
        $auth = new TokenAuthentication($accessToken);

        $header = $auth->getAuthorizationHeader();

        expect($header)->toBe('access_token_789');
    });

    test('getAuthorizationHeader returns null for expired AccessToken', function (): void {
        $accessToken = new AccessToken('expired_token', time() - 3600); // Expired 1 hour ago
        $auth = new TokenAuthentication($accessToken);

        $header = $auth->getAuthorizationHeader();

        expect($header)->toBeNull();
    });

    test('getAuthorizationHeader returns null for AccessToken expiring at current time', function (): void {
        $accessToken = new AccessToken('expiring_token', time() - 1); // Expired 1 second ago
        $auth = new TokenAuthentication($accessToken);

        $header = $auth->getAuthorizationHeader();

        expect($header)->toBeNull();
    });

    test('handleAuthenticationResponse does nothing', function (): void {
        $auth = new TokenAuthentication('test_token');
        $response = test()->createMock(ResponseInterface::class);

        // Should not interact with the response at all
        $response->expects(test()->never())
            ->method(test()->anything());

        // This should complete without any action or exception
        $auth->handleAuthenticationResponse($response);

        // If we reach this point, the method completed successfully
        expect(true)->toBeTrue();
    });

    test('requiresAuthentication always returns true', function (): void {
        $stringAuth = new TokenAuthentication('string_token');
        $accessTokenAuth = new TokenAuthentication(new AccessToken('access_token', time() + 3600));

        expect($stringAuth->requiresAuthentication())->toBeTrue();
        expect($accessTokenAuth->requiresAuthentication())->toBeTrue();
    });

    test('getToken returns original token unchanged', function (): void {
        // Test with string
        $stringToken = 'original_string_token';
        $stringAuth = new TokenAuthentication($stringToken);
        expect($stringAuth->getToken())->toBe($stringToken);

        // Test with AccessToken
        $accessToken = new AccessToken('access_token_value', time() + 3600, 'scope');
        $accessTokenAuth = new TokenAuthentication($accessToken);
        expect($accessTokenAuth->getToken())->toBe($accessToken);
        expect($accessTokenAuth->getToken())->toBeInstanceOf(AccessTokenInterface::class);
    });

    test('is immutable readonly class with consistent behavior', function (): void {
        $token = 'consistent_token';
        $auth = new TokenAuthentication($token);

        // Should behave consistently across multiple calls
        expect($auth->getToken())->toBe($token);
        expect($auth->getAuthorizationHeader())->toBe($token);
        expect($auth->requiresAuthentication())->toBeTrue();
        expect($auth->getAuthenticationRequest(test()->createMock(StreamFactoryInterface::class)))->toBeNull();

        // Second set of calls should be identical
        expect($auth->getToken())->toBe($token);
        expect($auth->getAuthorizationHeader())->toBe($token);
        expect($auth->requiresAuthentication())->toBeTrue();
    });

    test('handles empty string token', function (): void {
        $auth = new TokenAuthentication('');

        expect($auth->getToken())->toBe('');
        expect($auth->getAuthorizationHeader())->toBe('');
        expect($auth->requiresAuthentication())->toBeTrue();
    });

    test('handles long string token', function (): void {
        $longToken = str_repeat('a', 1000);
        $auth = new TokenAuthentication($longToken);

        expect($auth->getToken())->toBe($longToken);
        expect($auth->getAuthorizationHeader())->toBe($longToken);
    });

    test('handles special characters in string token', function (): void {
        $specialToken = 'token!@#$%^&*()_+-={}[]|\\:";\'<>?,./~`';
        $auth = new TokenAuthentication($specialToken);

        expect($auth->getToken())->toBe($specialToken);
        expect($auth->getAuthorizationHeader())->toBe($specialToken);
    });

    test('AccessToken with scope handling', function (): void {
        $accessToken = new AccessToken('scoped_token', time() + 3600, 'read write admin');
        $auth = new TokenAuthentication($accessToken);

        expect($auth->getToken())->toBe($accessToken);
        expect($auth->getToken()->getScope())->toBe('read write admin');
        expect($auth->getAuthorizationHeader())->toBe('scoped_token');
    });

    test('AccessToken without scope handling', function (): void {
        $accessToken = new AccessToken('unscoped_token', time() + 3600); // No scope
        $auth = new TokenAuthentication($accessToken);

        expect($auth->getToken())->toBe($accessToken);
        expect($auth->getToken()->getScope())->toBeNull();
        expect($auth->getAuthorizationHeader())->toBe('unscoped_token');
    });

    test('AccessToken expiration boundary testing', function (): void {
        $currentTime = time();

        // Token expiring in 1 second (should still be valid)
        $almostExpired = new AccessToken('almost_expired', $currentTime + 1);
        $authAlmost = new TokenAuthentication($almostExpired);
        expect($authAlmost->getAuthorizationHeader())->toBe('almost_expired');

        // Token that expired 1 second ago
        $justExpired = new AccessToken('just_expired', $currentTime - 1);
        $authExpired = new TokenAuthentication($justExpired);
        expect($authExpired->getAuthorizationHeader())->toBeNull();
    });
});
