# OpenFGA PHP SDK Improvement Plan

Based on comprehensive codebase analysis, this document outlines prioritized improvements to enhance code quality, performance, reliability, and developer experience.

## Quick Wins (High Impact, Low Effort) - Phase 1

### 1. Optimize Tuple Filtering Algorithm

**Impact**: High | **Effort**: Low | **Priority**: 1

**Current Issue**: O(n²) complexity in duplicate detection
**Location**: `src/Services/TupleFilterService.php`

**Implementation**:

```php
// Replace current nested loops with hash-based deduplication
public function filterDuplicates(TupleKeysInterface $writes, TupleKeysInterface $deletes): array
{
    $seenWrites = [];
    $seenDeletes = [];
    $filteredWrites = [];
    $filteredDeletes = [];

    foreach ($writes as $tuple) {
        $key = $this->generateTupleKey($tuple);
        if (!isset($seenWrites[$key])) {
            $seenWrites[$key] = true;
            $filteredWrites[] = $tuple;
        }
    }

    // Similar logic for deletes...
    return [$filteredWrites, $filteredDeletes];
}
```

**Deliverables**:

- [ ] Rewrite `TupleFilterService::filterDuplicates()` method
- [ ] Add unit tests for performance validation
- [ ] Update existing tests

**Estimated Time**: 1-2 days

### 2. Break Down Large Methods

**Impact**: Medium | **Effort**: Low | **Priority**: 2

**Current Issue**: Methods like `Client::writeTuples()` exceed 100 lines
**Location**: `src/Client.php:887-989`

**Implementation**:

```php
// Extract transactional logic
private function handleTransactionalWrite(/* params */): FailureInterface | SuccessInterface
{
    // Move transactional-specific logic here
}

// Extract non-transactional logic
private function handleNonTransactionalWrite(/* params */): FailureInterface | SuccessInterface
{
    // Move repository-based logic here
}

public function writeTuples(/* params */): FailureInterface | SuccessInterface
{
    return $this->withLanguageContext(function () use (/* params */) {
        if ($transactional && $this->needsTransactionalHandling($writes, $deletes)) {
            return $this->handleTransactionalWrite(/* params */);
        }

        return $this->handleNonTransactionalWrite(/* params */);
    }, 'writeTuples', $store, $model);
}
```

**Deliverables**:

- [ ] Extract `Client::writeTuples()` into smaller methods
- [ ] Extract complex logic from other large methods
- [ ] Maintain test coverage

**Estimated Time**: 2-3 days

### 3. Enhance Error Context

**Impact**: Medium | **Effort**: Low | **Priority**: 3

**Current Issue**: Exception context loss during propagation
**Location**: Various exception classes in `src/Exceptions/`

**Implementation**:

```php
// Add structured error context
class NetworkException extends ClientException
{
    public function __construct(
        string $message,
        private readonly ?HttpResponseInterface $response = null,
        private readonly array $context = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, previous: $previous);
    }

    public function getContext(): array
    {
        return [
            'response_status' => $this->response?->getStatusCode(),
            'response_headers' => $this->response?->getHeaders(),
            ...$this->context
        ];
    }
}
```

**Deliverables**:

- [ ] Add context preservation to all exception classes
- [ ] Update exception throwing sites to include context
- [ ] Add error context to logging

**Estimated Time**: 2-3 days

### 4. Use Constant-Time Comparisons

**Impact**: Low | **Effort**: Low | **Priority**: 4

**Current Issue**: Potential timing attacks on string comparisons
**Location**: Authentication-related comparisons

**Implementation**:

```php
// Replace direct string comparisons
if ($token === $expectedToken) { // Vulnerable

// With constant-time comparisons
if (hash_equals($token, $expectedToken)) { // Secure
```

**Deliverables**:

- [ ] Audit all sensitive string comparisons
- [ ] Replace with `hash_equals()`
- [ ] Add security comments

**Estimated Time**: 1 day

## High Impact Improvements - Phase 2

### 5. Implement Client Builder Pattern

