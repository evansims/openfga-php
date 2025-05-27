<?php

declare(strict_types=1);

use OpenFGA\Client;
use OpenFGA\Exceptions\{NetworkException};
use OpenFGA\Network\{RequestManager, RequestMethod};
use OpenFGA\Requests\RequestInterface as ClientRequestInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, ResponseFactoryInterface, ResponseInterface, StreamFactoryInterface, StreamInterface};

describe('RequestManager', function (): void {
    test('constructs with required parameters', function (): void {
        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
        );

        expect($manager)->toBeInstanceOf(RequestManager::class);
    });

    test('constructs with all parameters', function (): void {
        $httpClient = Mockery::mock(ClientInterface::class);
        $responseFactory = Mockery::mock(ResponseFactoryInterface::class);
        $streamFactory = Mockery::mock(StreamFactoryInterface::class);
        $requestFactory = Mockery::mock(RequestFactoryInterface::class);

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
        $httpClient = Mockery::mock(ClientInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpClient: $httpClient,
        );

        expect($manager->getHttpClient())->toBe($httpClient);
    });

    test('getHttpRequestFactory returns provided factory', function (): void {
        $requestFactory = Mockery::mock(RequestFactoryInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpRequestFactory: $requestFactory,
        );

        expect($manager->getHttpRequestFactory())->toBe($requestFactory);
    });

    test('getHttpResponseFactory returns provided factory', function (): void {
        $responseFactory = Mockery::mock(ResponseFactoryInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpResponseFactory: $responseFactory,
        );

        expect($manager->getHttpResponseFactory())->toBe($responseFactory);
    });

    test('getHttpStreamFactory returns provided factory', function (): void {
        $streamFactory = Mockery::mock(StreamFactoryInterface::class);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpStreamFactory: $streamFactory,
        );

        expect($manager->getHttpStreamFactory())->toBe($streamFactory);
    });

    test('request creates PSR-7 request with authorization header', function (): void {
        $streamFactory = Mockery::mock(StreamFactoryInterface::class);
        $requestFactory = Mockery::mock(RequestFactoryInterface::class);
        $psrRequest = Mockery::mock(RequestInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $requestContext = new OpenFGA\Network\RequestContext(
            method: RequestMethod::POST,
            url: '/stores',
            body: $stream,
            headers: ['Content-Type' => 'application/json'],
            useApiUrl: true,
        );

        $clientRequest = Mockery::mock(ClientRequestInterface::class);
        $clientRequest->shouldReceive('getRequest')
            ->with($streamFactory)
            ->andReturn($requestContext);

        $requestFactory->shouldReceive('createRequest')
            ->with('POST', 'https://api.example.com/stores')
            ->andReturn($psrRequest);

        $psrRequest->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->andReturn($psrRequest);
        $psrRequest->shouldReceive('withHeader')
            ->with('User-Agent', 'openfga-sdk php/' . Client::VERSION)
            ->andReturn($psrRequest);
        $psrRequest->shouldReceive('withHeader')
            ->with('Authorization', 'Bearer token123')
            ->andReturn($psrRequest);
        $psrRequest->shouldReceive('withBody')
            ->with($stream)
            ->andReturn($psrRequest);

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
        $streamFactory = Mockery::mock(StreamFactoryInterface::class);
        $requestFactory = Mockery::mock(RequestFactoryInterface::class);
        $psrRequest = Mockery::mock(RequestInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $requestContext = new OpenFGA\Network\RequestContext(
            method: RequestMethod::GET,
            url: 'https://other.example.com/resource',
            body: $stream,
            headers: [],
            useApiUrl: false,
        );

        $clientRequest = Mockery::mock(ClientRequestInterface::class);
        $clientRequest->shouldReceive('getRequest')
            ->with($streamFactory)
            ->andReturn($requestContext);

        $requestFactory->shouldReceive('createRequest')
            ->with('GET', 'https://other.example.com/resource')
            ->andReturn($psrRequest);

        $psrRequest->shouldReceive('withHeader')
            ->with('User-Agent', 'openfga-sdk php/' . Client::VERSION)
            ->andReturn($psrRequest);
        $psrRequest->shouldReceive('withBody')
            ->with($stream)
            ->andReturn($psrRequest);

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
        $httpClient = Mockery::mock(ClientInterface::class);
        $psrRequest = Mockery::mock(RequestInterface::class);
        $psrResponse = Mockery::mock(ResponseInterface::class);

        $httpClient->shouldReceive('sendRequest')
            ->with($psrRequest)
            ->andReturn($psrResponse);

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpClient: $httpClient,
        );

        $result = $manager->send($psrRequest);

        expect($result)->toBe($psrResponse);
    });

    test('handleResponseException throws appropriate errors', function (): void {
        $request = Mockery::mock(RequestInterface::class);

        // Test 400 error
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')->andReturn(400);
        $response->shouldReceive('getBody->getContents')->andReturn('{"error": "Invalid request"}');

        expect(fn () => RequestManager::handleResponseException($response, $request))
            ->toThrow(NetworkException::class);

        // Test 401 error
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')->andReturn(401);
        $response->shouldReceive('getBody->getContents')->andReturn('');

        expect(fn () => RequestManager::handleResponseException($response, $request))
            ->toThrow(NetworkException::class);

        // Test unknown status code
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')->andReturn(418);
        $response->shouldReceive('getBody->getContents')->andReturn('');

        expect(fn () => RequestManager::handleResponseException($response, $request))
            ->toThrow(NetworkException::class);
    });

    test('send throws exception on network failure', function (): void {
        $httpClient = Mockery::mock(ClientInterface::class);
        $psrRequest = Mockery::mock(RequestInterface::class);

        $httpClient->shouldReceive('sendRequest')
            ->with($psrRequest)
            ->once()
            ->andThrow(new Exception('Network error'));

        $manager = new RequestManager(
            url: 'https://api.example.com',
            maxRetries: 3,
            httpClient: $httpClient,
        );

        expect(fn () => $manager->send($psrRequest))
            ->toThrow(Exception::class, 'Network error');
    });

    test('RequestMethod enum has correct values', function (): void {
        expect(RequestMethod::DELETE->value)->toBe('DELETE');
        expect(RequestMethod::GET->value)->toBe('GET');
        expect(RequestMethod::POST->value)->toBe('POST');
        expect(RequestMethod::PUT->value)->toBe('PUT');
    });

    test('handles URL with trailing slash', function (): void {
        $streamFactory = Mockery::mock(StreamFactoryInterface::class);
        $requestFactory = Mockery::mock(RequestFactoryInterface::class);
        $psrRequest = Mockery::mock(RequestInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $requestContext = new OpenFGA\Network\RequestContext(
            method: RequestMethod::GET,
            url: '/stores/',
            body: $stream,
            headers: [],
            useApiUrl: true,
        );

        $clientRequest = Mockery::mock(ClientRequestInterface::class);
        $clientRequest->shouldReceive('getRequest')
            ->with($streamFactory)
            ->andReturn($requestContext);

        $requestFactory->shouldReceive('createRequest')
            ->with('GET', 'https://api.example.com//stores')
            ->andReturn($psrRequest);

        $psrRequest->shouldReceive('withHeader')
            ->andReturn($psrRequest);
        $psrRequest->shouldReceive('withBody')
            ->andReturn($psrRequest);

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
