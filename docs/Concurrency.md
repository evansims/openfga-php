# Concurrency Guide

Learn how to leverage the OpenFGA PHP SDK's powerful concurrency features to dramatically improve performance when working with large-scale authorization operations. This guide covers async patterns, fiber-based parallelism, and batch operations that can speed up your authorization workflows by orders of magnitude.

## Quick Start

Transform slow sequential operations into blazing-fast parallel executions with just a few configuration changes:

```php
use OpenFga\Sdk\Helpers;

// Sequential: ~10 seconds for 1000 tuples
$result = Helpers::batch($client, $storeId)
    ->write($tuples)
    ->withMaxParallelRequests(1)
    ->execute();

// Parallel: ~2 seconds for 1000 tuples (5x faster!)
$result = Helpers::batch($client, $storeId)
    ->write($tuples)
    ->withMaxParallelRequests(10)
    ->execute();
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

The `batchTuples` method processes large sets of tuple operations efficiently:

```php
use OpenFga\Sdk\Client;
use OpenFga\Sdk\Configuration;

$client = new Client(new Configuration([
    'apiUrl' => 'http://localhost:8080',
    'storeId' => $storeId,
]));

// Prepare your tuple operations
$writes = [];
$deletes = [];

for ($i = 0; $i < 1000; $i++) {
    $writes[] = [
        'user' => "user:user_{$i}",
        'relation' => 'reader',
        'object' => "document:doc_{$i}",
    ];
}

// Execute batch operation
$result = $client->batchTuples($writes, $deletes)->unwrap();

echo "Successful: {$result->getSuccessfulChunks()}\n";
echo "Failed: {$result->getFailedChunks()}\n";
echo "Total operations: {$result->getTotalOperations()}\n";
```

### Configuration Options

Fine-tune batch behavior for your specific needs:

```php
$result = $client->batchTuples(
    writes: $writes,
    deletes: $deletes,
    options: [
        'maxParallelRequests' => 10,      // Concurrent requests (default: 1)
        'maxTuplesPerChunk' => 50,        // Tuples per request (max: 100)
        'maxRetries' => 3,                // Retry attempts per chunk
        'retryDelaySeconds' => 1,         // Initial retry delay
        'stopOnFirstError' => false,      // Continue on failures
    ]
)->unwrap();
```

### Using the Fluent Helper Interface

The SDK provides a more expressive fluent interface through helpers:

```php
use OpenFga\Sdk\Helpers;

$result = Helpers::batch($client, $storeId)
    ->write([
        ['user' => 'user:anne', 'relation' => 'reader', 'object' => 'document:budget'],
        ['user' => 'user:beth', 'relation' => 'writer', 'object' => 'document:budget'],
        // ... hundreds more
    ])
    ->delete([
        ['user' => 'user:carl', 'relation' => 'reader', 'object' => 'document:old-doc'],
        // ... hundreds more
    ])
    ->withMaxParallelRequests(10)
    ->withMaxTuplesPerChunk(50)
    ->withMaxRetries(3)
    ->withRetryDelay(1)
    ->continueOnError()  // Don't stop on first failure
    ->execute();

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
$result = Helpers::batch($client, $storeId)
    ->write($tuples)
    ->withMaxParallelRequests(3)
    ->execute();

// Moderate: Balanced performance
$result = Helpers::batch($client, $storeId)
    ->write($tuples)
    ->withMaxParallelRequests(5)
    ->execute();

// Aggressive: Maximum throughput
$result = Helpers::batch($client, $storeId)
    ->write($tuples)
    ->withMaxParallelRequests(10)
    ->execute();
```

### Performance Comparison Example

Here's a real-world example showing the performance benefits:

```php
use OpenFga\Sdk\Helpers;

// Generate test data
$tuples = [];
for ($i = 0; $i < 1000; $i++) {
    $tuples[] = [
        'user' => "user:employee_{$i}",
        'relation' => 'member',
        'object' => "team:engineering",
    ];
}

// Sequential processing
$start = microtime(true);
$sequentialResult = Helpers::batch($client, $storeId)
    ->write($tuples)
    ->withMaxParallelRequests(1)
    ->execute();
$sequentialTime = microtime(true) - $start;

// Parallel processing
$start = microtime(true);
$parallelResult = Helpers::batch($client, $storeId)
    ->write($tuples)
    ->withMaxParallelRequests(10)
    ->execute();
$parallelTime = microtime(true) - $start;

