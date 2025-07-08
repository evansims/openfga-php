Learn how to leverage the OpenFGA PHP SDK's powerful concurrency features to dramatically improve performance when working with large-scale authorization operations. This guide covers async patterns, fiber-based parallelism, and bulk write operations that can speed up your authorization workflows by orders of magnitude.

## Prerequisites

All examples in this guide assume you have the following setup:

[Snippet](../../examples/snippets/concurrency-setup.php)

With this setup established, the examples below focus on the concurrency features without repetitive boilerplate.

## Quickstart

Transform slow sequential operations into blazing-fast parallel executions with just a few configuration changes:

[Snippet](../../examples/snippets/concurrency-parallel.php#parallelism)

## Core concepts

### Why concurrency matters

When managing thousands or millions of authorization tuples, sequential processing becomes a bottleneck. The SDK's concurrency features let you:

- **Reduce latency** by processing multiple operations simultaneously
- **Maximize throughput** with configurable parallelism levels
- **Handle failures gracefully** without losing successful operations
- **Scale efficiently** while respecting API rate limits

### PHP Fibers: Modern async without the complexity

The SDK uses PHP 8.1+ Fibers to provide true concurrency without the complexity of promises or callbacks. Fibers allow cooperative multitasking where operations yield control when waiting for I/O, enabling other operations to proceed.

## Bulk write operations

Use the `writes` helper function or `writeTuples` method to process large sets of tuple operations efficiently:

[Snippet](../../examples/snippets/concurrency-bulk-basic.php#helper)

[Snippet](../../examples/snippets/concurrency-bulk-basic.php#api)

### Configuration options

Fine-tune bulk write behavior for your specific needs:

[Snippet](../../examples/snippets/concurrency-bulk-config.php#config)

## Parallel processing patterns

### Optimal parallelism levels

Choose parallelism based on your infrastructure and requirements:

```php
use function OpenFGA\{tuple, tuples, writes};

// Conservative: Good for shared environments
$result = writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 3
);

// Moderate: Balanced performance
$result = writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 5
);

// Aggressive: Maximum throughput
$result = writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 10
);
```

## Error handling and resilience

### Partial success handling

In non-transactional mode (which the `writes` helper defaults to) the SDK will continue processing even when some operations fail:

```php
use function OpenFGA\{tuple, tuples, writes};

$result = writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxRetries: 3, // Retry failed chunks
    stopOnFirstError: false // Don't stop on first failure
);

if ($result->hasErrors()) {
    echo "Completed with {$result->getFailedChunks()} failed chunks\n";
    echo "Successfully processed: {$result->getSuccessfulOperations()} tuples\n";

    foreach ($result->getErrors() as $error) {
        error_log("Chunk failed: " . $error->getMessage());
    }
}
```

### Retry strategies

Configure retry behavior for transient failures:

```php
use function OpenFGA\{tuple, tuples, writes};

$result = writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxRetries: 3, // Retry up to 3 times
    retryDelaySeconds: 1.0, // Start with 1 second delay
    maxParallelRequests: 5
);

// The SDK uses exponential backoff:
// - First retry: 1 second delay
// - Second retry: 2 second delay
// - Third retry: 4 second delay
```

## Integration with async frameworks

### Using with ReactPHP

While the SDK uses native PHP Fibers, you can integrate it with async frameworks:

```php
use function OpenFGA\{tuple, tuples, writes};

use React\EventLoop\Loop;
use React\Promise\Promise;

// Wrap SDK calls in promises
function batchTuplesAsync($client, $storeId, $modelId, $writes, $deletes, $options = []) {
    return new Promise(function ($resolve, $reject) use ($client, $storeId, $modelId, $writes, $deletes, $options) {
        Loop::futureTick(function () use ($resolve, $reject, $client, $storeId, $modelId, $writes, $deletes, $options) {
            try {
                $result = writes(
                    client: $client,
                    store: $storeId,
                    model: $modelId,
                    writes: $writes,
                    deletes: $deletes,
                    maxParallelRequests: $options['maxParallelRequests'] ?? 1
                );
                $resolve($result);
            } catch (\Exception $e) {
                $reject($e);
            }
        });
    });
}

// Use in async context
batchTuplesAsync($client, $storeId, $modelId, $writes, $deletes, ['maxParallelRequests' => 10])
    ->then(function ($result) {
        echo "Batch completed: {$result->getSuccessfulOperations()} operations\n";
    })
    ->catch(function ($error) {
        echo "Batch failed: {$error->getMessage()}\n";
    });
```

### Using with Swoole

Integrate with Swoole coroutines:

```php
use Swoole\Coroutine;
use function OpenFGA\writes;

Coroutine\run(function () use ($client, $storeId, $modelId, $tuplesToWrite) {
    $result = Coroutine::create(function () use ($client, $storeId, $modelId, $tuplesToWrite) {
        return writes(
            client: $client,
            store: $storeId,
            model: $modelId,
            writes: $tuplesToWrite,
            maxParallelRequests: 10
        );
    });

    echo "Processed in coroutine: {$result->getTotalOperations()} tuples\n";
});
```

## Performance optimization

### Chunk size optimization

Find the optimal chunk size for your use case:

```php
use function OpenFGA\{tuple, tuples, writes};

// Generate test tuples
$testTuples = [];
for ($i = 0; $i < 1000; $i++) {
    $testTuples[] = tuple("user:test_{$i}", 'member', 'org:acme');
}
$tuplesToWrite = tuples(...$testTuples);

// Test different chunk sizes
$chunkSizes = [10, 25, 50, 75, 100];
$results = [];

foreach ($chunkSizes as $chunkSize) {
    $start = microtime(true);

    $result = writes(
        client: $client,
        store: $storeId,
        model: $modelId,
        writes: $tuplesToWrite,
        maxTuplesPerChunk: $chunkSize,
        maxParallelRequests: 5
    );

    $duration = microtime(true) - $start;
    $throughput = count($testTuples) / $duration;

    $results[$chunkSize] = [
        'duration' => $duration,
        'throughput' => $throughput,
        'chunks' => $result->getTotalChunks(),
    ];
}

// Find optimal chunk size
$optimal = array_reduce(array_keys($results), function ($carry, $size) use ($results) {
    return $results[$size]['throughput'] > $results[$carry]['throughput'] ? $size : $carry;
}, array_key_first($results));

echo "Optimal chunk size: {$optimal} (throughput: {$results[$optimal]['throughput']} tuples/sec)\n";
```

### Memory management

Handle large datasets efficiently:

```php
use function OpenFGA\{tuple, tuples, writes};

// Process large datasets in chunks to manage memory
function processLargeTupleSet($client, $storeId, $modelId, $totalTuples) {
    $batchSize = 10000;  // Process 10k at a time
    $processed = 0;

    while ($processed < $totalTuples) {
        // Generate batch (in real app, fetch from database)
        $batchTuples = [];
        $remaining = min($batchSize, $totalTuples - $processed);

        for ($i = 0; $i < $remaining; $i++) {
            $batchTuples[] = tuple(
                "user:" . ($processed + $i),
                'member',
                'org:acme'
            );
        }

        $tuplesToWrite = tuples(...$batchTuples);

        // Process batch with high parallelism
        $result = writes(
            client: $client,
            store: $storeId,
            model: $modelId,
            writes: $tuplesToWrite,
            maxParallelRequests: 10,
            maxTuplesPerChunk: 100
        );

        $processed += $remaining;
        echo "Processed: {$processed}/{$totalTuples}\n";

        // Allow garbage collection between batches
        unset($batchTuples, $tuplesToWrite, $result);
    }
}
```

## Monitoring and debugging

### Performance metrics

Track performance metrics for optimization:

```php
use function OpenFGA\{tuple, tuples, writes};

// Note: This is an example helper class and not part of the SDK.
class BatchMetrics {
    public static function track($client, $storeId, $modelId, $tuplesToWrite, $options) {
        $start = microtime(true);
        $startMemory = memory_get_usage(true);

        $result = writes(
            client: $client,
            store: $storeId,
            model: $modelId,
            writes: $tuplesToWrite,
            maxParallelRequests: $options['parallelism'] ?? 1
        );

        $duration = microtime(true) - $start;
        $memoryUsed = memory_get_usage(true) - $startMemory;

        return [
            'duration' => $duration,
            'throughput' => count($tuplesToWrite) / $duration,
            'memory_mb' => $memoryUsed / 1024 / 1024,
            'chunks' => $result->getTotalChunks(),
            'failures' => $result->getFailedChunks(),
            'efficiency' => $result->getSuccessfulOperations() / count($tuplesToWrite),
        ];
    }
}

// Use metrics to compare strategies
$metrics = BatchMetrics::track($client, $storeId, $modelId, $tuplesToWrite, ['parallelism' => 10]);
echo "Throughput: {$metrics['throughput']} tuples/sec\n";
echo "Memory usage: {$metrics['memory_mb']} MB\n";
echo "Efficiency: " . ($metrics['efficiency'] * 100) . "%\n";
```

### Debug logging

Enable detailed logging for troubleshooting:

```php
use Psr\Log\LoggerInterface;

// Note: This is an example helper class and not part of the SDK.
class BatchLogger {
    private LoggerInterface $logger;

    public function logBatchOperation($result) {
        $this->logger->info('Batch operation completed', [
            'total_operations' => $result->getTotalOperations(),
            'successful_chunks' => $result->getSuccessfulChunks(),
            'failed_chunks' => $result->getFailedChunks(),
            'duration' => $result->getDuration(),
        ]);

        if ($result->hasErrors()) {
            foreach ($result->getErrors() as $error) {
                $this->logger->error('Chunk failed', [
                    'error' => $error->getMessage(),
                    'code' => $error->getCode(),
                ]);
            }
        }
    }
}
```

## Best practices

### 1. Start conservative, scale up

Begin with lower parallelism and increase based on monitoring:

```php
use function OpenFGA\{tuple, tuples, writes};

// Development environment
$parallelism = 2;

// Staging environment
$parallelism = 5;

// Production (after load testing)
$parallelism = 10;
```

### 2. Handle rate limits gracefully

Implement backoff when hitting rate limits:

```php
$result = writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 5,
    maxRetries: 5, // More retries for rate limits
    retryDelaySeconds: 2.0, // Longer initial delay
    stopOnFirstError: false // Continue on error
);
```

### 3. Monitor resource usage

Keep an eye on system resources:

```php
use function OpenFGA\{tuple, tuples, writes};

// Monitor CPU and memory during batch operations
$cpuBefore = sys_getloadavg()[0];
$memBefore = memory_get_usage(true);

$result = writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $writes,
    deletes: $deletes,
    maxParallelRequests: 10
);

$cpuAfter = sys_getloadavg()[0];
$memAfter = memory_get_usage(true);

echo "CPU load increase: " . ($cpuAfter - $cpuBefore) . "\n";
echo "Memory increase: " . (($memAfter - $memBefore) / 1024 / 1024) . " MB\n";
```

### 4. Use appropriate chunk sizes

Balance between API limits and efficiency:

- **Small chunks (10-25)**: Lower latency per request, more overhead
- **Medium chunks (50)**: Good balance for most use cases
- **Large chunks (75-100)**: Maximum efficiency, higher latency per request

### 5. Implement circuit breakers

Protect against cascading failures:

```php
use function OpenFGA\{tuple, tuples, writes};

// Note: This is an example helper class and not part of the SDK.
class BatchCircuitBreaker {
    private int $failures = 0;
    private int $threshold = 5;
    private bool $open = false;

    public function executeBatch($client, $storeId, $modelId, $tuplesToWrite) {
        if ($this->open) {
            throw new \RuntimeException('Circuit breaker is open');
        }

        try {
            $result = writes(
                client: $client,
                store: $storeId,
                model: $modelId,
                writes: $tuplesToWrite
            );
            $this->failures = 0; // Reset on success
            return $result;
        } catch (\Exception $e) {
            $this->failures++;
            if ($this->failures >= $this->threshold) {
                $this->open = true;
            }
            throw $e;
        }
    }
}
```
