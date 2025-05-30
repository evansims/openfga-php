<?php

declare(strict_types=1);

use OpenFGA\{Authentication, Client};

use function OpenFGA\Models\{tuple, tuples};

beforeEach(function (): void {
    $this->responseFactory = new Nyholm\Psr7\Factory\Psr17Factory();
    $this->httpClient = new Buzz\Client\FileGetContents($this->responseFactory);
    $this->httpRequestFactory = $this->responseFactory;
    $this->httpStreamFactory = $this->responseFactory;
    $this->url = getenv('FGA_API_URL') ?: 'http://openfga:8080';

    $this->client = new Client(
        url: $this->url,
        httpClient: $this->httpClient,
        httpResponseFactory: $this->responseFactory,
        httpStreamFactory: $this->httpStreamFactory,
        httpRequestFactory: $this->httpRequestFactory,
    );

    // Create a test store
    $name = 'utility-test-' . bin2hex(random_bytes(5));
    $this->store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();
    $this->storeId = $this->store->getId();
});

afterEach(function (): void {
    // Clean up test store
    if (isset($this->storeId)) {
        $this->client->deleteStore(store: $this->storeId);
    }
});

test('getLastRequest captures request details', function (): void {
    // Make a request
    $this->client->getStore(store: $this->storeId)->rethrow()->unwrap();

    // Get the last request
    $lastRequest = $this->client->getLastRequest();

    expect($lastRequest)->not->toBeNull();
    expect($lastRequest)->toBeInstanceOf(Psr\Http\Message\RequestInterface::class);

    // Verify request details
    expect($lastRequest->getMethod())->toBe('GET');
    expect($lastRequest->getUri()->getPath())->toContain("/stores/{$this->storeId}");

    // Check for either Accept or Content-Type header
    $hasJsonHeader = $lastRequest->hasHeader('Accept')
                     && \in_array('application/json', $lastRequest->getHeader('Accept'), true)
                     || $lastRequest->hasHeader('Content-Type')
                     && str_contains($lastRequest->getHeader('Content-Type')[0], 'application/json');
    expect($hasJsonHeader)->toBeTrue();
});

test('getLastResponse captures response details', function (): void {
    // Make a request
    $this->client->getStore(store: $this->storeId)->rethrow()->unwrap();

    // Get the last response
    $lastResponse = $this->client->getLastResponse();

    expect($lastResponse)->not->toBeNull();
    expect($lastResponse)->toBeInstanceOf(Psr\Http\Message\ResponseInterface::class);

    // Verify response details
    expect($lastResponse->getStatusCode())->toBe(200);
    expect($lastResponse->getHeader('Content-Type'))->toContain('application/json');

    // Body should contain store data
    $body = (string) $lastResponse->getBody();
    expect($body)->toContain($this->storeId);
    expect($body)->toContain('"id"');
    expect($body)->toContain('"name"');
});

test('request and response are updated with each call', function (): void {
    // First request - getStore
    $this->client->getStore(store: $this->storeId)->rethrow()->unwrap();

    $firstRequest = $this->client->getLastRequest();
    $firstResponse = $this->client->getLastResponse();

    expect($firstRequest->getUri()->getPath())->toContain("/stores/{$this->storeId}");
    expect($firstResponse->getStatusCode())->toBe(200);

    // Second request - listStores
    $this->client->listStores()->rethrow()->unwrap();

    $secondRequest = $this->client->getLastRequest();
    $secondResponse = $this->client->getLastResponse();

    // Should be different requests
    expect($secondRequest)->not->toBe($firstRequest);
    expect($secondRequest->getUri()->getPath())->toContain('/stores');
    expect($secondRequest->getUri()->getPath())->not->toContain($this->storeId);

    // Response should also be different
    expect($secondResponse)->not->toBe($firstResponse);
});

