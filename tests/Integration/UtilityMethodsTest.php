<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use OpenFGA\{Authentication, Client};
use OpenFGA\Models\Enums\Consistency;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

use function in_array;
use function OpenFGA\{tuple, tuples};

describe('Utility Methods', function (): void {
    beforeEach(function (): void {
        $this->client = new Client(url: getOpenFgaUrl());

        $name = 'utility-test-' . bin2hex(random_bytes(5));
        $this->store = $this->client->createStore(name: $name)
            ->rethrow()
            ->unwrap();
        $this->storeId = $this->store->getId();
    });

    afterEach(function (): void {
        if (isset($this->storeId)) {
            $this->client->deleteStore(store: $this->storeId);
        }
    });

    test('getLastRequest captures request details', function (): void {
        $this->client->getStore(store: $this->storeId)->rethrow()->unwrap();

        $lastRequest = $this->client->getLastRequest();

        expect($lastRequest)->not->toBeNull();
        expect($lastRequest)->toBeInstanceOf(RequestInterface::class);

        expect($lastRequest->getMethod())->toBe('GET');
        expect($lastRequest->getUri()->getPath())->toContain("/stores/{$this->storeId}");

        $hasJsonHeader = $lastRequest->hasHeader('Accept')
                         && in_array('application/json', $lastRequest->getHeader('Accept'), true)
                         || $lastRequest->hasHeader('Content-Type')
                         && str_contains($lastRequest->getHeader('Content-Type')[0], 'application/json');
        expect($hasJsonHeader)->toBeTrue();
    });

    test('getLastResponse captures response details', function (): void {
        $this->client->getStore(store: $this->storeId)->rethrow()->unwrap();

        $lastResponse = $this->client->getLastResponse();

        expect($lastResponse)->not->toBeNull();
        expect($lastResponse)->toBeInstanceOf(ResponseInterface::class);

        expect($lastResponse->getStatusCode())->toBe(200);
        expect($lastResponse->getHeader('Content-Type'))->toContain('application/json');

        $body = (string) $lastResponse->getBody();
        expect($body)->toContain($this->storeId);
        expect($body)->toContain('"id"');
        expect($body)->toContain('"name"');
    });

    test('request and response are updated with each call', function (): void {
        $this->client->getStore(store: $this->storeId)->rethrow()->unwrap();

        $firstRequest = $this->client->getLastRequest();
        $firstResponse = $this->client->getLastResponse();

        expect($firstRequest->getUri()->getPath())->toContain("/stores/{$this->storeId}");
        expect($firstResponse->getStatusCode())->toBe(200);

        $this->client->listStores()->rethrow()->unwrap();

        $secondRequest = $this->client->getLastRequest();
        $secondResponse = $this->client->getLastResponse();

        expect($secondRequest)->not->toBe($firstRequest);
        expect($secondRequest->getUri()->getPath())->toContain('/stores');
        expect($secondRequest->getUri()->getPath())->not->toContain($this->storeId);

        expect($secondResponse)->not->toBe($firstResponse);
    });

    test('request contains authorization headers when configured', function (): void {
        $token = 'Bearer test-token-' . uniqid();

        $authClient = new Client(
            url: getOpenFgaUrl(),
            authentication: new Authentication\TokenAuthentication($token),
        );

        $authClient->listStores();

        $lastRequest = $authClient->getLastRequest();
        expect($lastRequest->hasHeader('Authorization'))->toBeTrue();
        expect($lastRequest->getHeader('Authorization')[0])->toBe($token);
    });

    test('POST request body is captured', function (): void {
        $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define viewer: [user]
    ';

        $model = $this->client->dsl($dsl)->rethrow()->unwrap();

        $this->client->createAuthorizationModel(
            store: $this->storeId,
            typeDefinitions: $model->getTypeDefinitions(),
        )->rethrow()->unwrap();

        $lastRequest = $this->client->getLastRequest();

        expect($lastRequest->getMethod())->toBe('POST');
        expect($lastRequest->getUri()->getPath())->toContain("/stores/{$this->storeId}/authorization-models");

        $body = (string) $lastRequest->getBody();
        expect($body)->toContain('type_definitions');
        expect($body)->toContain('user');
        expect($body)->toContain('document');
        expect($body)->toContain('viewer');
    });

    test('failed requests still capture request and response', function (): void {
        $invalidStoreId = 'invalid-store-id-123';
        $result = $this->client->getStore(store: $invalidStoreId);

        expect($result->failed())->toBeTrue();

        $lastRequest = $this->client->getLastRequest();
        expect($lastRequest)->not->toBeNull();
        expect($lastRequest->getUri()->getPath())->toContain($invalidStoreId);

        $lastResponse = $this->client->getLastResponse();
        expect($lastResponse)->not->toBeNull();
        expect($lastResponse->getStatusCode())->toBeGreaterThanOrEqual(400);
    });

    test('request headers are preserved', function (): void {
        $this->client->check(
            store: $this->storeId,
            model: 'latest',
            tuple: tuple('user:alice', 'viewer', 'document:doc1'),
            consistency: Consistency::HIGHER_CONSISTENCY,
        );

        $lastRequest = $this->client->getLastRequest();

        expect($lastRequest->hasHeader('Content-Type'))->toBeTrue();
        expect($lastRequest->getHeader('Content-Type'))->toContain('application/json');

        $body = (string) $lastRequest->getBody();
        expect($body)->toContain('consistency');
        expect($body)->toContain('HIGHER_CONSISTENCY');
    });

    test('response body can be read multiple times', function (): void {
        $this->client->getStore(store: $this->storeId)->rethrow()->unwrap();

        $lastResponse = $this->client->getLastResponse();

        $body1 = (string) $lastResponse->getBody();
        $body2 = (string) $lastResponse->getBody();

        expect($body1)->toBe($body2);
        expect($body1)->toContain($this->storeId);
    });

    test('null returned when no requests made', function (): void {
        $freshClient = new Client(
            url: getOpenFgaUrl(),
        );

        expect($freshClient->getLastRequest())->toBeNull();
        expect($freshClient->getLastResponse())->toBeNull();
    });

    test('captures request and response for batch operations', function (): void {
        $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define viewer: [user]
    ';

        $model = $this->client->dsl($dsl)->rethrow()->unwrap();
        $modelResponse = $this->client->createAuthorizationModel(
            store: $this->storeId,
            typeDefinitions: $model->getTypeDefinitions(),
        )->rethrow()->unwrap();

        $this->client->writeTuples(
            store: $this->storeId,
            model: $modelResponse->getModel(),
            writes: tuples(
                tuple('user:alice', 'viewer', 'document:doc1'),
                tuple('user:bob', 'viewer', 'document:doc2'),
                tuple('user:charlie', 'viewer', 'document:doc3'),
            ),
        )->rethrow()->unwrap();

        $lastRequest = $this->client->getLastRequest();
        $lastResponse = $this->client->getLastResponse();

        expect($lastRequest->getMethod())->toBe('POST');
        expect($lastRequest->getUri()->getPath())->toContain('/write');

        $body = (string) $lastRequest->getBody();
        expect($body)->toContain('user:alice');
        expect($body)->toContain('user:bob');
        expect($body)->toContain('user:charlie');

        expect($lastResponse->getStatusCode())->toBe(200);
    });
});
