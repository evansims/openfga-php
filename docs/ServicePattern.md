# Service Pattern Guide

The OpenFGA PHP SDK uses the Service pattern to encapsulate business logic and provide focused, testable components. This guide explains the service architecture and how to extend it.

## Overview

Services in the SDK handle specific domains of functionality:
- `AuthorizationService` - Authorization queries and checks
- `HttpService` - HTTP request/response management
- `TupleFilterService` - Duplicate tuple filtering
- `ValidationService` - Data validation

Each service follows the Single Responsibility Principle and can be extended or replaced through dependency injection.

## Core Services

### AuthorizationService

Handles all authorization-related operations.

```php
interface AuthorizationServiceInterface
{
    public function check(
        CheckRequest $request
    ): FailureInterface|SuccessInterface;
    
    public function expand(
        ExpandRequest $request
    ): FailureInterface|SuccessInterface;
    
    public function listObjects(
        ListObjectsRequest $request
    ): FailureInterface|SuccessInterface;
    
    public function listUsers(
        ListUsersRequest $request
    ): FailureInterface|SuccessInterface;
    
    public function batchCheck(
        BatchCheckRequest $request
    ): FailureInterface|SuccessInterface;
}
```

#### Usage Example

```php
use OpenFGA\Services\AuthorizationService;
use OpenFGA\Requests\CheckRequest;

$authService = new AuthorizationService($httpService);

$request = new CheckRequest(
    store: 'store-id',
    model: 'model-id',
    tupleKey: $tupleKey
);

$result = $authService->check($request);

$result->success(function ($response) {
    if ($response->getAllowed()) {
        echo "Access granted!";
    }
});
```

### HttpService

Manages HTTP communication and provides access to request/response history.

```php
interface HttpServiceInterface
{
    public function send(
        RequestInterface $request
    ): ResponseInterface;
    
    public function getLastRequest(): ?HttpRequestInterface;
    
    public function getLastResponse(): ?HttpResponseInterface;
}
```

#### Usage Example

```php
use OpenFGA\Services\HttpService;

$httpService = new HttpService($requestManager, $eventDispatcher);

$response = $httpService->send($request);

// Access last request for debugging
$lastRequest = $httpService->getLastRequest();
```

### TupleFilterService

Filters duplicate tuples based on business rules.

```php
interface TupleFilterServiceInterface
{
    public function filterDuplicateTuples(
        TupleKeysInterface $tuples
    ): TupleKeysInterface;
}
```

#### Usage Example

```php
use OpenFGA\Services\TupleFilterService;
use OpenFGA\Models\Collections\TupleKeys;

$filterService = new TupleFilterService();

$filtered = $filterService->filterDuplicateTuples($tuples);
// Duplicates removed, more specific relations preserved
```

### ValidationService

Validates data against JSON schemas.

```php
interface ValidationServiceInterface
{
    public function validate(
        mixed $data,
        SchemaInterface $schema
    ): void;
    
    public function registerSchema(
        string $class,
        SchemaInterface $schema
    ): void;
}
```

## Implementing Custom Services

### Example: Caching Authorization Service

```php
use OpenFGA\Services\AuthorizationServiceInterface;
use OpenFGA\Requests\CheckRequest;
use OpenFGA\Results\{Success, FailureInterface, SuccessInterface};
use Psr\SimpleCache\CacheInterface;

class CachingAuthorizationService implements AuthorizationServiceInterface
{
    private const CACHE_TTL = 60; // 1 minute
    
    public function __construct(
        private AuthorizationServiceInterface $decorated,
        private CacheInterface $cache
    ) {}
    
    public function check(CheckRequest $request): FailureInterface|SuccessInterface
    {
        $cacheKey = $this->buildCacheKey('check', $request);
        
        // Try cache first
        if ($cached = $this->cache->get($cacheKey)) {
            return Success::for($cached);
        }
        
        // Delegate to decorated service
        return $this->decorated->check($request)
            ->then(function ($response) use ($cacheKey) {
                // Cache successful responses
                $this->cache->set($cacheKey, $response, self::CACHE_TTL);
                return $response;
            });
    }
    
    private function buildCacheKey(string $operation, RequestInterface $request): string
    {
        // Build a unique cache key based on request parameters
        $params = [
            'op' => $operation,
            'store' => $request->getStore(),
            'model' => $request->getModel(),
            // Add other relevant parameters
        ];
        
        return 'openfga:' . md5(serialize($params));
    }
    
    // Implement other methods...
}
```