echo "Sequential: {$sequentialTime}s\n";
echo "Parallel: {$parallelTime}s\n";
echo "Speedup: " . round($sequentialTime / $parallelTime, 2) . "x faster\n";
```

## Error Handling and Resilience

### Partial Success Handling

The SDK continues processing even when some operations fail:

```php
$result = Helpers::batch($client, $storeId)
    ->write($tuples)
    ->continueOnError()  // Don't stop on first failure
    ->withMaxRetries(3)  // Retry failed chunks
    ->execute();

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
$result = Helpers::batch($client, $storeId)
    ->write($tuples)
    ->withMaxRetries(3)         // Retry up to 3 times
    ->withRetryDelay(1)         // Start with 1 second delay
    ->withMaxParallelRequests(5)
    ->execute();

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
function batchTuplesAsync($client, $writes, $deletes, $options = []) {
    return new Promise(function ($resolve, $reject) use ($client, $writes, $deletes, $options) {
        Loop::futureTick(function () use ($resolve, $reject, $client, $writes, $deletes, $options) {
            try {
                $result = $client->batchTuples($writes, $deletes, $options)->unwrap();
                $resolve($result);
            } catch (\Exception $e) {
                $reject($e);
            }
        });
    });
}

// Use in async context
batchTuplesAsync($client, $writes, $deletes, ['maxParallelRequests' => 10])
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

Coroutine\run(function () use ($client, $tuples) {
    $result = Coroutine::create(function () use ($client, $tuples) {
        return $client->batchTuples($tuples, [], [
            'maxParallelRequests' => 10
        ])->unwrap();
    });
    
    echo "Processed in coroutine: {$result->getTotalOperations()} tuples\n";
});
```

## Performance Optimization

### Chunk Size Optimization

Find the optimal chunk size for your use case:

```php
// Test different chunk sizes
$chunkSizes = [10, 25, 50, 75, 100];
$results = [];

foreach ($chunkSizes as $chunkSize) {
    $start = microtime(true);
    
    $result = Helpers::batch($client, $storeId)
        ->write($tuples)
        ->withMaxTuplesPerChunk($chunkSize)
        ->withMaxParallelRequests(5)
        ->execute();
    
    $duration = microtime(true) - $start;
    $throughput = count($tuples) / $duration;
    
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
function processLargeTupleSet($client, $storeId, $totalTuples) {
    $batchSize = 10000;  // Process 10k at a time
    $processed = 0;
    
    while ($processed < $totalTuples) {
        // Generate batch (in real app, fetch from database)
        $batch = [];
        $remaining = min($batchSize, $totalTuples - $processed);
        
        for ($i = 0; $i < $remaining; $i++) {
            $batch[] = [
                'user' => "user:" . ($processed + $i),
                'relation' => 'member',
                'object' => 'org:acme',
            ];
        }
        
        // Process batch with high parallelism
        $result = Helpers::batch($client, $storeId)
            ->write($batch)
            ->withMaxParallelRequests(10)
            ->withMaxTuplesPerChunk(100)
            ->execute();
        
        $processed += $remaining;
        echo "Processed: {$processed}/{$totalTuples}\n";
        
        // Allow garbage collection between batches
        unset($batch, $result);
    }
}
```

## Monitoring and Debugging

### Performance Metrics

Track performance metrics for optimization:

```php
class BatchMetrics {
    public static function track($client, $storeId, $tuples, $options) {
        $start = microtime(true);
        $startMemory = memory_get_usage(true);
        
        $result = Helpers::batch($client, $storeId)
            ->write($tuples)
            ->withMaxParallelRequests($options['parallelism'] ?? 1)
            ->execute();
        
        $duration = microtime(true) - $start;
        $memoryUsed = memory_get_usage(true) - $startMemory;
        
        return [
            'duration' => $duration,
            'throughput' => count($tuples) / $duration,
            'memory_mb' => $memoryUsed / 1024 / 1024,
            'chunks' => $result->getTotalChunks(),
            'failures' => $result->getFailedChunks(),
            'efficiency' => $result->getSuccessfulOperations() / count($tuples),
        ];
    }
}

// Use metrics to compare strategies
$metrics = BatchMetrics::track($client, $storeId, $tuples, ['parallelism' => 10]);
echo "Throughput: {$metrics['throughput']} tuples/sec\n";
echo "Memory usage: {$metrics['memory_mb']} MB\n";
echo "Efficiency: " . ($metrics['efficiency'] * 100) . "%\n";
```

### Debug Logging

Enable detailed logging for troubleshooting:

```php
use Psr\Log\LoggerInterface;

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
$result = Helpers::batch($client, $storeId)
    ->write($tuples)
    ->withMaxParallelRequests(5)
    ->withMaxRetries(5)        // More retries for rate limits
    ->withRetryDelay(2)        // Longer initial delay
    ->continueOnError()
    ->execute();
```

### 3. Monitor Resource Usage

Keep an eye on system resources:

```php
// Monitor CPU and memory during batch operations
$cpuBefore = sys_getloadavg()[0];
$memBefore = memory_get_usage(true);

$result = $client->batchTuples($writes, $deletes, [
    'maxParallelRequests' => 10
])->unwrap();

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
class BatchCircuitBreaker {
    private int $failures = 0;
    private int $threshold = 5;
    private bool $open = false;
    
    public function executeBatch($client, $tuples) {
        if ($this->open) {
            throw new \RuntimeException('Circuit breaker is open');
        }
        
        try {
            $result = $client->batchTuples($tuples)->unwrap();
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