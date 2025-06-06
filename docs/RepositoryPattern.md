# Repository Pattern Guide

The OpenFGA PHP SDK uses the Repository pattern to abstract data access and provide a clean separation between business logic and infrastructure concerns. This guide explains how to use and extend the repository interfaces.

## Overview

Repositories provide a domain-focused interface for data operations while hiding implementation details like HTTP communication, caching, or database access. The SDK includes three main repository interfaces:

- `TupleRepositoryInterface` - Manages relationship tuples
- `StoreRepositoryInterface` - Manages OpenFGA stores  
- `ModelRepositoryInterface` - Manages authorization models

## Using Repositories

### Default Usage

By default, the Client uses HTTP implementations of the repositories:

```php
use OpenFGA\Client;

$client = new Client(url: 'https://api.openfga.example');

// The client internally uses:
// - HttpTupleRepository
// - HttpStoreRepository  
// - HttpModelRepository
```

### Custom Repository Injection

You can provide custom repository implementations:

```php
use OpenFGA\Client;
use OpenFGA\Repositories\HttpStoreRepository;
use MyApp\Repositories\CachedStoreRepository;

$httpRepo = new HttpStoreRepository($httpService, $validator);
$cachedRepo = new CachedStoreRepository($httpRepo, $cache);

$client = new Client(
    url: 'https://api.openfga.example',
    storeRepository: $cachedRepo
);
```

## Repository Interfaces

### TupleRepositoryInterface

Manages relationship tuples with support for both transactional and non-transactional operations.

```php
interface TupleRepositoryInterface
{
    public function write(
        StoreInterface $store,
        AuthorizationModelInterface $model,
        TupleKeysInterface $tuples,
        bool $transactional = true,
        array $options = []
    ): FailureInterface|SuccessInterface;
    
    public function read(
        StoreInterface $store,
        TupleKeyInterface $filter,
        ?string $continuationToken = null,
        ?int $pageSize = null
    ): FailureInterface|SuccessInterface;
    
    public function delete(
        StoreInterface $store,
        AuthorizationModelInterface $model,
        TupleKeysInterface $tuples,
        bool $transactional = true,
        array $options = []
    ): FailureInterface|SuccessInterface;
    
    public function listChanges(
        StoreInterface $store,
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
        ?string $continuationToken = null,
        ?int $pageSize = null
    ): FailureInterface|SuccessInterface;
}
```

#### Options for write/delete operations:
- `transactional` (bool) - Use transactional mode (default: true)
- `maxTuplesPerChunk` (int) - Maximum tuples per request in non-transactional mode
- `maxParallelRequests` (int) - Maximum concurrent requests 
- `maxRetries` (int) - Maximum retry attempts
- `retryDelaySeconds` (float) - Retry delay in seconds
- `stopOnFirstError` (bool) - Stop processing on first error

### StoreRepositoryInterface

Manages OpenFGA stores.

```php
interface StoreRepositoryInterface  
{
    public function create(string $name): FailureInterface|SuccessInterface;
    
    public function get(string $storeId): FailureInterface|SuccessInterface;
    
    public function list(
        ?int $pageSize = null,
        ?string $continuationToken = null
    ): FailureInterface|SuccessInterface;
    
    public function delete(string $storeId): FailureInterface|SuccessInterface;
}
```

### ModelRepositoryInterface

Manages authorization models within a store.

```php
interface ModelRepositoryInterface
{
    public function create(
        TypeDefinitionsInterface $typeDefinitions,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
        ?ConditionsInterface $conditions = null
    ): FailureInterface|SuccessInterface;
    
    public function get(string $modelId): FailureInterface|SuccessInterface;
    
    public function list(
        ?int $pageSize = null,
        ?string $continuationToken = null  
    ): FailureInterface|SuccessInterface;
}
```

## Implementing Custom Repositories

### Example: Cached Store Repository