### Example: Rate Limiting Service

```php
use OpenFGA\Services\HttpServiceInterface;
use OpenFGA\Exceptions\NetworkException;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

class RateLimitingHttpService implements HttpServiceInterface
{
    private array $buckets = [];
    
    public function __construct(
        private HttpServiceInterface $decorated,
        private int $requestsPerSecond = 10
    ) {}
    
    public function send(RequestInterface $request): ResponseInterface
    {
        $this->enforceRateLimit();
        
        return $this->decorated->send($request);
    }
    
    private function enforceRateLimit(): void
    {
        $now = microtime(true);
        $window = floor($now);
        
        // Initialize bucket for current window
        if (!isset($this->buckets[$window])) {
            $this->buckets[$window] = 0;
            // Clean old buckets
            $this->cleanOldBuckets($window);
        }
        
        // Check rate limit
        if ($this->buckets[$window] >= $this->requestsPerSecond) {
            $waitTime = $window + 1 - $now;
            usleep((int)($waitTime * 1_000_000));
            
            // Retry in next window
            return $this->send($request);
        }
        
        $this->buckets[$window]++;
    }
    
    private function cleanOldBuckets(float $currentWindow): void
    {
        foreach (array_keys($this->buckets) as $window) {
            if ($window < $currentWindow - 2) {
                unset($this->buckets[$window]);
            }
        }
    }
    
    // Implement other methods...
}
```

### Example: Telemetry Service Decorator

```php
use OpenFGA\Services\AuthorizationServiceInterface;
use OpenFGA\Observability\TelemetryInterface;

class TelemetryAuthorizationService implements AuthorizationServiceInterface
{
    public function __construct(
        private AuthorizationServiceInterface $decorated,
        private TelemetryInterface $telemetry
    ) {}
    
    public function check(CheckRequest $request): FailureInterface|SuccessInterface
    {
        $span = $this->telemetry->startSpan('authorization.check', [
            'store.id' => $request->getStore(),
            'model.id' => $request->getModel(),
        ]);
        
        $startTime = microtime(true);
        
        try {
            return $this->decorated->check($request)
                ->success(function ($response) use ($span, $startTime) {
                    $this->telemetry->endSpan($span, [
                        'allowed' => $response->getAllowed(),
                        'duration_ms' => (microtime(true) - $startTime) * 1000,
                    ]);
                    return $response;
                })
                ->failure(function ($error) use ($span, $startTime) {
                    $this->telemetry->endSpan($span, [
                        'error' => true,
                        'error.message' => $error->getMessage(),
                        'duration_ms' => (microtime(true) - $startTime) * 1000,
                    ]);
                    throw $error;
                });
        } catch (\Throwable $e) {
            $this->telemetry->endSpan($span, ['error' => true]);
            throw $e;
        }
    }
    
    // Implement other methods...
}
```

## Service Composition

Services can be composed using the decorator pattern:

```php
// Base service
$httpService = new HttpService($requestManager, $validator);

// Add rate limiting
$rateLimitedHttp = new RateLimitingHttpService($httpService, 100);

// Create authorization service
$authService = new AuthorizationService($rateLimitedHttp);

// Add caching
$cachedAuth = new CachingAuthorizationService($authService, $cache);

// Add telemetry
$telemetryAuth = new TelemetryAuthorizationService($cachedAuth, $telemetry);

// Use in client
$client = new Client(
    url: $config['url'],
    authorizationService: $telemetryAuth
);
```

## Testing with Services

Services make unit testing straightforward:

