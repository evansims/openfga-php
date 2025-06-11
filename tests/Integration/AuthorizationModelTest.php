<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;

describe('Authorization Model', function (): void {
    beforeEach(function (): void {
        $this->responseFactory = new Psr17Factory;
        $this->httpClient = new FileGetContents($this->responseFactory);
        $this->httpRequestFactory = $this->responseFactory;
        $this->httpStreamFactory = $this->responseFactory;
        $this->url = getOpenFgaUrl();

        $this->client = Client::create(
            url: $this->url,
            httpClient: $this->httpClient,
            httpResponseFactory: $this->responseFactory,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
        );

        $name = 'auth-model-test-' . bin2hex(random_bytes(5));
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

    test('creates and retrieves authorization model', function (): void {
        $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
            define writer: [user]
            define owner: [user]
    ';

        $model = $this->client->dsl($dsl)->rethrow()->unwrap();

        $createResponse = $this->client->createAuthorizationModel(
            store: $this->storeId,
            typeDefinitions: $model->getTypeDefinitions(),
        )->rethrow()->unwrap();

        expect($createResponse->getModel())->not()->toBe('');

        $modelId = $createResponse->getModel();

        $getResponse = $this->client->getAuthorizationModel(
            store: $this->storeId,
            model: $modelId,
        )->rethrow()->unwrap();

        $retrievedModel = $getResponse->getModel();
        expect($retrievedModel->getSchemaVersion()->value)->toBe('1.1');
        expect($retrievedModel->getTypeDefinitions())->toHaveCount(2);
    });

    test('lists authorization models', function (): void {
        $dsl1 = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
    ';

        $dsl2 = '
        model
          schema 1.1

        type user

        type folder
          relations
            define viewer: [user]
    ';

        $model1 = $this->client->dsl($dsl1)->rethrow()->unwrap();
        $model2 = $this->client->dsl($dsl2)->rethrow()->unwrap();

        $create1 = $this->client->createAuthorizationModel(
            store: $this->storeId,
            typeDefinitions: $model1->getTypeDefinitions(),
        )->rethrow()->unwrap();

        $create2 = $this->client->createAuthorizationModel(
            store: $this->storeId,
            typeDefinitions: $model2->getTypeDefinitions(),
        )->rethrow()->unwrap();

        $listResponse = $this->client->listAuthorizationModels(
            store: $this->storeId,
        )->rethrow()->unwrap();

        $models = $listResponse->getModels();
        expect($models->count())->toBeGreaterThanOrEqual(2);

        $modelIds = [];

        foreach ($models as $model) {
            $modelIds[] = $model->getId();
        }

        expect($modelIds)->toContain($create1->getModel());
        expect($modelIds)->toContain($create2->getModel());
    });

    test('complex authorization model with conditions', function (): void {
        $dsl = '
        model
          schema 1.1

        type user

        type organization
          relations
            define member: [user]

        type document
          relations
            define owner: [organization]
            define reader: [user] or member from owner
    ';

        $model = $this->client->dsl($dsl)->rethrow()->unwrap();

        $createResponse = $this->client->createAuthorizationModel(
            store: $this->storeId,
            typeDefinitions: $model->getTypeDefinitions(),
        )->rethrow()->unwrap();

        expect($createResponse->getModel())->not()->toBe('');

        $getResponse = $this->client->getAuthorizationModel(
            store: $this->storeId,
            model: $createResponse->getModel(),
        )->rethrow()->unwrap();

        $retrievedModel = $getResponse->getModel();
        expect($retrievedModel->getTypeDefinitions())->toHaveCount(3);

        $documentType = null;

        foreach ($retrievedModel->getTypeDefinitions() as $typeDef) {
            if ('document' === $typeDef->getType()) {
                $documentType = $typeDef;

                break;
            }
        }

        expect($documentType)->not()->toBeNull();
        expect($documentType->getRelations())->toHaveCount(2);
    });
});
