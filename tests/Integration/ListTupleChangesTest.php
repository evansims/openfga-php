<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use Buzz\Client\FileGetContents;
use DateTimeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;
use OpenFGA\Models\Enums\{TupleOperation};

use function count;
use function OpenFGA\{tuple, tuples};

describe('List Tuple Changes', function (): void {
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

        $name = 'tuple-changes-test-' . bin2hex(random_bytes(5));
        $this->store = $this->client->createStore(name: $name)
            ->rethrow()
            ->unwrap();
        $this->storeId = $this->store->getId();
        $dsl = '
        model
          schema 1.1

        type user

        type group
          relations
            define member: [user]

        type document
          relations
            define owner: [user]
            define editor: [user, group#member] or owner
            define viewer: [user, group#member] or editor
    ';

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

    test('list changes after writes', function (): void {
        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:alice', 'owner', 'document:doc1'),
                tuple('user:bob', 'viewer', 'document:doc1'),
                tuple('user:charlie', 'editor', 'document:doc2'),
            ),
        )->rethrow()->unwrap();

        $result = $this->client->listTupleChanges(
            store: $this->storeId,
        )->rethrow()->unwrap();

        expect($result->getChanges())->not->toBeNull();
        expect($result->getChanges()->count())->toBeGreaterThanOrEqual(3);

        $changes = [];

        foreach ($result->getChanges() as $change) {
            $changes[] = $change;

            expect($change->getTimestamp())->toBeInstanceOf(DateTimeInterface::class);
            expect($change->getTupleKey())->not->toBeNull();
            expect($change->getOperation())->toBeIn([
                TupleOperation::TUPLE_OPERATION_WRITE,
                TupleOperation::TUPLE_OPERATION_DELETE,
            ]);
        }

        expect(count($changes))->toBeGreaterThanOrEqual(3);
    });

    test('list changes after deletes', function (): void {
        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:alice', 'owner', 'document:doc1'),
                tuple('user:bob', 'viewer', 'document:doc1'),
            ),
        )->rethrow()->unwrap();

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            deletes: tuples(
                tuple('user:bob', 'viewer', 'document:doc1'),
            ),
        )->rethrow()->unwrap();

        $result = $this->client->listTupleChanges(
            store: $this->storeId,
        )->rethrow()->unwrap();

        $deleteFound = false;

        foreach ($result->getChanges() as $change) {
            if (TupleOperation::TUPLE_OPERATION_DELETE === $change->getOperation()) {
                $deleteFound = true;
                $tupleKey = $change->getTupleKey();
                expect($tupleKey->getUser())->toBe('user:bob');
                expect($tupleKey->getRelation())->toBe('viewer');
                expect($tupleKey->getObject())->toBe('document:doc1');
            }
        }

        expect($deleteFound)->toBeTrue();
    });

    test('list changes with pagination', function (): void {
        $tuplesToWrite = [];

        for ($i = 0; 20 > $i; ++$i) {
            $tuplesToWrite[] = tuple("user:user{$i}", 'viewer', "document:doc{$i}");
        }

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(...$tuplesToWrite),
        )->rethrow()->unwrap();

        $firstPage = $this->client->listTupleChanges(
            store: $this->storeId,
            pageSize: 5,
        )->rethrow()->unwrap();

        expect($firstPage->getChanges()->count())->toBeLessThanOrEqual(5);

        $continuationToken = $firstPage->getContinuationToken();

        if ($continuationToken) {
            $secondPage = $this->client->listTupleChanges(
                store: $this->storeId,
                pageSize: 5,
                continuationToken: $continuationToken,
            )->rethrow()->unwrap();

            expect($secondPage->getChanges())->not->toBeNull();

            if (0 < $firstPage->getChanges()->count() && 0 < $secondPage->getChanges()->count()) {
                $firstPageChanges = [];

                foreach ($firstPage->getChanges() as $change) {
                    $firstPageChanges[] = $change;
                }
                $secondPageChanges = [];

                foreach ($secondPage->getChanges() as $change) {
                    $secondPageChanges[] = $change;
                }

                $firstPageLast = end($firstPageChanges);
                $secondPageFirst = reset($secondPageChanges);

                expect($secondPageFirst->getTimestamp()->getTimestamp())
                    ->toBeGreaterThanOrEqual($firstPageLast->getTimestamp()->getTimestamp());
            }
        }
    });

    test('list changes filtered by type', function (): void {
        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:alice', 'owner', 'document:doc1'),
                tuple('user:bob', 'member', 'group:engineering'),
                tuple('user:charlie', 'viewer', 'document:doc2'),
            ),
        )->rethrow()->unwrap();

        $result = $this->client->listTupleChanges(
            store: $this->storeId,
            type: 'document',
        )->rethrow()->unwrap();

        foreach ($result->getChanges() as $change) {
            $object = $change->getTupleKey()->getObject();
            expect($object)->toStartWith('document:');
        }
    });

    test('list changes chronological order', function (): void {
        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:alice', 'owner', 'document:first'),
            ),
        )->rethrow()->unwrap();

        usleep(100000); // 100ms

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:bob', 'owner', 'document:second'),
            ),
        )->rethrow()->unwrap();

        $result = $this->client->listTupleChanges(
            store: $this->storeId,
        )->rethrow()->unwrap();

        $timestamps = [];

        foreach ($result->getChanges() as $change) {
            $timestamps[] = $change->getTimestamp()->getTimestamp();
        }

        $sortedTimestamps = $timestamps;
        sort($sortedTimestamps);
        expect($timestamps)->toBe($sortedTimestamps);
    });

    test('list changes empty store', function (): void {
        $emptyStore = $this->client->createStore(
            name: 'empty-changes-test-' . bin2hex(random_bytes(5)),
        )->rethrow()->unwrap();

        try {
            $result = $this->client->listTupleChanges(
                store: $emptyStore->getId(),
            )->rethrow()->unwrap();

            expect($result->getChanges())->not->toBeNull();

            expect($result->getChanges()->count())->toBeGreaterThanOrEqual(0);
        } finally {
            $this->client->deleteStore(store: $emptyStore->getId());
        }
    });

    test('list changes with mixed operations', function (): void {
        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:alice', 'owner', 'document:doc1'),
                tuple('user:bob', 'viewer', 'document:doc1'),
                tuple('user:charlie', 'editor', 'document:doc1'),
            ),
        )->rethrow()->unwrap();

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:dave', 'viewer', 'document:doc1'),
            ),
            deletes: tuples(
                tuple('user:charlie', 'editor', 'document:doc1'),
            ),
        )->rethrow()->unwrap();

        $result = $this->client->listTupleChanges(
            store: $this->storeId,
        )->rethrow()->unwrap();

        $operations = [];

        foreach ($result->getChanges() as $change) {
            $operations[] = $change->getOperation();
        }

        expect($operations)->toContain(TupleOperation::TUPLE_OPERATION_WRITE);
        expect($operations)->toContain(TupleOperation::TUPLE_OPERATION_DELETE);
    });

    test('tuple change metadata', function (): void {
        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:alice', 'owner', 'document:important'),
            ),
        )->rethrow()->unwrap();

        $result = $this->client->listTupleChanges(
            store: $this->storeId,
        )->rethrow()->unwrap();

        $found = false;

        foreach ($result->getChanges() as $change) {
            $tupleKey = $change->getTupleKey();

            if ('user:alice' === $tupleKey->getUser()
                && 'document:important' === $tupleKey->getObject()) {
                $found = true;

                expect($change->getTimestamp())->toBeInstanceOf(DateTimeInterface::class);
                expect($change->getOperation())->toBe(TupleOperation::TUPLE_OPERATION_WRITE);
                expect($tupleKey->getUser())->toBe('user:alice');
                expect($tupleKey->getRelation())->toBe('owner');
                expect($tupleKey->getObject())->toBe('document:important');
            }
        }

        expect($found)->toBeTrue();
    });

    test('continuation token persistence', function (): void {
        $tuplesToWrite = [];

        for ($i = 0; 15 > $i; ++$i) {
            $tuplesToWrite[] = tuple("user:user{$i}", 'viewer', "document:doc{$i}");
        }

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(...$tuplesToWrite),
        )->rethrow()->unwrap();

        $firstResult = $this->client->listTupleChanges(
            store: $this->storeId,
            pageSize: 3,
        )->rethrow()->unwrap();

        $token1 = $firstResult->getContinuationToken();
        expect($token1)->not->toBeNull();

        $secondResult = $this->client->listTupleChanges(
            store: $this->storeId,
            pageSize: 3,
            continuationToken: $token1,
        )->rethrow()->unwrap();

        $token2 = $secondResult->getContinuationToken();

        $secondResultAgain = $this->client->listTupleChanges(
            store: $this->storeId,
            pageSize: 3,
            continuationToken: $token1,
        )->rethrow()->unwrap();

        expect($secondResultAgain->getChanges()->count())
            ->toBe($secondResult->getChanges()->count());
    });
});
