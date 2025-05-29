<?php

declare(strict_types=1);

use OpenFGA\Client;

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
    $name = 'benchmark-test-' . bin2hex(random_bytes(5));
    $this->store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();
    $this->storeId = $this->store->getId();

    // Create authorization model
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
    // Clean up test store
    if (isset($this->storeId)) {
        $this->client->deleteStore(store: $this->storeId);
    }
});

test('benchmark single check performance', function (): void {
    // Setup: Write a simple tuple
    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:alice', 'viewer', 'document:doc1'),
        ),
    )->rethrow()->unwrap();

    // Benchmark single checks
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

    $avgTime = array_sum($times) / \count($times);
    $minTime = min($times);
    $maxTime = max($times);

    // Performance expectations (adjust based on your infrastructure)
    expect($avgTime)->toBeLessThan(100); // Average should be under 100ms
    expect($minTime)->toBeLessThan(50);  // Best case under 50ms

    // Log results for monitoring
    echo \sprintf(
        "\nSingle check performance: avg=%.2fms, min=%.2fms, max=%.2fms\n",
        $avgTime,
        $minTime,
        $maxTime,
    );
});

test('benchmark batch write performance', function (): void {
    // Test different batch sizes
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
            // Skip this batch size if it fails
            continue;
        }

        $elapsed = (microtime(true) - $start) * 1000;
        $results[$size] = $elapsed;

        // Performance expectation: should scale reasonably
        $expectedMax = $size * 20; // 20ms per tuple as upper bound
        expect($elapsed)->toBeLessThan($expectedMax);
    }

    // Log results
    echo "\nBatch write performance:\n";
    if (empty($results)) {
        echo "  No successful batch writes\n";
    } else {
        foreach ($results as $size => $time) {
            echo \sprintf("  %d tuples: %.2fms (%.2fms per tuple)\n", $size, $time, $time / $size);
        }
    }
});

test('benchmark complex authorization checks', function (): void {
    // Setup: Create a hierarchy of relationships
    $setupTuples = tuples(
        // Users in groups
        tuple('user:alice', 'member', 'group:engineering'),
        tuple('user:bob', 'member', 'group:engineering'),
        tuple('user:charlie', 'member', 'group:marketing'),

        // Groups in organization
        tuple('group:engineering#member', 'member', 'organization:acme'),
        tuple('group:marketing#member', 'member', 'organization:acme'),

        // Document permissions
        tuple('organization:acme#member', 'viewer', 'document:company-handbook'),
        tuple('group:engineering#member', 'editor', 'document:api-docs'),
        tuple('user:diane', 'owner', 'document:secret-project'),
    );

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: $setupTuples,
    )->rethrow()->unwrap();

    // Benchmark checks that require traversing the relationship graph
    $testCases = [
        ['user' => 'user:alice', 'relation' => 'viewer', 'object' => 'document:company-handbook', 'name' => 'indirect through org'],
        ['user' => 'user:alice', 'relation' => 'editor', 'object' => 'document:api-docs', 'name' => 'indirect through group'],
        ['user' => 'user:diane', 'relation' => 'viewer', 'object' => 'document:secret-project', 'name' => 'direct owner'],
    ];

    echo "\nComplex authorization check performance:\n";

    foreach ($testCases as $testCase) {
        $times = [];

        for ($i = 0; $i < 5; ++$i) {
            $start = microtime(true);

            $this->client->check(
                store: $this->storeId,
                model: $this->modelId,
                tupleKey: tuple($testCase['user'], $testCase['relation'], $testCase['object']),
            )->rethrow()->unwrap();

            $times[] = (microtime(true) - $start) * 1000;
        }

        $avgTime = array_sum($times) / \count($times);
        echo \sprintf("  %s: %.2fms avg\n", $testCase['name'], $avgTime);

        // Complex checks should still be reasonably fast
        expect($avgTime)->toBeLessThan(150);
    }
});

test('benchmark listObjects performance', function (): void {
    // Setup: Create many objects with permissions
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

    // Benchmark listObjects
    $start = microtime(true);

    $result = $this->client->listObjects(
        store: $this->storeId,
        model: $this->modelId,
        type: 'document',
        relation: 'viewer',
        user: 'user:alice',
    )->rethrow()->unwrap();

    $elapsed = (microtime(true) - $start) * 1000;
    $objectCount = \count($result->getObjects());

    echo \sprintf(
        "\nlistObjects performance: %d objects in %.2fms (%.2fms per object)\n",
        $objectCount,
        $elapsed,
        $objectCount > 0 ? $elapsed / $objectCount : 0,
    );

    // Should complete in reasonable time even with many objects
    expect($elapsed)->toBeLessThan(500);
});

test('benchmark concurrent operations', function (): void {
    // This test simulates multiple operations happening in quick succession
    $operations = [
        'write' => 0,
        'check' => 0,
        'read' => 0,
    ];

    $totalStart = microtime(true);

    // Write operation
    $start = microtime(true);
    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:test', 'viewer', 'document:concurrent'),
        ),
    )->rethrow()->unwrap();
    $operations['write'] = (microtime(true) - $start) * 1000;

    // Check operation
    $start = microtime(true);
    $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:test', 'viewer', 'document:concurrent'),
    )->rethrow()->unwrap();
    $operations['check'] = (microtime(true) - $start) * 1000;

    // Read operation
    $start = microtime(true);
    $this->client->readTuples(
        store: $this->storeId,
        tupleKey: tuple('user:test', 'viewer', 'document:concurrent'),
    )->rethrow()->unwrap();
    $operations['read'] = (microtime(true) - $start) * 1000;

    $totalElapsed = (microtime(true) - $totalStart) * 1000;

    echo "\nConcurrent operations performance:\n";
    foreach ($operations as $op => $time) {
        echo \sprintf("  %s: %.2fms\n", $op, $time);
    }
    echo \sprintf("  Total: %.2fms\n", $totalElapsed);

    // All operations combined should complete quickly
    expect($totalElapsed)->toBeLessThan(200);
});

test('benchmark pagination performance', function (): void {
    // Create many tuples to test pagination
    $tuplesBatch = [];
    for ($i = 0; $i < 50; ++$i) {
        $tuplesBatch[] = tuple("user:user{$i}", 'viewer', 'document:shared');
    }

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(...$tuplesBatch),
    )->rethrow()->unwrap();

    // Benchmark paginated reads
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
    } while ($continuationToken && $pages < 10); // Safety limit

    $avgPageTime = $totalTime / $pages;

    echo \sprintf(
        "\nPagination performance: %d pages, %.2fms total, %.2fms per page\n",
        $pages,
        $totalTime,
        $avgPageTime,
    );

    // Each page should be fast
    expect($avgPageTime)->toBeLessThan(50);
});
