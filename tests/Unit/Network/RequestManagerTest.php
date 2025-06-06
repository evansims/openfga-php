<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Network;

use Exception;
use OpenFGA\Client;
use OpenFGA\Exceptions\{NetworkException};
use OpenFGA\Network\{RequestContext, RequestManager, RequestMethod};
use OpenFGA\Requests\RequestInterface as ClientRequestInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, ResponseFactoryInterface, ResponseInterface, StreamFactoryInterface, StreamInterface};
use Psr\Http\Message\UriInterface;

describe('RequestManager', function (): void {
    test('constructs with required parameters', function (): void {
        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
        );

        expect($manager)->toBeInstanceOf(RequestManager::class);
    });

    test('constructs with all parameters', function (): void {
        $httpClient = test()->createMock(ClientInterface::class);
        $responseFactory = test()->createMock(ResponseFactoryInterface::class);
        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $requestFactory = test()->createMock(RequestFactoryInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            authorizationHeader: 'Bearer token123',
            httpClient: $httpClient,
            httpResponseFactory: $responseFactory,
            httpStreamFactory: $streamFactory,
            httpRequestFactory: $requestFactory,
        );

        expect($manager)->toBeInstanceOf(RequestManager::class);
    });

    test('getHttpClient returns provided client', function (): void {
        $httpClient = test()->createMock(ClientInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpClient: $httpClient,
        );

        expect($manager->getHttpClient())->toBe($httpClient);
    });

    test('getHttpRequestFactory returns provided factory', function (): void {
        $requestFactory = test()->createMock(RequestFactoryInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpRequestFactory: $requestFactory,
        );

        expect($manager->getHttpRequestFactory())->toBe($requestFactory);
    });

    test('getHttpResponseFactory returns provided factory', function (): void {
        $responseFactory = test()->createMock(ResponseFactoryInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpResponseFactory: $responseFactory,
        );

        expect($manager->getHttpResponseFactory())->toBe($responseFactory);
    });

    test('getHttpStreamFactory returns provided factory', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpStreamFactory: $streamFactory,
        );

        expect($manager->getHttpStreamFactory())->toBe($streamFactory);
    });

    test('request creates PSR-7 request with authorization header', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $requestFactory = test()->createMock(RequestFactoryInterface::class);
        $psrRequest = test()->createMock(RequestInterface::class);
        $stream = test()->createMock(StreamInterface::class);

        $requestContext = new RequestContext(
            method: RequestMethod::POST,
            url: '/stores',
            body: $stream,
            headers: ['Content-Type' => 'application/json'],
            useApiUrl: true,
        );

        $clientRequest = test()->createMock(ClientRequestInterface::class);
        $clientRequest->expects(test()->once())
            ->method('getRequest')
            ->with($streamFactory)
            ->willReturn($requestContext);

        $requestFactory->expects(test()->once())
            ->method('createRequest')
            ->with('POST', 'https://api.example.com/stores')
            ->willReturn($psrRequest);

        $psrRequest->expects(test()->exactly(3))
            ->method('withHeader')
            ->willReturnCallback(function (string $name, string $value) use ($psrRequest): MockObject {
                // Verify expected headers
                expect($name)->toBeIn(['Content-Type', 'User-Agent', 'Authorization']);

                if ('Content-Type' === $name) {
                    expect($value)->toBe('application/json');
                } elseif ('User-Agent' === $name) {
                    expect($value)->toBe('openfga-sdk php/' . Client::VERSION);
                } elseif ('Authorization' === $name) {
                    expect($value)->toBe('Bearer token123');
                }

                return $psrRequest;
            });

        $psrRequest->expects(test()->once())
            ->method('withBody')
            ->with($stream)
            ->willReturn($psrRequest);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            authorizationHeader: 'Bearer token123',
            httpStreamFactory: $streamFactory,
            httpRequestFactory: $requestFactory,
        );

        $result = $manager->request($clientRequest);

        expect($result)->toBe($psrRequest);
    });

    test('request handles non-API URLs', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $requestFactory = test()->createMock(RequestFactoryInterface::class);
        $psrRequest = test()->createMock(RequestInterface::class);
        $stream = test()->createMock(StreamInterface::class);

        $requestContext = new RequestContext(
            method: RequestMethod::GET,
            url: 'https://other.example.com/resource',
            body: $stream,
            headers: [],
            useApiUrl: false,
        );

        $clientRequest = test()->createMock(ClientRequestInterface::class);
        $clientRequest->expects(test()->once())
            ->method('getRequest')
            ->with($streamFactory)
            ->willReturn($requestContext);

        $requestFactory->expects(test()->once())
            ->method('createRequest')
            ->with('GET', 'https://other.example.com/resource')
            ->willReturn($psrRequest);

        $psrRequest->expects(test()->exactly(2))
            ->method('withHeader')
            ->willReturn($psrRequest);

        $psrRequest->expects(test()->once())
            ->method('withBody')
            ->with($stream)
            ->willReturn($psrRequest);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpStreamFactory: $streamFactory,
            httpRequestFactory: $requestFactory,
        );

        $result = $manager->request($clientRequest);

        expect($result)->toBe($psrRequest);
    });

    test('send executes request and returns response', function (): void {
        $httpClient = test()->createMock(ClientInterface::class);
        $psrRequest = test()->createMock(RequestInterface::class);
        $psrResponse = test()->createMock(ResponseInterface::class);
        $uri = test()->createMock(UriInterface::class);

        // Mock successful response (status 200)
        $psrResponse->method('getStatusCode')->willReturn(200);

        // Mock URI for circuit breaker tracking
        $uri->method('__toString')->willReturn('https://api.example.com/test');
        $psrRequest->method('getUri')->willReturn($uri);

        $httpClient->expects(test()->once())
            ->method('sendRequest')
            ->with($psrRequest)
            ->willReturn($psrResponse);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpClient: $httpClient,
        );

        $result = $manager->send($psrRequest);

        expect($result)->toBe($psrResponse);
    });

    test('handleResponseException throws appropriate errors', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $response = test()->createMock(ResponseInterface::class);
        $body = test()->createMock(StreamInterface::class);

        $response->method('getStatusCode')->willReturn(400);
        $response->method('getBody')->willReturn($body);
        $body->method('getContents')->willReturn('{"error": "Invalid request"}');

        RequestManager::handleResponseException($response, $request);
    })->throws(NetworkException::class);

    test('handleResponseException throws error for 401', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $response = test()->createMock(ResponseInterface::class);
        $body = test()->createMock(StreamInterface::class);

        $response->method('getStatusCode')->willReturn(401);
        $response->method('getBody')->willReturn($body);
        $body->method('getContents')->willReturn('');

        RequestManager::handleResponseException($response, $request);
    })->throws(NetworkException::class);

    test('handleResponseException throws error for unknown status', function (): void {
        $request = test()->createMock(RequestInterface::class);
        $response = test()->createMock(ResponseInterface::class);
        $body = test()->createMock(StreamInterface::class);

        $response->method('getStatusCode')->willReturn(418);
        $response->method('getBody')->willReturn($body);
        $body->method('getContents')->willReturn('');

        RequestManager::handleResponseException($response, $request);
    })->throws(NetworkException::class);

    test('send throws exception on network failure', function (): void {
        $httpClient = test()->createMock(ClientInterface::class);
        $psrRequest = test()->createMock(RequestInterface::class);
        $uri = test()->createMock(UriInterface::class);

        // Mock URI for circuit breaker tracking
        $uri->method('__toString')->willReturn('https://api.example.com/test');
        $psrRequest->method('getUri')->willReturn($uri);

        $httpClient->expects(test()->once())
            ->method('sendRequest')
            ->with($psrRequest)
            ->willThrowException(new Exception('Network error'));

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpClient: $httpClient,
        );

        $manager->send($psrRequest);
    })->throws(Exception::class, 'Network error');

    test('RequestMethod enum has correct values', function (): void {
        expect(RequestMethod::DELETE->value)->toBe('DELETE');
        expect(RequestMethod::GET->value)->toBe('GET');
        expect(RequestMethod::POST->value)->toBe('POST');
        expect(RequestMethod::PUT->value)->toBe('PUT');
    });

    test('handles URL with trailing slash', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $requestFactory = test()->createMock(RequestFactoryInterface::class);
        $psrRequest = test()->createMock(RequestInterface::class);
        $stream = test()->createMock(StreamInterface::class);

        $requestContext = new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/',
            body: $stream,
            headers: [],
            useApiUrl: true,
        );

        $clientRequest = test()->createMock(ClientRequestInterface::class);
        $clientRequest->expects(test()->once())
            ->method('getRequest')
            ->with($streamFactory)
            ->willReturn($requestContext);

        $requestFactory->expects(test()->once())
            ->method('createRequest')
            ->with('GET', 'https://api.example.com//stores')
            ->willReturn($psrRequest);

        $psrRequest->method('withHeader')->willReturn($psrRequest);
        $psrRequest->method('withBody')->willReturn($psrRequest);

        $manager = new RequestManager(
            url: 'https://api.example.com/',
            maxRetries: 3,
            httpStreamFactory: $streamFactory,
            httpRequestFactory: $requestFactory,
        );

        $result = $manager->request($clientRequest);

        expect($result)->toBe($psrRequest);
    });
});
