# OpenFGA PHP SDK - Architecture Overview

The OpenFGA PHP SDK follows clean architecture principles with clear separation of concerns, dependency injection, and interface-first design. This document describes the architectural decisions, patterns, and organization of the SDK.

## Table of Contents

- [Core Principles](#core-principles)
- [Architecture Layers](#architecture-layers)
- [Directory Structure](#directory-structure)
- [Dependency Rules](#dependency-rules)
- [Key Components](#key-components)
- [Design Patterns](#design-patterns)
- [Extension Points](#extension-points)
- [Migration Summary](#migration-summary)

## Core Principles

### 1. Clean Architecture
The SDK follows Uncle Bob's Clean Architecture with clear dependency rules and layer boundaries that ensure maintainability and testability.

### 2. Interface-First Design
Every major class has a corresponding interface, enabling loose coupling and easy testing:
- `ClientInterface` for the main client
- Service interfaces for all business logic
- Repository interfaces for data access
- Infrastructure interfaces for external dependencies

### 3. Dependency Injection
All dependencies are injected through constructors, making the code testable and flexible through the `ServiceProvider` pattern.

### 4. Result Pattern
All operations return `ResultInterface` for safe error handling without exceptions:

```php
$result = $client->check($store, $model, $tupleKey);

$result
    ->success(fn($response) => echo "Allowed: " . $response->getAllowed())
    ->failure(fn($error) => echo "Error: " . $error->getMessage());
```

### 5. Single Responsibility Principle
Each class has one reason to change, with services focused on specific domains.

## Architecture Layers

```
┌─────────────────────────────────────────────────────────────────┐
│                        Presentation Layer                        │
│                         (API/SDK Interface)                      │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ • Client (Facade)                                        │   │
│  │ • ClientInterface                                        │   │
│  │ • Request/Response DTOs                                  │   │
│  │ • Configuration                                          │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                                  ↓
┌─────────────────────────────────────────────────────────────────┐
│                        Application Layer                         │
│                      (Business Use Cases)                        │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ • Services (Authorization, Store, Model, Tuple, etc.)    │   │
│  │ • Service Interfaces                                     │   │
│  │ • Repository Interfaces                                  │   │
│  │ • Result Pattern (Success/Failure)                       │   │
│  │ • Request/Response DTOs                                  │   │
│  │ • Events and Event Dispatcher                           │   │
│  │ • Factory Interfaces                                     │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                                  ↓
┌─────────────────────────────────────────────────────────────────┐
│                         Domain Layer                             │
│                    (Business Entities & Rules)                   │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ • Models (Store, Tuple, AuthorizationModel, etc.)        │   │
│  │ • Model Interfaces                                       │   │
│  │ • Collections                                            │   │
│  │ • Enums (Consistency, SchemaVersion, etc.)              │   │
│  │ • Schema Validation                                      │   │
│  │ • Language Processing (DSL Transformer)                 │   │
│  │ • Exception Hierarchy                                    │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                                  ↓
┌─────────────────────────────────────────────────────────────────┐
│                      Infrastructure Layer                        │
│                     (External Dependencies)                      │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ • Repository Implementations (HTTP-based)               │   │
│  │ • Network (RequestManager, HttpClient, RetryStrategy)    │   │
│  │ • Authentication Implementations                         │   │
│  │ • Observability (Telemetry Providers)                    │   │
│  │ • Translation System                                     │   │
│  │ • Service Implementations (Config, HTTP, etc.)          │   │
│  │ • Integration Points (Service Provider)                  │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

## Directory Structure

```
src/
├── Application/            # Application Layer
│   ├── DependencyInjection/    # Service container
│   ├── Events/                 # Event system
│   ├── Factory/                # Object creation
│   ├── Http/                   # HTTP abstractions
│   ├── Repositories/           # Repository interfaces
│   ├── Requests/               # Request DTOs
│   ├── Responses/              # Response DTOs
│   ├── Results/                # Result pattern
│   ├── Services/               # Service interfaces
│   └── Telemetry/              # Telemetry abstractions
├── Domain/                 # Domain Layer
│   ├── Exceptions/             # Exception hierarchy
│   ├── Language/               # DSL processing
│   └── Schema/                 # Schema validation
├── Infrastructure/         # Infrastructure Layer
│   ├── Observability/          # Telemetry providers
│   ├── Repositories/           # HTTP repositories
│   ├── Services/               # Infrastructure services
│   ├── Telemetry/              # Event listeners
│   └── Translation/            # I18n implementation
├── Models/                 # Domain Models
│   ├── Collections/            # Type-safe collections
│   └── Enums/                  # Enumeration types
├── Network/                # Network Infrastructure
├── Authentication/         # Authentication Implementations
├── Observability/          # Legacy telemetry (to be migrated)
├── Translation/            # Legacy translation (to be migrated)
├── Integration/            # Framework integration
├── DI/                     # Dependency injection (new location)
├── Events/                 # Event system (new location)
├── Factories/              # Factory implementations
├── Language/               # DSL transformer (new location)
├── Repositories/           # Repository implementations (new location)
├── Requests/               # Request DTOs (new location)
├── Responses/              # Response DTOs (new location)
├── Results/                # Result implementations (new location)
├── Schemas/                # Schema validation (new location)
├── Services/               # Service implementations (new location)
└── Client.php              # Main client facade
```

## Dependency Rules

### The Dependency Rule
Dependencies must point inward. Nothing in an inner circle can know anything about something in an outer circle.

1. **Domain Layer** (innermost)
   - Has NO dependencies on other layers
   - Contains pure business entities and rules
   - Examples: `Store`, `Tuple`, `AuthorizationModel`, DSL transformer

2. **Application Layer**
   - Depends only on Domain Layer
   - Contains business logic and use cases
   - Examples: `AuthorizationService`, `TupleService`, event system

3. **Infrastructure Layer**
   - Depends on Domain and Application layers
   - Contains implementation details
   - Examples: `HttpTupleRepository`, `RequestManager`, authentication

4. **Presentation Layer** (outermost)
   - Depends on all inner layers
   - Contains API/SDK interface
   - Examples: `Client`, configuration

## Key Components

### Client (Presentation Layer)
The main SDK interface that provides a unified API for all OpenFGA operations. Acts as a facade that delegates to specialized services while maintaining backward compatibility.

### Services (Application Layer)

#### AuthorizationService
Handles all authorization-related queries:
- `check()` - Check if a user has a relationship
- `expand()` - Expand a relationship tree
- `listObjects()` - List objects a user has access to
- `listUsers()` - List users with access to an object
- `batchCheck()` - Check multiple relationships at once

#### Business Domain Services
- `TupleService` - Tuple lifecycle management
- `StoreService` - Store operations
- `ModelService` - Authorization model management
- `AssertionService` - Model assertions

### Repositories (Infrastructure Layer)
Data access layer with HTTP implementations:
- `HttpTupleRepository` - Tuple CRUD operations
- `HttpStoreRepository` - Store management
- `HttpModelRepository` - Authorization model operations
- `HttpAssertionRepository` - Assertion operations

### Network Layer (Infrastructure)
Low-level HTTP handling with configurable strategies:
- `RequestManager` - Orchestrates HTTP requests
- `HttpClientInterface` - PSR-18 client abstraction
- `RetryHandler` - Exponential backoff with jitter
- `ConcurrentExecutor` - Parallel request execution (Fiber-based)

## Design Patterns

### 1. Facade Pattern
The `Client` class serves as a simplified interface to the complex subsystem of services and repositories.

### 2. Repository Pattern
Abstracts data access through interfaces, with HTTP implementations in the infrastructure layer.

### 3. Service Layer Pattern
Business logic is encapsulated in service classes that orchestrate between repositories.

### 4. Result Pattern
All operations return `Success` or `Failure` objects instead of throwing exceptions, enabling functional error handling.

### 5. Strategy Pattern
Configurable strategies for retries, authentication, and HTTP clients.

### 6. Observer Pattern
Event system for cross-cutting concerns like telemetry and logging.

### 7. Factory Pattern
Object creation abstracted through factory interfaces and implementations.

### 8. Dependency Injection
Constructor injection throughout, with service provider for wiring.

## Extension Points

### Custom Services
Implement service interfaces to add custom behavior:

```php
class LoggingAuthorizationService implements AuthorizationServiceInterface
{
    public function __construct(
        private AuthorizationServiceInterface $wrapped,
        private LoggerInterface $logger
    ) {}
    
    public function check(/* ... */): ResultInterface
    {
        $this->logger->info('Checking authorization', $params);
        return $this->wrapped->check(/* ... */);
    }
}
```

### Custom Repositories
Implement repository interfaces for caching, metrics, or alternative backends:

```php
class CachedStoreRepository implements StoreRepositoryInterface
{
    public function __construct(
        private StoreRepositoryInterface $wrapped,
        private CacheInterface $cache
    ) {}
    
    public function get(string $storeId): ResultInterface
    {
        $cacheKey = "store:$storeId";
        
        if ($cached = $this->cache->get($cacheKey)) {
            return Success::for($cached);
        }
        
        return $this->wrapped->get($storeId)
            ->then(fn($store) => $this->cache->set($cacheKey, $store, 300));
    }
}
```

### Event-Driven Extensions
Subscribe to events for cross-cutting concerns:

```php
$eventDispatcher->subscribe(OperationCompletedEvent::class, function($event) {
    $this->metrics->recordLatency($event->getDuration());
});
```

## Migration Summary

The SDK underwent a significant architectural refactoring to implement clean architecture principles:

### What Changed
- **Directory Structure**: Reorganized into clear architectural layers
- **Namespace Updates**: Moved classes to appropriate layer namespaces
- **Dependency Injection**: Introduced service provider pattern
- **Interface Segregation**: Created interfaces for all major components
- **Event System**: Added event dispatcher for cross-cutting concerns

### What Stayed the Same
- **Public API**: Complete backward compatibility maintained
- **Client Interface**: All existing methods work unchanged
- **Functionality**: All features preserved and enhanced
- **Performance**: Improved through better separation and concurrent execution

### Benefits Achieved
1. **Testability**: Each layer can be tested independently
2. **Maintainability**: Clear boundaries and responsibilities
3. **Flexibility**: Easy to swap implementations
4. **Extensibility**: Multiple extension points for customization
5. **Performance**: Optimized network layer with retry strategies

## Future Enhancements

The architecture supports future enhancements:
- Event-driven telemetry via event dispatchers ✅
- Dependency injection container integration ✅
- Additional repository implementations (GraphQL, gRPC)
- Middleware pipeline for request/response processing
- CQRS pattern for read/write separation
- Domain events for better decoupling

## Best Practices

1. **Always code against interfaces** - Use service and repository interfaces in application code
2. **Follow dependency rules** - Inner layers should not depend on outer layers
3. **Use the Result pattern** - Handle errors functionally without exceptions
4. **Inject dependencies** - Use constructor injection for all dependencies
5. **Separate concerns** - Keep business logic separate from infrastructure details
6. **Test each layer** - Write focused tests for each architectural layer