**Impact**: High | **Effort**: Medium | **Priority**: 5

**Current Issue**: Complex constructor with 16+ parameters
**Location**: `src/Client.php:143-184`

**Implementation**:

```php
final class ClientBuilder
{
    private string $url;
    private ?AuthenticationInterface $authentication = null;
    private array $httpConfig = [];
    private array $serviceConfig = [];

    public static function create(string $url): self
    {
        return new self($url);
    }

    public function withAuthentication(AuthenticationInterface $auth): self
    {
        $this->authentication = $auth;
        return $this;
    }

    public function withHttpConfig(array $config): self
    {
        $this->httpConfig = array_merge($this->httpConfig, $config);
        return $this;
    }

    public function build(): ClientInterface
    {
        return new Client(/* simplified constructor */);
    }
}
```

**Usage**:

```php
$client = ClientBuilder::create('https://api.fga.example')
    ->withAuthentication(new TokenAuthentication('token'))
    ->withHttpConfig(['maxRetries' => 5])
    ->build();
```

**Deliverables**:

- [ ] Create `ClientBuilder` class
- [ ] Simplify `Client` constructor
- [ ] Update factory methods to use builder
- [ ] Add comprehensive tests
- [ ] Update documentation

**Estimated Time**: 1 week

### 6. Integrate Circuit Breaker Fully

**Impact**: High | **Effort**: Medium | **Priority**: 6

**Current Issue**: Circuit breaker exists but not integrated into main flow
**Location**: `src/Network/CircuitBreaker.php`

**Implementation**:

```php
final class RequestManagerWithCircuitBreaker implements RequestManagerInterface
{
    public function __construct(
        private readonly RequestManager $requestManager,
        private readonly CircuitBreakerInterface $circuitBreaker
    ) {}

    public function send(HttpRequestInterface $request): HttpResponseInterface
    {
        return $this->circuitBreaker->call(
            fn() => $this->requestManager->send($request)
        );
    }
}
```

**Deliverables**:

- [ ] Create circuit breaker wrapper for RequestManager
- [ ] Add per-endpoint failure tracking
- [ ] Add configuration options
- [ ] Update DI container
- [ ] Add monitoring/metrics

**Estimated Time**: 1 week

### 7. Secure Token Handling

**Impact**: High | **Effort**: Medium | **Priority**: 7

**Current Issue**: OAuth tokens stored in memory without encryption
**Location**: `src/Authentication/` classes

**Implementation**:

```php
interface SecureTokenStorageInterface
{
    public function store(string $key, string $token): void;
    public function retrieve(string $key): ?string;
    public function clear(string $key): void;
}

final class EncryptedMemoryTokenStorage implements SecureTokenStorageInterface
{
    private array $encryptedTokens = [];

    public function store(string $key, string $token): void
    {
        $this->encryptedTokens[$key] = sodium_crypto_secretbox(
            $token,
            $this->getNonce(),
            $this->getKey()
        );
    }

    public function __destruct()
    {
        // Securely clear memory
        sodium_memzero($this->encryptedTokens);
    }
}
```

**Deliverables**:

- [ ] Create secure token storage interface
- [ ] Implement encrypted memory storage
- [ ] Update authentication classes
- [ ] Add token rotation support
- [ ] Add security tests

**Estimated Time**: 1 week

### 8. Optimize Collection Performance

**Impact**: High | **Effort**: Medium | **Priority**: 8

**Current Issue**: Collections use inefficient array operations
**Location**: `src/Models/Collections/` classes

**Implementation**:

```php
abstract class OptimizedIndexedCollection implements IndexedCollectionInterface
{
    private array $items = [];
    private array $index = []; // Hash index for fast lookups

    public function contains($item): bool
    {
        $key = $this->generateKey($item);
        return isset($this->index[$key]);
    }

    public function add($item): void
    {
        $key = $this->generateKey($item);
        if (!isset($this->index[$key])) {
            $this->items[] = $item;
            $this->index[$key] = count($this->items) - 1;
        }
    }

    abstract protected function generateKey($item): string;
}
```

