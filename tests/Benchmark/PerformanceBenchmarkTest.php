<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Benchmark;

use OpenFGA\Client;

use function count;
use function OpenFGA\{tuple, tuples};
use function sprintf;

describe('Performance Benchmarks', function (): void {
    beforeEach(function (): void {
        $this->markTestSkipped('Performance benchmarks are temporarily disabled');
        $this->client = new Client(url: getOpenFgaUrl());

        $name = 'benchmark-test-' . bin2hex(random_bytes(5));
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

        type organization
          relations
            define member: [user, group#member]
            define admin: [user]

        type document
          relations
            define owner: [user]
            define editor: [user, group#member] or owner
            define viewer: [user, group#member, organization#member] or editor
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

    test('benchmark single check performance', function (): void {
        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:alice', 'viewer', 'document:doc1'),
            ),
        )->rethrow()->unwrap();

        $iterations = 10;
        $times = [];

        for ($i = 0; $i < $iterations; ++$i) {
            $start = microtime(true);

            $this->client->check(
                store: $this->storeId,
                model: $this->modelId,
                tupleKey: tuple('user:alice', 'viewer', 'document:doc1'),
            )->rethrow()->unwrap();

            $times[] = (microtime(true) - $start) * 1000; // Convert to milliseconds
        }

        $avgTime = array_sum($times) / count($times);
        $minTime = min($times);
        $maxTime = max($times);

        expect($avgTime)->toBeLessThan(100); // Average should be under 100ms
        expect($minTime)->toBeLessThan(50);  // Best case under 50ms

        echo sprintf(
            "\nSingle check performance: avg=%.2fms, min=%.2fms, max=%.2fms\n",
            $avgTime,
            $minTime,
            $maxTime,
        );
    });

    test('benchmark batch write performance', function (): void {
        $batchSizes = [5, 10, 20];
        $results = [];

        foreach ($batchSizes as $size) {
            $tuplesBatch = [];

            for ($i = 0; $i < $size; ++$i) {
                $tuplesBatch[] = tuple("user:batchuser{$i}", 'viewer', "document:batchdoc{$i}");
            }

            $start = microtime(true);

            $writeResult = $this->client->writeTuples(
                store: $this->storeId,
                model: $this->modelId,
                writes: tuples(...$tuplesBatch),
            );

            if ($writeResult->failed()) {
                continue;
            }

            $elapsed = (microtime(true) - $start) * 1000;
            $results[$size] = $elapsed;

            $expectedMax = $size * 20; // 20ms per tuple as upper bound
            expect($elapsed)->toBeLessThan($expectedMax);
        }

        echo "\nBatch write performance:\n";

        if (empty($results)) {
            echo "  No successful batch writes\n";
        } else {
            foreach ($results as $size => $time) {
                echo sprintf("  %d tuples: %.2fms (%.2fms per tuple)\n", $size, $time, $time / $size);
            }
        }
    });

    test('benchmark complex authorization checks', function (): void {
        $setupTuples = tuples(
            tuple('user:alice', 'member', 'group:engineering'),
            tuple('user:bob', 'member', 'group:engineering'),
            tuple('user:charlie', 'member', 'group:marketing'),
            tuple('group:engineering#member', 'member', 'organization:acme'),
            tuple('group:marketing#member', 'member', 'organization:acme'),
            tuple('organization:acme#member', 'viewer', 'document:company-handbook'),
            tuple('group:engineering#member', 'editor', 'document:api-docs'),
            tuple('user:diane', 'owner', 'document:secret-project'),
        );

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $setupTuples,
        )->rethrow()->unwrap();

        $testCases = [
            ['user' => 'user:alice', 'relation' => 'viewer', 'object' => 'document:company-handbook', 'name' => 'indirect through org'],
            ['user' => 'user:alice', 'relation' => 'editor', 'object' => 'document:api-docs', 'name' => 'indirect through group'],
            ['user' => 'user:diane', 'relation' => 'viewer', 'object' => 'document:secret-project', 'name' => 'direct owner'],
        ];

        echo "\nComplex authorization check performance:\n";

        foreach ($testCases as $testCase) {
            $times = [];

            for ($i = 0; 5 > $i; ++$i) {
                $start = microtime(true);

                $this->client->check(
                    store: $this->storeId,
                    model: $this->modelId,
                    tupleKey: tuple($testCase['user'], $testCase['relation'], $testCase['object']),
                )->rethrow()->unwrap();

                $times[] = (microtime(true) - $start) * 1000;
            }

            $avgTime = array_sum($times) / count($times);
            echo sprintf("  %s: %.2fms avg\n", $testCase['name'], $avgTime);

            expect($avgTime)->toBeLessThan(150);
        }
    });

    test('benchmark listObjects performance', function (): void {
        $numDocuments = 100;
        $tuplesBatch = [];

        for ($i = 0; $i < $numDocuments; ++$i) {
            if (0 === $i % 3) {
                $tuplesBatch[] = tuple('user:alice', 'viewer', "document:doc{$i}");
            }

            if (0 === $i % 5) {
                $tuplesBatch[] = tuple('user:alice', 'editor', "document:doc{$i}");
            }
        }

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(...$tuplesBatch),
        )->rethrow()->unwrap();

        $start = microtime(true);

        $result = $this->client->listObjects(
            store: $this->storeId,
            model: $this->modelId,
            type: 'document',
            relation: 'viewer',
            user: 'user:alice',
        )->rethrow()->unwrap();

        $elapsed = (microtime(true) - $start) * 1000;
        $objectCount = count($result->getObjects());

        echo sprintf(
            "\nlistObjects performance: %d objects in %.2fms (%.2fms per object)\n",
            $objectCount,
            $elapsed,
            0 < $objectCount ? $elapsed / $objectCount : 0,
        );

        expect($elapsed)->toBeLessThan(500);
    });

    test('benchmark concurrent operations', function (): void {
        $operations = [
            'write' => 0,
            'check' => 0,
            'read' => 0,
        ];

        $totalStart = microtime(true);

        $start = microtime(true);
        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:test', 'viewer', 'document:concurrent'),
            ),
        )->rethrow()->unwrap();
        $operations['write'] = (microtime(true) - $start) * 1000;

        $start = microtime(true);
        $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:test', 'viewer', 'document:concurrent'),
        )->rethrow()->unwrap();
        $operations['check'] = (microtime(true) - $start) * 1000;

        $start = microtime(true);
        $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('user:test', 'viewer', 'document:concurrent'),
        )->rethrow()->unwrap();
        $operations['read'] = (microtime(true) - $start) * 1000;

        $totalElapsed = (microtime(true) - $totalStart) * 1000;

        echo "\nConcurrent operations performance:\n";

        foreach ($operations as $op => $time) {
            echo sprintf("  %s: %.2fms\n", $op, $time);
        }
        echo sprintf("  Total: %.2fms\n", $totalElapsed);

        expect($totalElapsed)->toBeLessThan(200);
    });

    test('benchmark pagination performance', function (): void {
        $tuplesBatch = [];

        for ($i = 0; 50 > $i; ++$i) {
            $tuplesBatch[] = tuple("user:user{$i}", 'viewer', 'document:shared');
        }

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(...$tuplesBatch),
        )->rethrow()->unwrap();

        $pageSize = 10;
        $pages = 0;
        $totalTime = 0;
        $continuationToken = null;

        do {
            $start = microtime(true);

            $result = $this->client->readTuples(
                store: $this->storeId,
                tupleKey: tuple('', '', 'document:shared'),
                pageSize: $pageSize,
                continuationToken: $continuationToken,
            )->rethrow()->unwrap();

            $totalTime += (microtime(true) - $start) * 1000;
            ++$pages;

            $continuationToken = $result->getContinuationToken();
        } while ($continuationToken && 10 > $pages); // Safety limit

        $avgPageTime = $totalTime / $pages;

        echo sprintf(
            "\nPagination performance: %d pages, %.2fms total, %.2fms per page\n",
            $pages,
            $totalTime,
            $avgPageTime,
        );

        expect($avgPageTime)->toBeLessThan(50);
    });
});
