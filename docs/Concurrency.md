# Concurrency Guide

Learn how to leverage the OpenFGA PHP SDK's powerful concurrency features to dramatically improve performance when working with large-scale authorization operations. This guide covers async patterns, fiber-based parallelism, and batch operations that can speed up your authorization workflows by orders of magnitude.

## Prerequisites

All examples in this guide assume you have the following setup:

```php
<?php

use OpenFGA\Client;
use function OpenFGA\{batch, tuples, tuple, store, model};

// Basic client setup
$client = new Client(url: 'http://localhost:8080');

// Store and model (replace with your actual IDs, or use the helper functions)
$storeId = 'your-store-id';  // or: $storeId = store($client, 'My Store');
$modelId = 'your-model-id';  // or: $modelId = model($client, $storeId, $authorizationModel);
```

With this setup established, the examples below focus on the concurrency features without repetitive boilerplate.

## Table of Contents

- [Quick Start](#quick-start)
- [Core Concepts](#core-concepts)
- [Batch Operations](#batch-operations)
- [Parallel Processing Patterns](#parallel-processing-patterns)
- [Error Handling and Resilience](#error-handling-and-resilience)
- [Integration with Async Frameworks](#integration-with-async-frameworks)
- [Performance Optimization](#performance-optimization)
- [Monitoring and Debugging](#monitoring-and-debugging)
- [Best Practices](#best-practices)
- [Next Steps](#next-steps)

## Quick Start

Transform slow sequential operations into blazing-fast parallel executions with just a few configuration changes:

```php
// Create some sample tuples
$tuplesToWrite = tuples(
    tuple('user:anne', 'reader', 'document:budget'),
    tuple('user:beth', 'writer', 'document:budget'),
    // ... more tuples
);

// Sequential: ~10 seconds for 1000 tuples
$result = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 1
);

// Parallel: ~2 seconds for 1000 tuples (5x faster!)
$result = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 10
);
```

## Core Concepts

### Why Concurrency Matters

When managing thousands or millions of authorization tuples, sequential processing becomes a bottleneck. The SDK's concurrency features let you:

- **Reduce latency** by processing multiple operations simultaneously
- **Maximize throughput** with configurable parallelism levels
- **Handle failures gracefully** without losing successful operations
- **Scale efficiently** while respecting API rate limits

### PHP Fibers: Modern Async Without the Complexity

The SDK uses PHP 8.1+ Fibers to provide true concurrency without the complexity of promises or callbacks. Fibers allow cooperative multitasking where operations yield control when waiting for I/O, enabling other operations to proceed.

## Batch Operations

### Basic Batch Usage

The batch helper function and `batchTuples` method process large sets of tuple operations efficiently:

```php
// Prepare your tuple operations using helper functions
$writes = [];
for ($i = 0; $i < 1000; $i++) {
    $writes[] = tuple("user:user_{$i}", 'reader', "document:doc_{$i}");
}
$writeTuples = tuples(...$writes);

// Execute batch operation using the helper function
$result = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $writeTuples
);

echo "Successful: {$result->getSuccessfulChunks()}\n";
echo "Failed: {$result->getFailedChunks()}\n";
echo "Total operations: {$result->getTotalOperations()}\n";
```

### Configuration Options

Fine-tune batch behavior for your specific needs:

```php
$result = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $writes,
    deletes: $deletes,
    maxParallelRequests: 10,      // Concurrent requests (default: 1)
    maxTuplesPerChunk: 50,        // Tuples per request (max: 100)
    maxRetries: 3,                // Retry attempts per chunk
    retryDelaySeconds: 1.0,       // Initial retry delay
    stopOnFirstError: false       // Continue on failures
);
```

### Using the Batch Helper Function

The SDK provides a convenient batch helper function for non-transactional writes with full configuration:

```php
// Prepare tuples for writing and deleting
$writes = tuples(
    tuple('user:anne', 'reader', 'document:budget'),
    tuple('user:beth', 'writer', 'document:budget'),
    // ... hundreds more
);

$deletes = tuples(
    tuple('user:carl', 'reader', 'document:old-doc'),
    // ... hundreds more
);

// Execute batch operation with full configuration
$result = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $writes,
    deletes: $deletes,
    maxParallelRequests: 10,
    maxTuplesPerChunk: 50,
    maxRetries: 3,
    retryDelaySeconds: 1.0,
    stopOnFirstError: false  // Don't stop on first failure
);

// Detailed results
echo "Chunks processed: {$result->getSuccessfulChunks()}/{$result->getTotalChunks()}\n";
echo "Operations successful: {$result->getSuccessfulOperations()}/{$result->getTotalOperations()}\n";

// Handle any errors
foreach ($result->getErrors() as $error) {
    echo "Error in chunk: {$error->getMessage()}\n";
}
```

## Parallel Processing Patterns

### Optimal Parallelism Levels

Choose parallelism based on your infrastructure and requirements:

```php
// Conservative: Good for shared environments
$result = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 3
);

// Moderate: Balanced performance
$result = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 5
);

// Aggressive: Maximum throughput
$result = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 10
);
```

### Performance Comparison Example

Here's a real-world example showing the performance benefits:

```php
// Generate test data
$testTuples = [];
for ($i = 0; $i < 1000; $i++) {
    $testTuples[] = tuple("user:employee_{$i}", 'member', 'team:engineering');
}
$tuplesToWrite = tuples(...$testTuples);

// Sequential processing
$start = microtime(true);
$sequentialResult = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 1
);
$sequentialTime = microtime(true) - $start;

// Parallel processing
$start = microtime(true);
$parallelResult = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 10
);
$parallelTime = microtime(true) - $start;

echo "Sequential: {$sequentialTime}s\n";
echo "Parallel: {$parallelTime}s\n";
echo "Speedup: " . round($sequentialTime / $parallelTime, 2) . "x faster\n";
```

## Error Handling and Resilience

### Partial Success Handling

The SDK continues processing even when some operations fail:

```php
$result = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxRetries: 3,           // Retry failed chunks
    stopOnFirstError: false  // Don't stop on first failure
);

if ($result->hasErrors()) {
    echo "Completed with {$result->getFailedChunks()} failed chunks\n";

    // Process successful operations
    echo "Successfully processed: {$result->getSuccessfulOperations()} tuples\n";

    // Handle failures
    foreach ($result->getErrors() as $error) {
        // Log or retry failed chunks
        error_log("Chunk failed: " . $error->getMessage());
    }
}
```

### Retry Strategies

Configure retry behavior for transient failures:

```php
$result = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxRetries: 3,               // Retry up to 3 times
    retryDelaySeconds: 1.0,      // Start with 1 second delay
    maxParallelRequests: 5
);

// The SDK uses exponential backoff:
// - First retry: 1 second delay
// - Second retry: 2 second delay
// - Third retry: 4 second delay
```

## Integration with Async Frameworks

### Using with ReactPHP

While the SDK uses native PHP Fibers, you can integrate it with async frameworks:

```php
use React\EventLoop\Loop;
use React\Promise\Promise;

// Wrap SDK calls in promises
function batchTuplesAsync($client, $storeId, $modelId, $writes, $deletes, $options = []) {
    return new Promise(function ($resolve, $reject) use ($client, $storeId, $modelId, $writes, $deletes, $options) {
        Loop::futureTick(function () use ($resolve, $reject, $client, $storeId, $modelId, $writes, $deletes, $options) {
            try {
                $result = batch(
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
use function OpenFGA\batch;

Coroutine\run(function () use ($client, $storeId, $modelId, $tuplesToWrite) {
    $result = Coroutine::create(function () use ($client, $storeId, $modelId, $tuplesToWrite) {
        return batch(
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

## Performance Optimization

### Chunk Size Optimization

Find the optimal chunk size for your use case:

```php
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

    $result = batch(
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

### Memory Management

Handle large datasets efficiently:

```php

// Process large datasets in batches to manage memory
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
        $result = batch(
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

## Monitoring and Debugging

### Performance Metrics

Track performance metrics for optimization:

```php

// Note: This is an example helper class and not part of the SDK.
class BatchMetrics {
    public static function track($client, $storeId, $modelId, $tuplesToWrite, $options) {
        $start = microtime(true);
        $startMemory = memory_get_usage(true);

        $result = batch(
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

### Debug Logging

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

## Best Practices

### 1. Start Conservative, Scale Up

Begin with lower parallelism and increase based on monitoring:

```php
// Development environment
$parallelism = 2;

// Staging environment
$parallelism = 5;

// Production (after load testing)
$parallelism = 10;
```

### 2. Handle Rate Limits Gracefully

Implement backoff when hitting rate limits:

```php
$result = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 5,
    maxRetries: 5,              // More retries for rate limits
    retryDelaySeconds: 2.0,     // Longer initial delay
    stopOnFirstError: false     // Continue on error
);
```

### 3. Monitor Resource Usage

Keep an eye on system resources:

```php

// Monitor CPU and memory during batch operations
$cpuBefore = sys_getloadavg()[0];
$memBefore = memory_get_usage(true);

$result = batch(
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

### 4. Use Appropriate Chunk Sizes

Balance between API limits and efficiency:

- **Small chunks (10-25)**: Lower latency per request, more overhead
- **Medium chunks (50)**: Good balance for most use cases
- **Large chunks (75-100)**: Maximum efficiency, higher latency per request

### 5. Implement Circuit Breakers

Protect against cascading failures:

```php

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
            $result = batch(
                client: $client,
                store: $storeId,
                model: $modelId,
                writes: $tuplesToWrite
            );
            $this->failures = 0;  // Reset on success
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

## Next Steps

- Review the [Non-Transactional Writes Example](../examples/non-transactional-writes/example.php) for detailed parallel processing patterns
- Explore [Integration Tests](../tests/Integration/BatchTuplesIntegrationTest.php) for advanced usage patterns
- Check out [Observability Guide](Observability.md) for monitoring concurrent operations
- Read about [Error Handling](Results.md) for comprehensive failure management

With these concurrency features, you can scale your authorization system to handle millions of relationships efficiently while maintaining reliability and performance.
