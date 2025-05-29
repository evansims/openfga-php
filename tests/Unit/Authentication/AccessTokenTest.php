<?php

declare(strict_types=1);

use OpenFGA\Authentication\{AccessToken, AccessTokenInterface};
use Psr\Http\Message\{ResponseInterface, StreamInterface};

describe('AccessToken', function (): void {
    test('implements AccessTokenInterface', function (): void {
        $token = new AccessToken('token123', time() + 3600);

        expect($token)->toBeInstanceOf(AccessTokenInterface::class);
    });

    test('constructs with required parameters', function (): void {
        $tokenValue = 'access_token_123';
        $expiresAt = time() + 3600;

        $token = new AccessToken($tokenValue, $expiresAt);

        expect($token->getToken())->toBe($tokenValue);
        expect($token->getExpires())->toBe($expiresAt);
        expect($token->getScope())->toBeNull();
    });

    test('constructs with scope', function (): void {
        $tokenValue = 'access_token_123';
        $expiresAt = time() + 3600;
        $scope = 'read write';

        $token = new AccessToken($tokenValue, $expiresAt, $scope);

        expect($token->getToken())->toBe($tokenValue);
        expect($token->getExpires())->toBe($expiresAt);
        expect($token->getScope())->toBe($scope);
    });

    test('toString returns token value', function (): void {
        $tokenValue = 'access_token_123';
        $token = new AccessToken($tokenValue, time() + 3600);

        expect((string) $token)->toBe($tokenValue);
    });

    test('isExpired returns false for future expiration', function (): void {
        $token = new AccessToken('token', time() + 3600); // 1 hour from now

        expect($token->isExpired())->toBeFalse();
    });

    test('isExpired returns true for past expiration', function (): void {
        $token = new AccessToken('token', time() - 3600); // 1 hour ago

        expect($token->isExpired())->toBeTrue();
    });

    test('isExpired returns true for current time', function (): void {
        $currentTime = time();
        $token = new AccessToken('token', $currentTime);

        // At exact expiration time, token is expired (expires < time())
        expect($token->isExpired())->toBeFalse();

        // One second past expiration, token should be expired
        $expiredToken = new AccessToken('token', $currentTime - 1);
        expect($expiredToken->isExpired())->toBeTrue();
    });

    test('fromResponse parses valid response', function (): void {
        $responseData = [
            'access_token' => 'test_token_123',
            'expires_in' => 3600,
            'scope' => 'read write',
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        $beforeTime = time();
        $token = AccessToken::fromResponse($response);
        $afterTime = time();

        expect($token)->toBeInstanceOf(AccessToken::class);
        expect($token->getToken())->toBe('test_token_123');
        expect($token->getExpires())->toBeGreaterThanOrEqual($beforeTime + 3600);
        expect($token->getExpires())->toBeLessThanOrEqual($afterTime + 3600);
        expect($token->getScope())->toBe('read write');
    });

    test('fromResponse handles response without scope', function (): void {
        $responseData = [
            'access_token' => 'test_token_456',
            'expires_in' => 7200,
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        $token = AccessToken::fromResponse($response);

        expect($token->getToken())->toBe('test_token_456');
        expect($token->getScope())->toBeNull();
    });

    test('fromResponse throws on invalid JSON', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn('invalid json');

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        $this->expectException(JsonException::class);
        AccessToken::fromResponse($response);
    });

    test('fromResponse throws on non-array response', function (): void {
        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn('"not an array"');

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid response format');
        AccessToken::fromResponse($response);
    });

    test('fromResponse throws on missing access_token', function (): void {
        $responseData = [
            'expires_in' => 3600,
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing required fields in response');
        AccessToken::fromResponse($response);
    });

    test('fromResponse throws on missing expires_in', function (): void {
        $responseData = [
            'access_token' => 'test_token',
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing required fields in response');
        AccessToken::fromResponse($response);
    });

    test('fromResponse throws on non-string access_token', function (): void {
        $responseData = [
            'access_token' => 123,
            'expires_in' => 3600,
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('access_token must be a string');
        AccessToken::fromResponse($response);
    });

    test('fromResponse throws on non-integer expires_in', function (): void {
        $responseData = [
            'access_token' => 'test_token',
            'expires_in' => '3600',
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('expires_in must be an integer');
        AccessToken::fromResponse($response);
    });

    test('fromResponse handles non-string scope gracefully', function (): void {
        $responseData = [
            'access_token' => 'test_token',
            'expires_in' => 3600,
            'scope' => 123, // Invalid type
        ];

        $stream = test()->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn(json_encode($responseData));

        $response = test()->createMock(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);

        $token = AccessToken::fromResponse($response);

        expect($token->getScope())->toBeNull();
    });

    test('handles empty scope string', function (): void {
        $token = new AccessToken('token', time() + 3600, '');

        expect($token->getScope())->toBe('');
    });
});