```php
use OpenFGA\Repositories\StoreRepositoryInterface;
use OpenFGA\Results\{ResultInterface, Success, Failure};
use Psr\SimpleCache\CacheInterface;

class CachedStoreRepository implements StoreRepositoryInterface
{
    private const CACHE_TTL = 300; // 5 minutes
    
    public function __construct(
        private StoreRepositoryInterface $decorated,
        private CacheInterface $cache
    ) {}
    
    public function create(string $name): ResultInterface
    {
        return $this->decorated->create($name)
            ->then(function ($store) {
                // Cache the newly created store
                $this->cache->set(
                    $this->getCacheKey($store->getId()),
                    $store,
                    self::CACHE_TTL
                );
                return $store;
            });
    }
    
    public function get(string $storeId): ResultInterface
    {
        $cacheKey = $this->getCacheKey($storeId);
        
        // Try cache first
        if ($cached = $this->cache->get($cacheKey)) {
            return Success::for($cached);
        }
        
        // Fall back to decorated repository
        return $this->decorated->get($storeId)
            ->then(function ($store) use ($cacheKey) {
                $this->cache->set($cacheKey, $store, self::CACHE_TTL);
                return $store;
            });
    }
    
    public function list(?int $pageSize = null, ?string $continuationToken = null): ResultInterface
    {
        // Lists are not cached due to pagination
        return $this->decorated->list($pageSize, $continuationToken);
    }
    
    public function delete(string $storeId): ResultInterface
    {
        return $this->decorated->delete($storeId)
            ->then(function ($result) use ($storeId) {
                // Invalidate cache on successful delete
                $this->cache->delete($this->getCacheKey($storeId));
                return $result;
            });
    }
    
    private function getCacheKey(string $storeId): string
    {
        return "openfga:store:{$storeId}";
    }
}
```

### Example: Logging Repository

```php
use OpenFGA\Repositories\TupleRepositoryInterface;
use Psr\Log\LoggerInterface;

class LoggingTupleRepository implements TupleRepositoryInterface
{
    public function __construct(
        private TupleRepositoryInterface $decorated,
        private LoggerInterface $logger
    ) {}
    
    public function write(
        StoreInterface $store,
        AuthorizationModelInterface $model,
        TupleKeysInterface $tuples,
        array $options = []
    ): ResultInterface {
        $this->logger->info('Writing tuples', [
            'store' => $store->getId(),
            'model' => $model->getId(),
            'count' => $tuples->count(),
            'options' => $options
        ]);
        
        $startTime = microtime(true);
        
        return $this->decorated->write($store, $model, $tuples, $options)
            ->success(function ($response) use ($startTime, $tuples) {
                $duration = microtime(true) - $startTime;
                $this->logger->info('Tuples written successfully', [
                    'count' => $tuples->count(),
                    'duration' => $duration
                ]);
            })
            ->failure(function ($error) use ($startTime) {
                $duration = microtime(true) - $startTime;
                $this->logger->error('Failed to write tuples', [
                    'error' => $error->getMessage(),
                    'duration' => $duration
                ]);
            });
    }
    
    // Implement other methods similarly...
}
```

### Example: Metrics Repository

```php
use OpenFGA\Repositories\ModelRepositoryInterface;
use Prometheus\CollectorRegistry;

class MetricsModelRepository implements ModelRepositoryInterface
{
    private $requestCounter;
    private $requestDuration;
    
    public function __construct(
        private ModelRepositoryInterface $decorated,
        private CollectorRegistry $registry
    ) {
        $this->requestCounter = $registry->registerCounter(
            'openfga',
            'model_repository_requests_total',
            'Total requests to model repository',
            ['method', 'status']
        );
        
        $this->requestDuration = $registry->registerHistogram(
            'openfga',
            'model_repository_request_duration_seconds',
            'Request duration in seconds',
            ['method']
        );
    }
    
    public function get(string $modelId): ResultInterface
    {
        $startTime = microtime(true);
        
        return $this->decorated->get($modelId)
            ->success(function ($model) use ($startTime) {
                $this->recordMetrics('get', 'success', $startTime);
                return $model;
            })
            ->failure(function ($error) use ($startTime) {
                $this->recordMetrics('get', 'failure', $startTime);
                throw $error;
            });
    }
    
    private function recordMetrics(string $method, string $status, float $startTime): void
    {
        $duration = microtime(true) - $startTime;
        
        $this->requestCounter->inc(['method' => $method, 'status' => $status]);
        $this->requestDuration->observe($duration, ['method' => $method]);
    }
    
    // Implement other methods...
}
```

