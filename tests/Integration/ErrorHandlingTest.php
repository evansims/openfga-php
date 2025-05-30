<?php

declare(strict_types=1);

use OpenFGA\Client;
use OpenFGA\Exceptions\NetworkException;

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
});

test('handles invalid store ID gracefully', function (): void {
    $invalidStoreId = 'invalid-store-id-' . bin2hex(random_bytes(8));

    $result = $this->client->getStore(store: $invalidStoreId);

    expect($result->failed())->toBeTrue();

    $result->failure(function ($error): void {
        expect($error)->toBeInstanceOf(NetworkException::class);
    });
});

test('handles invalid authorization model ID gracefully', function (): void {
    // First create a valid store
    $name = 'error-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    $invalidModelId = 'invalid-model-id-' . bin2hex(random_bytes(8));

    $result = $this->client->getAuthorizationModel(
        store: $store->getId(),
        model: $invalidModelId,
    );

    expect($result->failed())->toBeTrue();

    $result->failure(function ($error): void {
        expect($error)->toBeInstanceOf(NetworkException::class);
    });

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});

test('handles malformed authorization model', function (): void {
    // Create a valid store
    $name = 'malformed-model-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    // Try to create a malformed model with syntax error
    $malformedDsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user invalid syntax here
    ';

    $result = $this->client->dsl($malformedDsl);

    expect($result->failed())->toBeTrue();

    $result->failure(function ($error): void {
        expect($error)->toBeInstanceOf(RuntimeException::class);
    });

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});

test('handles invalid tuple writes', function (): void {
    // Create store and model
    $name = 'invalid-tuple-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    $createModelResponse = $this->client->createAuthorizationModel(
        store: $store->getId(),
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    // Try to write tuple with invalid relation
    $invalidTuples = tuples(
        tuple('user:alice', 'nonexistent_relation', 'document:test'), // This relation doesn't exist in the model
    );

    $result = $this->client->writeTuples(
        store: $store->getId(),
        model: $createModelResponse->getModel(),
        writes: $invalidTuples,
    );

    expect($result->failed())->toBeTrue();

    $result->failure(function ($error): void {
        expect($error)->toBeInstanceOf(NetworkException::class);
    });

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});

test('handles check with invalid user format', function (): void {
    // Create store and model
    $name = 'invalid-check-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    $createModelResponse = $this->client->createAuthorizationModel(
        store: $store->getId(),
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    // Try check with malformed user ID
    $result = $this->client->check(
        store: $store->getId(),
        model: $createModelResponse->getModel(),
        tupleKey: tuple('invalid-user-format', 'reader', 'document:test'), // Should be user:id format
    );

    expect($result->failed())->toBeTrue();

    $result->failure(function ($error): void {
        expect($error)->toBeInstanceOf(NetworkException::class);
    });

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});

test('handles network connectivity issues', function (): void {
    // Create client with invalid URL to simulate network issues
    $invalidClient = new Client(
        url: 'http://nonexistent-server:9999',
        httpClient: $this->httpClient,
        httpResponseFactory: $this->responseFactory,
        httpStreamFactory: $this->httpStreamFactory,
        httpRequestFactory: $this->httpRequestFactory,
    );

    // Set up custom error handler to suppress expected network warnings
    $previousHandler = set_error_handler(function ($errno, $errstr) {
        // Suppress warnings about network connectivity
        return (bool) (str_contains($errstr, 'php_network_getaddresses')
            || str_contains($errstr, 'getaddrinfo')
            || str_contains($errstr, 'Failed to open stream'));

        // Let other errors through
    }, E_WARNING);

    try {
        $result = $invalidClient->listStores();

        expect($result->failed())->toBeTrue();

        $result->failure(function ($error): void {
            expect($error)->toBeInstanceOf(NetworkException::class);
        });
    } finally {
        // Restore previous error handler
        restore_error_handler();
    }
});

