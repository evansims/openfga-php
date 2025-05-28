<?php

declare(strict_types=1);

use OpenFGA\Authentication\{AuthenticationInterface, ClientCredentialAuthentication};
use OpenFGA\Network\{RequestContext, RequestMethod};
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

test('ClientCredentialAuthentication implements AuthenticationInterface', function (): void {
    $auth = new ClientCredentialAuthentication(
        'client_id',
        'client_secret',
        'audience',
        'issuer',
    );

    expect($auth)->toBeInstanceOf(AuthenticationInterface::class);
});

test('ClientCredentialAuthentication constructs with required parameters', function (): void {
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

test('ClientCredentialAuthentication getRequest returns correct RequestContext', function (): void {
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

    $requestContext = $auth->getRequest($streamFactory);

    expect($requestContext)->toBeInstanceOf(RequestContext::class);
    expect($requestContext->getMethod())->toBe(RequestMethod::POST);
    expect($requestContext->getUrl())->toBe('/oauth/token');
    expect($requestContext->getBody())->toBe($stream);
    expect($requestContext->getHeaders())->toBe([
        'Accept' => 'application/json',
        'Content-Type' => 'application/x-www-form-urlencoded',
    ]);
});

test('ClientCredentialAuthentication handles special characters in credentials', function (): void {
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

    $requestContext = $auth->getRequest($streamFactory);

    expect($requestContext)->toBeInstanceOf(RequestContext::class);
});

test('ClientCredentialAuthentication handles empty strings', function (): void {
    $auth = new ClientCredentialAuthentication('', '', '', '');

    $expectedBody = json_encode([
        'grant_type' => 'client_credentials',
        'client_id' => '',
        'client_secret' => '',
        'audience' => '',
    ], JSON_THROW_ON_ERROR);

    $stream = test()->createMock(StreamInterface::class);

    $streamFactory = test()->createMock(StreamFactoryInterface::class);
    $streamFactory->expects(test()->once())
        ->method('createStream')
        ->with($expectedBody)
        ->willReturn($stream);

    $requestContext = $auth->getRequest($streamFactory);

    expect($requestContext)->toBeInstanceOf(RequestContext::class);
});

test('ClientCredentialAuthentication handles very long credentials', function (): void {
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

    $requestContext = $auth->getRequest($streamFactory);

    expect($requestContext)->toBeInstanceOf(RequestContext::class);
});

test('ClientCredentialAuthentication creates consistent requests', function (): void {
    $auth = new ClientCredentialAuthentication(
        'client_id',
        'client_secret',
        'audience',
        'issuer',
    );

    $stream1 = test()->createMock(StreamInterface::class);
    $stream2 = test()->createMock(StreamInterface::class);

    $streamFactory = test()->createMock(StreamFactoryInterface::class);
    $streamFactory->expects(test()->exactly(2))
        ->method('createStream')
        ->willReturnOnConsecutiveCalls($stream1, $stream2);

    $request1 = $auth->getRequest($streamFactory);
    $request2 = $auth->getRequest($streamFactory);

    expect($request1->getMethod())->toBe($request2->getMethod());
    expect($request1->getUrl())->toBe($request2->getUrl());
    expect($request1->getHeaders())->toBe($request2->getHeaders());
});