## Best Practices

### 1. Composition Over Inheritance
Use the decorator pattern to add behavior:

```php
// Good: Compose repositories
$repository = new LoggingRepository(
    new CachedRepository(
        new HttpRepository($httpService)
    )
);

// Avoid: Extending concrete classes
class MyCachedHttpRepository extends HttpRepository { }
```

### 2. Single Responsibility
Each repository decorator should have one concern:

```php
// Good: Separate concerns
$repository = new LoggingRepository(
    new CachedRepository(
        new MetricsRepository(
            new HttpRepository($httpService)
        )
    )
);

// Avoid: Mixed concerns
class LoggingCachedMetricsRepository { }
```

### 3. Preserve Result Pattern
Always return ResultInterface and use then/success/failure:

```php
// Good: Preserve Result pattern
public function get(string $id): ResultInterface
{
    return $this->decorated->get($id)
        ->then(fn($result) => $this->processResult($result));
}

// Avoid: Breaking the pattern
public function get(string $id): mixed
{
    try {
        return $this->decorated->get($id)->unwrap();
    } catch (Exception $e) {
        throw new MyException($e);
    }
}
```

### 4. Handle Pagination Properly
Be careful with caching paginated results:

```php
public function list(?int $pageSize = null, ?string $continuationToken = null): ResultInterface
{
    // Don't cache paginated results
    // Or use sophisticated cache keys that include pagination params
    return $this->decorated->list($pageSize, $continuationToken);
}
```

## Testing with Repositories

Repositories make testing much easier:

```php
use PHPUnit\Framework\TestCase;
use OpenFGA\Repositories\StoreRepositoryInterface;
use OpenFGA\Results\Success;

class MyServiceTest extends TestCase
{
    public function testStoreCreation(): void
    {
        $mockRepository = $this->createMock(StoreRepositoryInterface::class);
        $mockRepository->method('create')
            ->with('test-store')
            ->willReturn(Success::for($this->createTestStore()));
        
        $service = new MyService($mockRepository);
        $result = $service->createStore('test-store');
        
        $this->assertTrue($result->isSuccess());
    }
}
```

## Integration with Dependency Injection

The repository pattern works well with DI containers:

```php
// Symfony example
use Symfony\Component\DependencyInjection\Reference;

$container->register(HttpStoreRepository::class)
    ->setArguments([
        new Reference(HttpServiceInterface::class),
        new Reference(SchemaValidator::class)
    ]);

$container->register(CachedStoreRepository::class)
    ->setArguments([
        new Reference(HttpStoreRepository::class),
        new Reference(CacheInterface::class)
    ])
    ->setDecoratedService(StoreRepositoryInterface::class);

$container->register(Client::class)
    ->setArguments([
        '$url' => '%openfga.url%',
        '$storeRepository' => new Reference(StoreRepositoryInterface::class)
    ]);
```

## Conclusion

The Repository pattern in the OpenFGA PHP SDK provides:

- Clean separation of concerns
- Easy testing through mocking
- Flexible extension points
- Consistent error handling via Result pattern
- Support for cross-cutting concerns

By implementing custom repositories, you can add caching, logging, metrics, or any other behavior without modifying the core SDK code.