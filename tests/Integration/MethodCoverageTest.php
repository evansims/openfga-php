<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;

describe('Method Coverage', function (): void {
    test('all client methods exist and are callable', function (): void {
        $responseFactory = new Psr17Factory;
        $httpClient = new FileGetContents($responseFactory);
        $url = getOpenFgaUrl();

        $client = new Client(
            url: $url,
            httpClient: $httpClient,
            httpResponseFactory: $responseFactory,
            httpStreamFactory: $responseFactory,
            httpRequestFactory: $responseFactory,
        );

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

    test('result pattern methods', function (): void {
        $responseFactory = new Psr17Factory;
        $httpClient = new FileGetContents($responseFactory);
        $url = getOpenFgaUrl();

        $client = new Client(
            url: $url,
            httpClient: $httpClient,
            httpResponseFactory: $responseFactory,
            httpStreamFactory: $responseFactory,
            httpRequestFactory: $responseFactory,
        );

        $result = $client->listStores();

        expect(method_exists($result, 'succeeded'))->toBeTrue();
        expect(method_exists($result, 'failed'))->toBeTrue();
        expect(method_exists($result, 'success'))->toBeTrue();
        expect(method_exists($result, 'failure'))->toBeTrue();
        expect(method_exists($result, 'unwrap'))->toBeTrue();
        expect(method_exists($result, 'rethrow'))->toBeTrue();

        expect($result->succeeded() || $result->failed())->toBeTrue();
        expect($result->succeeded() && $result->failed())->toBeFalse();
    });
});