**Deliverables**:

- [ ] Add hash-based indexing to collections
- [ ] Implement lazy loading for expensive operations
- [ ] Add generator support for iteration
- [ ] Performance benchmarks
- [ ] Update all collection implementations

**Estimated Time**: 2 weeks

## Medium Impact Improvements - Phase 3

### 9. Standardize Retry Logic

**Impact**: Medium | **Effort**: Medium | **Priority**: 9

**Current Issue**: Inconsistent retry strategies across components

**Implementation**:

```php
final class RetryConfiguration
{
    public function __construct(
        public readonly int $maxAttempts = 3,
        public readonly float $baseDelaySeconds = 1.0,
        public readonly float $maxDelaySeconds = 30.0,
        public readonly float $jitterFactor = 0.1,
        public readonly array $retryableStatusCodes = [429, 502, 503, 504],
        public readonly array $retryableExceptions = [NetworkException::class]
    ) {}
}

interface RetryableOperation
{
    public function execute(): mixed;
    public function shouldRetry(\Throwable $exception): bool;
}
```

**Deliverables**:

- [ ] Create unified retry configuration
- [ ] Implement standardized retry wrapper
- [ ] Add jitter to prevent thundering herd
- [ ] Update all retry logic
- [ ] Add retry metrics

**Estimated Time**: 1 week

### 10. Add Performance Tests

**Impact**: Medium | **Effort**: Medium | **Priority**: 10

**Current Issue**: No performance regression detection

**Implementation**:

```php
// tests/Performance/ClientPerformanceTest.php
final class ClientPerformanceTest extends TestCase
{
    public function testBatchOperationPerformance(): void
    {
        $startTime = microtime(true);
        $memoryStart = memory_get_usage(true);

        // Execute performance-critical operation
        $result = $this->client->writeTuples(/* large dataset */);

        $duration = microtime(true) - $startTime;
        $memoryUsed = memory_get_usage(true) - $memoryStart;

        $this->assertLessThan(5.0, $duration, 'Operation should complete within 5 seconds');
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsed, 'Memory usage should be under 50MB');
    }
}
```

**Deliverables**:

- [ ] Create performance test suite
- [ ] Add memory usage tests
- [ ] Implement benchmark tracking
- [ ] Add CI performance checks
- [ ] Create performance regression alerts

**Estimated Time**: 1 week

### 11. Enhanced Input Validation

**Impact**: Medium | **Effort**: Low | **Priority**: 11

**Current Issue**: Some user inputs not fully validated

**Implementation**:

```php
final class InputValidator
{
    public static function validateStoreId(string $storeId): void
    {
        if (!preg_match('/^[A-Za-z0-9]{26}$/', $storeId)) {
            throw new ValidationException('Invalid store ID format');
        }
    }

    public static function validateTupleKey(TupleKeyInterface $tuple): void
    {
        self::validateRequired($tuple->getObject(), 'object');
        self::validateRequired($tuple->getRelation(), 'relation');
        self::validateRequired($tuple->getUser(), 'user');

        // Additional validation rules...
    }
}
```

**Deliverables**:

- [ ] Create comprehensive input validator
- [ ] Add validation to all public methods
- [ ] Implement rate limiting helpers
- [ ] Add request size validation
- [ ] Update error messages

**Estimated Time**: 3-4 days

## Long-term Improvements - Phase 4

### 12. Add Async Support

**Impact**: High | **Effort**: High | **Priority**: 12

**Current Issue**: No support for async/await patterns

**Implementation**:

```php
interface AsyncClientInterface
{
    public function checkAsync(/* params */): PromiseInterface;
    public function batchCheckAsync(/* params */): PromiseInterface;
    public function writeTuplesAsync(/* params */): PromiseInterface;
}

final class ReactPhpAsyncClient implements AsyncClientInterface
{
    public function checkAsync(/* params */): PromiseInterface
    {
        return $this->httpClient->requestAsync('POST', $url, $options)
            ->then(fn($response) => $this->parseResponse($response));
    }
}
```