test('request contains authorization headers when configured', function (): void {
    // Create a pre-made access token
    $token = 'Bearer test-token-' . uniqid();

    // Create client with TOKEN authentication mode for pre-shared tokens
    $authClient = new Client(
        url: $this->url,
        authentication: Authentication::TOKEN,
        token: $token,
        httpClient: $this->httpClient,
        httpResponseFactory: $this->responseFactory,
        httpStreamFactory: $this->httpStreamFactory,
        httpRequestFactory: $this->httpRequestFactory,
    );

    // Make a request
    $authClient->listStores();

    $lastRequest = $authClient->getLastRequest();
    expect($lastRequest->hasHeader('Authorization'))->toBeTrue();
    expect($lastRequest->getHeader('Authorization')[0])->toBe($token);
});

test('POST request body is captured', function (): void {
    // Create a simple model
    $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define viewer: [user]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    // Create authorization model (POST request)
    $this->client->createAuthorizationModel(
        store: $this->storeId,
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    $lastRequest = $this->client->getLastRequest();

    expect($lastRequest->getMethod())->toBe('POST');
    expect($lastRequest->getUri()->getPath())->toContain("/stores/{$this->storeId}/authorization-models");

    // Body should contain the model definition
    $body = (string) $lastRequest->getBody();
    expect($body)->toContain('type_definitions');
    expect($body)->toContain('user');
    expect($body)->toContain('document');
    expect($body)->toContain('viewer');
});

test('failed requests still capture request and response', function (): void {
    // Make a request that will fail (invalid store ID)
    $invalidStoreId = 'invalid-store-id-123';
    $result = $this->client->getStore(store: $invalidStoreId);

    expect($result->failed())->toBeTrue();

    // Should still have captured the request
    $lastRequest = $this->client->getLastRequest();
    expect($lastRequest)->not->toBeNull();
    expect($lastRequest->getUri()->getPath())->toContain($invalidStoreId);

    // Should have captured the error response
    $lastResponse = $this->client->getLastResponse();
    expect($lastResponse)->not->toBeNull();
    expect($lastResponse->getStatusCode())->toBeGreaterThanOrEqual(400);
});

test('request headers are preserved', function (): void {
    // Make a request with custom consistency
    $this->client->check(
        store: $this->storeId,
        model: 'latest',
        tupleKey: tuple('user:alice', 'viewer', 'document:doc1'),
        consistency: OpenFGA\Models\Enums\Consistency::HIGHER_CONSISTENCY,
    );

    $lastRequest = $this->client->getLastRequest();

    // Should have proper headers
    expect($lastRequest->hasHeader('Content-Type'))->toBeTrue();
    expect($lastRequest->getHeader('Content-Type'))->toContain('application/json');

    // Check request body contains consistency
    $body = (string) $lastRequest->getBody();
    expect($body)->toContain('consistency');
    expect($body)->toContain('HIGHER_CONSISTENCY');
});

test('response body can be read multiple times', function (): void {
    // Make a request
    $this->client->getStore(store: $this->storeId)->rethrow()->unwrap();

    $lastResponse = $this->client->getLastResponse();

    // Read body multiple times
    $body1 = (string) $lastResponse->getBody();
    $body2 = (string) $lastResponse->getBody();

    // Should get same content
    expect($body1)->toBe($body2);
    expect($body1)->toContain($this->storeId);
});

test('null returned when no requests made', function (): void {
    // Create a fresh client
    $freshClient = new Client(
        url: $this->url,
        httpClient: $this->httpClient,
        httpResponseFactory: $this->responseFactory,
        httpStreamFactory: $this->httpStreamFactory,
        httpRequestFactory: $this->httpRequestFactory,
    );

    // Should return null before any requests
    expect($freshClient->getLastRequest())->toBeNull();
    expect($freshClient->getLastResponse())->toBeNull();
});

test('captures request and response for batch operations', function (): void {
    // Create a model first
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

    // Write multiple tuples
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

    // Request should be a write
    expect($lastRequest->getMethod())->toBe('POST');
    expect($lastRequest->getUri()->getPath())->toContain('/write');

    // Body should contain all tuples
    $body = (string) $lastRequest->getBody();
    expect($body)->toContain('user:alice');
    expect($body)->toContain('user:bob');
    expect($body)->toContain('user:charlie');

    // Response should be successful
    expect($lastResponse->getStatusCode())->toBe(200);
});
