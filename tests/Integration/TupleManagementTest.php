<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;
use OpenFGA\Models\Enums\Consistency;

use function OpenFGA\{tuple, tuples};
use function sprintf;

describe('Tuple Management', function (): void {
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

        $name = 'tuple-test-' . bin2hex(random_bytes(5));
        $this->store = $this->client->createStore(name: $name)
            ->rethrow()
            ->unwrap();
        $this->storeId = $this->store->getId();
        $dsl = <<<'DSL'
            model
              schema 1.1
            type user
            type organization
              relations
                define member: [user]
                define admin: [user]
            type document
              relations
                define reader: [user]
                define writer: [user]
                define owner: [user]
            DSL;

        $model = $this->client->dsl($dsl)->rethrow()->unwrap();
        $createModelResponse = $this->client->createAuthorizationModel(
            store: $this->storeId,
            typeDefinitions: $model->getTypeDefinitions(),
        )->rethrow()->unwrap();

        $this->modelId = $createModelResponse->getModel();
    });

    afterEach(function (): void {
        if (isset($this->storeId)) {
            $this->client->deleteStore(store: $this->storeId);
        }
    });

    test('writes and reads relationship tuples', function (): void {
        $tuplesToWrite = tuples(
            tuple('user:alice', 'owner', 'document:readme'),
            tuple('user:bob', 'reader', 'document:readme'),
            tuple('user:charlie', 'writer', 'document:spec'),
        );

        $writeResponse = $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $tuplesToWrite,
        )->rethrow()->unwrap();

        expect($writeResponse)->not()->toBeNull();

        $readResponse1 = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('', '', 'document:readme'),
            consistency: Consistency::HIGHER_CONSISTENCY,
        )->rethrow()->unwrap();

        expect($readResponse1->getTuples()->count())->toBe(2); // alice as owner, bob as reader

        $readResponse2 = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('', '', 'document:spec'),
        )->rethrow()->unwrap();

        expect($readResponse2->getTuples()->count())->toBe(1); // charlie as writer

        $tupleStrings1 = [];

        foreach ($readResponse1->getTuples() as $tuple) {
            $key = $tuple->getKey();
            $tupleStrings1[] = sprintf(
                '%s#%s@%s',
                $key->getObject(),
                $key->getRelation(),
                $key->getUser(),
            );
        }

        expect($tupleStrings1)->toContain('document:readme#owner@user:alice');
        expect($tupleStrings1)->toContain('document:readme#reader@user:bob');

        $tupleStrings2 = [];

        foreach ($readResponse2->getTuples() as $tuple) {
            $key = $tuple->getKey();
            $tupleStrings2[] = sprintf(
                '%s#%s@%s',
                $key->getObject(),
                $key->getRelation(),
                $key->getUser(),
            );
        }

        expect($tupleStrings2)->toContain('document:spec#writer@user:charlie');
    });

    test('deletes relationship tuples', function (): void {
        $tuplesToWrite = tuples(
            tuple('user:alice', 'owner', 'document:test'),
            tuple('user:bob', 'reader', 'document:test'),
        );

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $tuplesToWrite,
        )->rethrow()->unwrap();

        $readResponse = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('', '', 'document:test'),
        )->rethrow()->unwrap();

        expect($readResponse->getTuples()->count())->toBe(2);

        $deleteResponse = $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            deletes: tuples(tuple('user:bob', 'reader', 'document:test')),
        )->rethrow()->unwrap();

        expect($deleteResponse)->not()->toBeNull();

        $readAfterDelete = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('', '', 'document:test'),
        )->rethrow()->unwrap();

        expect($readAfterDelete->getTuples()->count())->toBe(1);

        $remainingTuple = $readAfterDelete->getTuples()->first();
        $key = $remainingTuple->getKey();
        expect($key->getUser())->toBe('user:alice');
        expect($key->getRelation())->toBe('owner');
        expect($key->getObject())->toBe('document:test');
    });

    test('reads tuples with filters', function (): void {
        $tuplesToWrite = tuples(
            tuple('user:alice', 'owner', 'document:doc1'),
            tuple('user:alice', 'reader', 'document:doc2'),
            tuple('user:bob', 'reader', 'document:doc1'),
            tuple('user:bob', 'writer', 'document:doc2'),
            tuple('user:charlie', 'reader', 'document:doc3'),
        );

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $tuplesToWrite,
        )->rethrow()->unwrap();

        $readByObject = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('', '', 'document:doc1'),
        )->rethrow()->unwrap();

        expect($readByObject->getTuples()->count())->toBe(2); // alice as owner, bob as reader

        $readByUserAndObject = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('user:alice', '', 'document:doc1'),
        )->rethrow()->unwrap();

        expect($readByUserAndObject->getTuples()->count())->toBe(1); // alice as owner

        $readByRelationAndObject = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('', 'reader', 'document:doc1'),
        )->rethrow()->unwrap();

        expect($readByRelationAndObject->getTuples()->count())->toBe(1); // bob as reader

        $readByAllFilters = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('user:bob', 'writer', 'document:doc2'),
        )->rethrow()->unwrap();

        expect($readByAllFilters->getTuples()->count())->toBe(1); // exact match
    });

    test('tuple changes over time', function (): void {
        $initialTuples = tuples(
            tuple('user:alice', 'owner', 'document:timeline'),
            tuple('user:bob', 'reader', 'document:timeline'),
        );

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $initialTuples,
        )->rethrow()->unwrap();

        $changesResponse = $this->client->listTupleChanges(
            store: $this->storeId,
        )->rethrow()->unwrap();

        $changes = $changesResponse->getChanges();
        expect($changes->count())->toBeGreaterThanOrEqual(2);

        $operations = [];

        foreach ($changes as $change) {
            $operations[] = $change->getOperation()->value;
        }

        expect($operations)->toContain('TUPLE_OPERATION_WRITE');

        $moreTuples = tuples(
            tuple('user:charlie', 'writer', 'document:timeline'),
        );

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $moreTuples,
        )->rethrow()->unwrap();

        $updatedChanges = $this->client->listTupleChanges(
            store: $this->storeId,
        )->rethrow()->unwrap();

        expect($updatedChanges->getChanges()->count())->toBeGreaterThan($changes->count());
    });
});