**Deliverables**:

- [ ] Design async interfaces
- [ ] Implement ReactPHP integration
- [ ] Add promise-based APIs
- [ ] Create async examples
- [ ] Performance comparisons

**Estimated Time**: 3-4 weeks

### 13. Implement Connection Pooling

**Impact**: Medium | **Effort**: High | **Priority**: 13

**Current Issue**: No HTTP connection reuse strategy

**Implementation**:

```php
final class PooledHttpClient implements HttpClientInterface
{
    private array $connectionPools = [];

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $pool = $this->getPoolForHost($request->getUri()->getHost());
        return $pool->execute($request);
    }

    private function getPoolForHost(string $host): ConnectionPool
    {
        return $this->connectionPools[$host] ??= new ConnectionPool($host, $this->config);
    }
}
```

**Deliverables**:

- [ ] Design connection pooling interfaces
- [ ] Implement pool management
- [ ] Add configuration options
- [ ] Monitor connection health
- [ ] Add pool metrics

**Estimated Time**: 2-3 weeks

### 14. Increase Test Coverage

**Impact**: High | **Effort**: High | **Priority**: 14

**Current Issue**: Insufficient test coverage across the codebase

**Strategy**:

- Target 95%+ line coverage
- Add property-based testing
- Implement mutation testing
- Create comprehensive integration tests

**Deliverables**:

- [ ] Audit current test coverage
- [ ] Add missing unit tests
- [ ] Implement property-based tests
- [ ] Add mutation testing
- [ ] Create integration test scenarios
- [ ] Add chaos engineering tests

**Estimated Time**: 4-6 weeks

## Implementation Timeline

### Quarter 1: Foundation & Quick Wins

- Week 1-2: Phase 1 (Quick Wins)
- Week 3-4: Client Builder Pattern
- Week 5-6: Circuit Breaker Integration
- Week 7-8: Secure Token Handling
- Week 9-12: Collection Performance Optimization

### Quarter 2: Reliability & Performance

- Week 1-2: Standardize Retry Logic
- Week 3-4: Performance Testing
- Week 5-6: Enhanced Input Validation
- Week 7-12: Begin Async Support Implementation

### Quarter 3: Advanced Features

- Week 1-8: Complete Async Support
- Week 9-12: Connection Pooling

### Quarter 4: Quality & Coverage

- Week 1-12: Comprehensive Test Coverage Improvement

## Success Metrics

### Performance Targets

- [ ] Reduce tuple filtering from O(n²) to O(n)
- [ ] Improve batch operation performance by 50%
- [ ] Reduce memory usage by 30%
- [ ] Achieve 99.9% uptime with circuit breaker

### Quality Targets

- [ ] Increase test coverage to 95%+
- [ ] Zero security vulnerabilities
- [ ] Reduce complexity in large methods by 70%
- [ ] Improve error message clarity

### Developer Experience

- [ ] Reduce client instantiation complexity
- [ ] Add 10+ new usage examples
- [ ] Achieve 100% API documentation coverage
- [ ] Improve getting started time by 50%

## Risk Mitigation

### Breaking Changes

- Implement builder pattern alongside existing constructors
- Use feature flags for new functionality
- Maintain backward compatibility for one major version

### Performance Regressions

- Implement comprehensive benchmarks
- Add performance CI checks
- Create rollback procedures

### Security Issues

- Regular security audits
- Automated vulnerability scanning
- Penetration testing for sensitive components

## Dependencies & Prerequisites

### External Dependencies

- PHP 8.3+ (for advanced type features)
- Async library (ReactPHP or Swoole)
- Encryption library (libsodium)
- Performance monitoring tools

### Internal Prerequisites

- Updated CI/CD pipeline
- Enhanced testing infrastructure
- Documentation generation tools
- Security scanning integration

---

This plan provides a structured approach to improving the OpenFGA PHP SDK while maintaining its current high quality and comprehensive feature set. Each phase builds upon the previous one, ensuring steady progress toward a more performant, secure, and developer-friendly SDK.
