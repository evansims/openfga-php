<?php

declare(strict_types=1);

use OpenFGA\Client;

test('all client methods exist and are callable', function (): void {
    $responseFactory = new Nyholm\Psr7\Factory\Psr17Factory();
    $httpClient = new Buzz\Client\FileGetContents($responseFactory);
    $url = getenv('FGA_API_URL') ?: 'http://openfga:8080';

    $client = new Client(
        url: $url,
        httpClient: $httpClient,
        httpResponseFactory: $responseFactory,
        httpStreamFactory: $responseFactory,
        httpRequestFactory: $responseFactory,
    );

    // Check that all methods exist
    $methods = [
        'check',
        'createAuthorizationModel',
        'createStore',
        'deleteStore',
        'dsl',
        'expand',
        'getAuthorizationModel',
        'getStore',
        'listAuthorizationModels',
        'listObjects',
        'listStores',
        'listTupleChanges',
        'listUsers',
        'readAssertions',
        'readTuples',
        'writeAssertions',
        'writeTuples',
        'assertLastRequest',
        'getLastRequest',
        'getLastResponse',
    ];

    foreach ($methods as $method) {
        expect(method_exists($client, $method))->toBeTrue("Method {$method} should exist");
    }
});

test('result pattern methods work correctly', function (): void {
    $responseFactory = new Nyholm\Psr7\Factory\Psr17Factory();
    $httpClient = new Buzz\Client\FileGetContents($responseFactory);
    $url = getenv('FGA_API_URL') ?: 'http://openfga:8080';

    $client = new Client(
        url: $url,
        httpClient: $httpClient,
        httpResponseFactory: $responseFactory,
        httpStreamFactory: $responseFactory,
        httpRequestFactory: $responseFactory,
    );

    // Test with a simple operation
    $result = $client->listStores();

    // Check result pattern methods
    expect(method_exists($result, 'succeeded'))->toBeTrue();
    expect(method_exists($result, 'failed'))->toBeTrue();
    expect(method_exists($result, 'success'))->toBeTrue();
    expect(method_exists($result, 'failure'))->toBeTrue();
    expect(method_exists($result, 'unwrap'))->toBeTrue();
    expect(method_exists($result, 'rethrow'))->toBeTrue();

    // Test that result is either success or failure
    expect($result->succeeded() || $result->failed())->toBeTrue();
    expect($result->succeeded() && $result->failed())->toBeFalse();
});
