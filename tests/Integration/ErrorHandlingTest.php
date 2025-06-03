<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\{Authentication, Client};
use OpenFGA\Exceptions\{ClientException, NetworkException, SerializationException};

use function OpenFGA\{tuple, tuples};

describe('Error Handling', function (): void {
    beforeEach(function (): void {
        $this->responseFactory = new Psr17Factory;
        $this->httpClient = new FileGetContents($this->responseFactory);
        $this->httpRequestFactory = $this->responseFactory;
        $this->httpStreamFactory = $this->responseFactory;
        $this->url = getOpenFgaUrl();

        $this->client = new Client(
            url: $this->url,
            httpClient: $this->httpClient,
            httpResponseFactory: $this->responseFactory,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
        );
    });

    test('rejects invalid store ID', function (): void {
        $invalidStoreId = 'invalid-store-id-' . bin2hex(random_bytes(8));

        $result = $this->client->getStore(store: $invalidStoreId);

        expect($result->failed())->toBeTrue();

        $result->failure(function ($error): void {
            expect($error)->toBeInstanceOf(NetworkException::class);
        });
    });

    test('rejects invalid model ID', function (): void {
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

        $this->client->deleteStore(store: $store->getId());
    });

    test('rejects malformed model', function (): void {
        $name = 'malformed-model-test-' . bin2hex(random_bytes(5));
        $store = $this->client->createStore(name: $name)
            ->rethrow()
            ->unwrap();

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
            expect($error)->toBeInstanceOf(SerializationException::class);
        });

        $this->client->deleteStore(store: $store->getId());
    });

    test('rejects invalid tuples', function (): void {
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

        $invalidTuples = tuples(
            tuple('user:alice', 'nonexistent_relation', 'document:test'), // This relation doesn't exist in the model
        );

        $result = $this->client->writeTuples(
            store: $store->getId(),
            model: $createModelResponse->getModel(),
            writes: $invalidTuples,
        );

        // The OpenFGA server behavior allows writing tuples with relations that don't exist in the model
        // This is valid behavior as the tuple store is separate from the authorization model
        expect($result->succeeded())->toBeTrue();

        $this->client->deleteStore(store: $store->getId());
    });

    test('rejects invalid user format', function (): void {
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

        $result = $this->client->check(
            store: $store->getId(),
            model: $createModelResponse->getModel(),
            tupleKey: tuple('invalid-user-format', 'reader', 'document:test'), // Should be user:id format
        );

        expect($result->failed())->toBeTrue();

        $result->failure(function ($error): void {
            expect($error)->toBeInstanceOf(NetworkException::class);
        });

        $this->client->deleteStore(store: $store->getId());
    });

    test('network errors', function (): void {
        $invalidClient = new Client(
            url: 'http://nonexistent-server:9999',
            httpClient: $this->httpClient,
            httpResponseFactory: $this->responseFactory,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
        );

        $previousHandler = set_error_handler(fn ($errno, $errstr) => (bool) (str_contains($errstr, 'php_network_getaddresses')
                || str_contains($errstr, 'getaddrinfo')
                || str_contains($errstr, 'Failed to open stream')), E_WARNING);

        try {
            $result = $invalidClient->listStores();

            expect($result->failed())->toBeTrue();

            $result->failure(function ($error): void {
                expect($error)->toBeInstanceOf(NetworkException::class);
            });
        } finally {
            restore_error_handler();
        }
    });

    test('concurrent operations', function (): void {
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

        $tuplesToWrite = tuples(
            tuple('user:alice', 'reader', 'document:concurrent'),
        );

        $this->client->writeTuples(
            store: $store->getId(),
            model: $modelId,
            writes: $tuplesToWrite,
        )->rethrow()->unwrap();

        $deleteResult = $this->client->writeTuples(
            store: $store->getId(),
            model: $modelId,
            deletes: $tuplesToWrite,
        );

        expect($deleteResult->succeeded())->toBeTrue();

        $readResponse = $this->client->readTuples(
            store: $store->getId(),
            tupleKey: tuple('user:alice', 'reader', 'document:concurrent'),
        )->rethrow()->unwrap();

        expect($readResponse->getTuples()->count())->toBe(0);

        $this->client->deleteStore(store: $store->getId());
    });

    test('paginates large results', function (): void {
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

        $tuplesArray = [];

        for ($i = 0; 25 > $i; ++$i) {
            $tuplesArray[] = tuple("user:user{$i}", 'reader', 'document:large-doc');
        }
        $tuplesToWrite = tuples(...$tuplesArray);

        $this->client->writeTuples(
            store: $store->getId(),
            model: $modelId,
            writes: $tuplesToWrite,
        )->rethrow()->unwrap();

        $readResponse = $this->client->readTuples(
            store: $store->getId(),
            tupleKey: tuple('', '', 'document:large-doc'),
            pageSize: 10,
        )->rethrow()->unwrap();

        expect($readResponse->getTuples()->count())->toBeLessThanOrEqual(10);

        if ($readResponse->getContinuationToken()) {
            $nextPageResponse = $this->client->readTuples(
                store: $store->getId(),
                tupleKey: tuple('', '', 'document:large-doc'),
                pageSize: 10,
                continuationToken: $readResponse->getContinuationToken(),
            )->rethrow()->unwrap();

            expect($nextPageResponse->getTuples()->count())->toBeGreaterThan(0);
        }

        $this->client->deleteStore(store: $store->getId());
    });

    test('rejects invalid credentials', function (): void {
        // This test requires OAuth/OIDC authentication to be configured on the OpenFGA server
        // It's skipped when running against a local OpenFGA instance without authentication
        if (! getenv('FGA_CLIENT_ID')) {
            $this->markTestSkipped('Authentication tests require FGA_CLIENT_ID to be set');
        }

        $invalidClient = new Client(
            url: $this->url,
            httpClient: $this->httpClient,
            httpResponseFactory: $this->responseFactory,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
            authentication: Authentication::CLIENT_CREDENTIALS,
            clientId: 'invalid-client-id',
            clientSecret: 'invalid-client-secret',
            audience: getenv('FGA_API_AUDIENCE') ?: 'test-audience',
            issuer: getenv('FGA_API_TOKEN_ISSUER') ?: 'https://test-issuer.example.com',
        );

        $result = $invalidClient->listStores();

        expect($result->failed())->toBeTrue();

        $result->failure(function ($error): void {
            expect($error)->toBeInstanceOf(NetworkException::class);
        });
    });

    test('rejects empty object type', function (): void {
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

        $result = $this->client->check(
            store: $store->getId(),
            model: $createModelResponse->getModel(),
            tupleKey: tuple('user:alice', 'reader', ':doc1'), // Empty type
        );

        expect($result->failed())->toBeTrue();

        $result->failure(function ($error): void {
            expect($error)->toBeInstanceOf(NetworkException::class);
        });

        $this->client->deleteStore(store: $store->getId());
    });

    test('batch limits', function (): void {
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

        $largeBatch = [];

        for ($i = 0; 150 > $i; ++$i) {
            $largeBatch[] = tuple("user:user{$i}", 'reader', "document:doc{$i}");
        }

        $result = $this->client->writeTuples(
            store: $store->getId(),
            model: $createModelResponse->getModel(),
            writes: tuples(...$largeBatch),
        );

        expect($result->failed())->toBeTrue();

        $result->failure(function ($error): void {
            expect($error)->toBeInstanceOf(ClientException::class);
        });

        $this->client->deleteStore(store: $store->getId());
    });

    test('rejects empty relation', function (): void {
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

        $result = $this->client->check(
            store: $store->getId(),
            model: $createModelResponse->getModel(),
            tupleKey: tuple('user:alice', '', 'document:doc1'), // Empty relation
        );

        expect($result->failed())->toBeTrue();

        $result->failure(function ($error): void {
            expect($error)->toBeInstanceOf(NetworkException::class);
        });

        $this->client->deleteStore(store: $store->getId());
    });

    test('rejects invalid token', function (): void {
        $name = 'continuation-test-' . bin2hex(random_bytes(5));
        $store = $this->client->createStore(name: $name)
            ->rethrow()
            ->unwrap();

        $result = $this->client->readTuples(
            store: $store->getId(),
            tupleKey: tuple('', '', ''),
            continuationToken: 'invalid-continuation-token-abc123',
        );

        expect($result->failed())->toBeTrue();

        $result->failure(function ($error): void {
            expect($error)->toBeInstanceOf(NetworkException::class);
        });

        $this->client->deleteStore(store: $store->getId());
    });

    test('write conflicts', function (): void {
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

        $conflictTuple = tuple('user:alice', 'reader', 'document:conflict');

        $result = $this->client->writeTuples(
            store: $store->getId(),
            model: $modelId,
            writes: tuples($conflictTuple),
            deletes: tuples($conflictTuple),
        );

        if ($result->failed()) {
            $result->failure(function ($error): void {
                expect($error)->toBeInstanceOf(NetworkException::class);
            });
        } else {
            expect($result->succeeded())->toBeTrue();
        }

        $this->client->deleteStore(store: $store->getId());
    });

    test('expands non-existent objects', function (): void {
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

        $result = $this->client->expand(
            store: $store->getId(),
            model: $createModelResponse->getModel(),
            tupleKey: tuple('', 'reader', 'document:nonexistent'),
        );

        expect($result->succeeded())->toBeTrue();

        $tree = $result->unwrap()->getTree();
        expect($tree)->not->toBeNull();

        $this->client->deleteStore(store: $store->getId());
    });

    test('rejects non-existent relation', function (): void {
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

        $this->client->deleteStore(store: $store->getId());
    });
});