test('handles concurrent tuple operations gracefully', function (): void {
    // Create store and model
    $name = 'concurrent-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    $createModelResponse = $this->client->createAuthorizationModel(
        store: $store->getId(),
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    $modelId = $createModelResponse->getModel();

    // Write a tuple
    $tuplesToWrite = tuples(
        tuple('user:alice', 'reader', 'document:concurrent'),
    );

    $this->client->writeTuples(
        store: $store->getId(),
        model: $modelId,
        writes: $tuplesToWrite,
    )->rethrow()->unwrap();

    // Try to delete the same tuple immediately (simulating potential race condition)
    $deleteResult = $this->client->writeTuples(
        store: $store->getId(),
        model: $modelId,
        deletes: $tuplesToWrite,
    );

    // This should succeed even if there might be timing issues
    expect($deleteResult->succeeded())->toBeTrue();

    // Verify tuple is gone
    $readResponse = $this->client->readTuples(
        store: $store->getId(),
        tupleKey: tuple('user:alice', 'reader', 'document:concurrent'),
    )->rethrow()->unwrap();

    expect($readResponse->getTuples()->count())->toBe(0);

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});

test('handles pagination correctly for large result sets', function (): void {
    // Create store and model
    $name = 'pagination-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    $createModelResponse = $this->client->createAuthorizationModel(
        store: $store->getId(),
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    $modelId = $createModelResponse->getModel();

    // Write many tuples
    $tuplesArray = [];
    for ($i = 0; $i < 25; ++$i) {
        $tuplesArray[] = tuple("user:user{$i}", 'reader', 'document:large-doc');
    }
    $tuplesToWrite = tuples(...$tuplesArray);

    $this->client->writeTuples(
        store: $store->getId(),
        model: $modelId,
        writes: $tuplesToWrite,
    )->rethrow()->unwrap();

    // Read with pagination
    $readResponse = $this->client->readTuples(
        store: $store->getId(),
        tupleKey: tuple('', '', 'document:large-doc'),
        pageSize: 10,
    )->rethrow()->unwrap();

    expect($readResponse->getTuples()->count())->toBeLessThanOrEqual(10);

    // If there's a continuation token, we should be able to get more results
    if ($readResponse->getContinuationToken()) {
        $nextPageResponse = $this->client->readTuples(
            store: $store->getId(),
            tupleKey: tuple('', '', 'document:large-doc'),
            pageSize: 10,
            continuationToken: $readResponse->getContinuationToken(),
        )->rethrow()->unwrap();

        expect($nextPageResponse->getTuples()->count())->toBeGreaterThan(0);
    }

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});

test('handles authentication errors with invalid credentials', function (): void {
    // Skip if not using authentication
    if (! getenv('FGA_CLIENT_ID')) {
        $this->markTestSkipped('Authentication tests require FGA_CLIENT_ID to be set');
    }

    // Create client with invalid CLIENT_CREDENTIALS authentication
    $invalidClient = new Client(
        url: $this->url,
        httpClient: $this->httpClient,
        httpResponseFactory: $this->responseFactory,
        httpStreamFactory: $this->httpStreamFactory,
        httpRequestFactory: $this->httpRequestFactory,
        authentication: OpenFGA\Authentication::CLIENT_CREDENTIALS,
        clientId: 'invalid-client-id',
        clientSecret: 'invalid-client-secret',
        audience: getenv('FGA_API_AUDIENCE') ?: 'test-audience',
        issuer: getenv('FGA_API_TOKEN_ISSUER') ?: 'https://test-issuer.example.com',
    );

    $result = $invalidClient->listStores();

    expect($result->failed())->toBeTrue();

    $result->failure(function ($error): void {
        // Authentication failure will manifest as a NetworkException
        expect($error)->toBeInstanceOf(NetworkException::class);
    });
});

test('handles empty object type error', function (): void {
    // Create store and model
    $name = 'empty-type-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    $createModelResponse = $this->client->createAuthorizationModel(
        store: $store->getId(),
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    // Try check with empty object
    $result = $this->client->check(
        store: $store->getId(),
        model: $createModelResponse->getModel(),
        tupleKey: tuple('user:alice', 'reader', ':doc1'), // Empty type
    );

    expect($result->failed())->toBeTrue();

    $result->failure(function ($error): void {
        expect($error)->toBeInstanceOf(NetworkException::class);
    });

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});