```php
use PHPUnit\Framework\TestCase;
use OpenFGA\Services\AuthorizationServiceInterface;
use OpenFGA\Requests\CheckRequest;
use OpenFGA\Results\Success;

class MyApplicationServiceTest extends TestCase
{
    public function testAccessControl(): void
    {
        // Create mock
        $mockAuth = $this->createMock(AuthorizationServiceInterface::class);
        
        // Set expectations
        $mockAuth->expects($this->once())
            ->method('check')
            ->with($this->callback(function (CheckRequest $request) {
                return $request->getStore() === 'test-store'
                    && $request->getTupleKey()->getUser() === 'user:anne';
            }))
            ->willReturn(Success::for(new CheckResponse(['allowed' => true])));
        
        // Test your service
        $appService = new MyApplicationService($mockAuth);
        $result = $appService->canUserAccessResource('anne', 'resource-123');
        
        $this->assertTrue($result);
    }
}
```

## Best Practices

### 1. Interface Dependency
Always depend on interfaces, not concrete implementations:

```php
// Good
public function __construct(
    private AuthorizationServiceInterface $authService
) {}

// Avoid
public function __construct(
    private AuthorizationService $authService
) {}
```

### 2. Single Responsibility
Each service should have one clear responsibility:

```php
// Good: Focused services
class CachingService implements AuthorizationServiceInterface {}
class LoggingService implements AuthorizationServiceInterface {}

// Avoid: Combined responsibilities
class CachingLoggingMetricsService implements AuthorizationServiceInterface {}
```

### 3. Preserve Result Pattern
Always return the same type as the interface defines:

```php
// Good: Preserve Result pattern
public function check(CheckRequest $request): FailureInterface|SuccessInterface
{
    return $this->decorated->check($request)
        ->then(fn($response) => $this->processResponse($response));
}

// Avoid: Breaking the contract
public function check(CheckRequest $request): bool
{
    return $this->decorated->check($request)->unwrap()->getAllowed();
}
```

### 4. Stateless Services
Keep services stateless when possible:

```php
// Good: Stateless
class LoggingService
{
    public function __construct(private LoggerInterface $logger) {}
    
    public function check(CheckRequest $request): ResultInterface
    {
        $this->logger->info('Checking', ['request' => $request]);
        return $this->decorated->check($request);
    }
}

// Avoid: Stateful
class CountingService
{
    private int $checkCount = 0;
    
    public function check(CheckRequest $request): ResultInterface
    {
        $this->checkCount++; // State makes testing harder
        return $this->decorated->check($request);
    }
}
```

## Integration Examples

### Laravel Service Provider

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenFGA\Services\{AuthorizationService, AuthorizationServiceInterface};
use App\Services\{CachingAuthorizationService, LoggingAuthorizationService};

class OpenFGAServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register base service
        $this->app->bind(
            AuthorizationService::class,
            fn($app) => new AuthorizationService(
                $app->make(HttpServiceInterface::class)
            )
        );
        
        // Register decorators
        $this->app->extend(AuthorizationService::class, fn($service, $app) =>
            new LoggingAuthorizationService($service, $app->make('log'))
        );
        
        $this->app->extend(AuthorizationService::class, fn($service, $app) =>
            new CachingAuthorizationService($service, $app->make('cache.store'))
        );
        
        // Bind interface
        $this->app->bind(
            AuthorizationServiceInterface::class,
            AuthorizationService::class
        );
    }
}
```

### Symfony Configuration

```yaml
# config/services.yaml
services:
    OpenFGA\Services\AuthorizationService:
        arguments:
            - '@OpenFGA\Services\HttpServiceInterface'

    App\Services\CachingAuthorizationService:
        decorates: OpenFGA\Services\AuthorizationService
        arguments:
            - '@.inner'
            - '@cache.app'

    App\Services\LoggingAuthorizationService:
        decorates: App\Services\CachingAuthorizationService
        arguments:
            - '@.inner'
            - '@logger'

    OpenFGA\Services\AuthorizationServiceInterface:
        alias: App\Services\LoggingAuthorizationService
```

## Conclusion

The Service pattern in the OpenFGA PHP SDK provides:

- Clear separation of concerns
- Easy testing through dependency injection
- Flexible extension through decoration
- Consistent interfaces for all operations
- Support for cross-cutting concerns like caching, logging, and telemetry

By implementing custom services, you can add behavior specific to your application's needs without modifying the core SDK.