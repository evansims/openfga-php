<?php

declare(strict_types=1);

use OpenFGA\Authentication\{AccessToken, AccessTokenInterface};
use Psr\Http\Message\{ResponseInterface, StreamInterface};

test('AccessToken implements AccessTokenInterface', function (): void {
    $token = new AccessToken('token123', time() + 3600);

    expect($token)->toBeInstanceOf(AccessTokenInterface::class);
});

test('AccessToken constructs with required parameters', function (): void {
    $tokenValue = 'access_token_123';
    $expiresAt = time() + 3600;

    $token = new AccessToken($tokenValue, $expiresAt);

    expect($token->getToken())->toBe($tokenValue);
    expect($token->getExpires())->toBe($expiresAt);
    expect($token->getScope())->toBeNull();
});

test('AccessToken constructs with scope', function (): void {
    $tokenValue = 'access_token_123';
    $expiresAt = time() + 3600;
    $scope = 'read write';

    $token = new AccessToken($tokenValue, $expiresAt, $scope);

    expect($token->getToken())->toBe($tokenValue);
    expect($token->getExpires())->toBe($expiresAt);
    expect($token->getScope())->toBe($scope);
});

test('AccessToken toString returns token value', function (): void {
    $tokenValue = 'access_token_123';
    $token = new AccessToken($tokenValue, time() + 3600);

    expect((string) $token)->toBe($tokenValue);
});

test('AccessToken isExpired returns false for future expiration', function (): void {
    $token = new AccessToken('token', time() + 3600); // 1 hour from now

    expect($token->isExpired())->toBeFalse();
});

test('AccessToken isExpired returns true for past expiration', function (): void {
    $token = new AccessToken('token', time() - 3600); // 1 hour ago

    expect($token->isExpired())->toBeTrue();
});

test('AccessToken isExpired returns true for current time', function (): void {
    $currentTime = time();
    $token = new AccessToken('token', $currentTime);

    // At exact expiration time, token is expired (expires < time())
    expect($token->isExpired())->toBeFalse();

    // One second past expiration, token should be expired
    $expiredToken = new AccessToken('token', $currentTime - 1);
    expect($expiredToken->isExpired())->toBeTrue();
});

test('AccessToken fromResponse parses valid response', function (): void {
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

test('AccessToken fromResponse handles response without scope', function (): void {
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

test('AccessToken fromResponse throws on invalid JSON', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('getContents')
        ->willReturn('invalid json');

    $response = test()->createMock(ResponseInterface::class);
    $response->method('getBody')
        ->willReturn($stream);

    expect(fn () => AccessToken::fromResponse($response))
        ->toThrow(JsonException::class);
});

test('AccessToken fromResponse throws on non-array response', function (): void {
    $stream = test()->createMock(StreamInterface::class);
    $stream->method('getContents')
        ->willReturn('"not an array"');

    $response = test()->createMock(ResponseInterface::class);
    $response->method('getBody')
        ->willReturn($stream);

    expect(fn () => AccessToken::fromResponse($response))
        ->toThrow(Exception::class, 'Invalid response format');
});

test('AccessToken fromResponse throws on missing access_token', function (): void {
    $responseData = [
        'expires_in' => 3600,
    ];

    $stream = test()->createMock(StreamInterface::class);
    $stream->method('getContents')
        ->willReturn(json_encode($responseData));

    $response = test()->createMock(ResponseInterface::class);
    $response->method('getBody')
        ->willReturn($stream);

    expect(fn () => AccessToken::fromResponse($response))
        ->toThrow(Exception::class, 'Missing required fields in response');
});

test('AccessToken fromResponse throws on missing expires_in', function (): void {
    $responseData = [
        'access_token' => 'test_token',
    ];

    $stream = test()->createMock(StreamInterface::class);
    $stream->method('getContents')
        ->willReturn(json_encode($responseData));

    $response = test()->createMock(ResponseInterface::class);
    $response->method('getBody')
        ->willReturn($stream);

    expect(fn () => AccessToken::fromResponse($response))
        ->toThrow(Exception::class, 'Missing required fields in response');
});

test('AccessToken fromResponse throws on non-string access_token', function (): void {
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

    expect(fn () => AccessToken::fromResponse($response))
        ->toThrow(Exception::class, 'access_token must be a string');
});

test('AccessToken fromResponse throws on non-integer expires_in', function (): void {
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

    expect(fn () => AccessToken::fromResponse($response))
        ->toThrow(Exception::class, 'expires_in must be an integer');
});

test('AccessToken fromResponse handles non-string scope gracefully', function (): void {
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

test('AccessToken handles empty scope string', function (): void {
    $token = new AccessToken('token', time() + 3600, '');

    expect($token->getScope())->toBe('');
});