test('handles batch write limits', function (): void {
    // Create store and model
    $name = 'batch-limit-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    $createModelResponse = $this->client->createAuthorizationModel(
        store: $store->getId(),
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    // Create a batch larger than typical limits (usually 100)
    $largeBatch = [];
    for ($i = 0; $i < 150; ++$i) {
        $largeBatch[] = tuple("user:user{$i}", 'reader', "document:doc{$i}");
    }

    $result = $this->client->writeTuples(
        store: $store->getId(),
        model: $createModelResponse->getModel(),
        writes: tuples(...$largeBatch),
    );

    expect($result->failed())->toBeTrue();

    $result->failure(function ($error): void {
        expect($error)->toBeInstanceOf(NetworkException::class);
    });

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});

test('handles empty relation name error', function (): void {
    // Create store and model
    $name = 'empty-relation-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    $createModelResponse = $this->client->createAuthorizationModel(
        store: $store->getId(),
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    // Try check with empty relation
    $result = $this->client->check(
        store: $store->getId(),
        model: $createModelResponse->getModel(),
        tupleKey: tuple('user:alice', '', 'document:doc1'), // Empty relation
    );

    expect($result->failed())->toBeTrue();

    $result->failure(function ($error): void {
        expect($error)->toBeInstanceOf(NetworkException::class);
    });

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});

test('handles invalid continuation token', function (): void {
    // Create store
    $name = 'continuation-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    // Try to read tuples with invalid continuation token
    $result = $this->client->readTuples(
        store: $store->getId(),
        tupleKey: tuple('', '', ''),
        continuationToken: 'invalid-continuation-token-abc123',
    );

    expect($result->failed())->toBeTrue();

    $result->failure(function ($error): void {
        expect($error)->toBeInstanceOf(NetworkException::class);
    });

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});

test('handles conflicting tuple writes', function (): void {
    // Create store and model
    $name = 'conflict-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    $createModelResponse = $this->client->createAuthorizationModel(
        store: $store->getId(),
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    $modelId = $createModelResponse->getModel();

    // Try to write and delete the same tuple in one transaction
    $conflictTuple = tuple('user:alice', 'reader', 'document:conflict');

    $result = $this->client->writeTuples(
        store: $store->getId(),
        model: $modelId,
        writes: tuples($conflictTuple),
        deletes: tuples($conflictTuple),
    );

    // This might succeed or fail depending on OpenFGA's conflict resolution
    // But we should handle it gracefully either way
    if ($result->failed()) {
        $result->failure(function ($error): void {
            expect($error)->toBeInstanceOf(NetworkException::class);
        });
    } else {
        expect($result->succeeded())->toBeTrue();
    }

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});

test('handles expand on non-existent objects', function (): void {
    // Create store and model
    $name = 'expand-error-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    $createModelResponse = $this->client->createAuthorizationModel(
        store: $store->getId(),
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    // Expand on object that doesn't exist (but valid format)
    $result = $this->client->expand(
        store: $store->getId(),
        model: $createModelResponse->getModel(),
        tupleKey: tuple('', 'reader', 'document:nonexistent'),
    );

    // This should succeed but return empty tree
    expect($result->succeeded())->toBeTrue();

    $tree = $result->unwrap()->getTree();
    expect($tree)->not->toBeNull();

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});

test('handles listObjects with non-existent relation', function (): void {
    // Create store and model
    $name = 'list-objects-error-test-' . bin2hex(random_bytes(5));
    $store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();

    $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    $createModelResponse = $this->client->createAuthorizationModel(
        store: $store->getId(),
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    // List objects with non-existent relation
    $result = $this->client->listObjects(
        store: $store->getId(),
        model: $createModelResponse->getModel(),
        type: 'document',
        relation: 'nonexistent_relation',
        user: 'user:alice',
    );

    expect($result->failed())->toBeTrue();

    $result->failure(function ($error): void {
        expect($error)->toBeInstanceOf(NetworkException::class);
    });

    // Cleanup
    $this->client->deleteStore(store: $store->getId());
});
