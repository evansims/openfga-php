# Exception Handling Guide

This guide explains how to properly handle errors in the OpenFGA PHP SDK using our Result type system and enum-based exceptions.

## Overview

The OpenFGA PHP SDK uses a modern approach to error handling that emphasizes type safety, predictability, and internationalization support. Instead of throwing exceptions for expected error cases, we return `Result` types that explicitly model success and failure states.

### Philosophy

Our error handling philosophy is built on three core principles:

1. **Explicit over implicit** - Errors are part of the return type, not hidden exceptions
2. **Type-safe over dynamic** - Use enums for error types, not string comparisons
3. **Composable over nested** - Chain operations with Result methods, not try/catch blocks

### Why Result Types?

Traditional exception handling has several drawbacks:

```php
// ❌ Traditional approach - errors are hidden
try {
    $response = $client->check($user, $relation, $object);
    // How do we know what errors this might throw?
} catch (Exception $e) {
    // Generic catch loses type information
}
```

Our Result type makes errors explicit:

```php
// ✅ Result approach - errors are visible in the type signature
$result = $client->check(
    user: 'user:anne',
    relation: 'reader', 
    object: 'document:budget'
);

// The type system tells us this returns Result<CheckResponse, ClientThrowable>
```

## Using the Result Type

The `Result` type represents either a successful value or a failure. It provides a rich API for handling both cases elegantly.

### Basic Usage

```php
use OpenFGA\Client;

$client = new Client(/* ... */);

// All SDK methods return Result types
$result = $client->check(
    store: $storeId,
    tupleKey: tuple('user:anne', 'reader', 'document:budget')
);

// Check if the operation succeeded
if ($result->succeeded()) {
    $response = $result->unwrap();
    echo "Access allowed: " . ($response->getAllowed() ? 'Yes' : 'No');
}

// Or check if it failed
if ($result->failed()) {
    $error = $result->err();
    echo "Operation failed: " . $error->getMessage();
}
```

### Chaining Operations

Result types support fluent chaining for elegant error handling:

```php
$result = $client->check(/* ... */)
    ->success(function ($response) {
        // Optional side effect on success
        logger()->info('Authorization check completed', [
            'allowed' => $response->getAllowed()
        ]);
    })
    ->failure(function ($error) {
        // Optional side effect on failure
        logger()->error('Authorization check failed', [
            'error' => $error->getMessage()
        ]);
    })
    ->then(function ($response) {
        // Transform successful value
        return $response->getAllowed() ? 'GRANTED' : 'DENIED';
    })
    ->recover(function ($error) {
        // Recover from specific errors
        if ($error instanceof NetworkException) {
            return 'UNKNOWN'; // Fail open on network errors
        }
        throw $error; // Re-throw other errors
    });

// Get the final value
$accessStatus = $result->unwrap(); // 'GRANTED', 'DENIED', or 'UNKNOWN'
```

### Unwrapping Patterns

There are several ways to extract values from Results:

```php
// 1. Simple unwrap - throws on failure
$response = $result->unwrap();

// 2. Unwrap with default - never throws
$response = $result->unwrap(fn() => new CheckResponse(['allowed' => false]));

// 3. Unwrap or throw custom exception
$response = $result->unwrapOr(fn($error) => 
    throw new MyCustomException("Check failed: " . $error->getMessage())
);

// 4. Pattern matching (PHP 8.3+)
$allowed = match(true) {
    $result->succeeded() => $result->unwrap()->getAllowed(),
    $result->failed() => false, // Default to denied on error
};
```

### Error Propagation

Use `rethrow()` to convert Result failures back to exceptions when needed:

```php
public function canUserRead(string $userId, string $documentId): bool
{
    return $this->client->check(
        store: $this->storeId,
        tupleKey: tuple($userId, 'reader', $documentId)
    )
    ->rethrow() // Throws the underlying exception if failed
    ->unwrap()
    ->getAllowed();
}
```

## Enum-Based Exception System

The SDK uses enum-backed exceptions for type-safe error handling. Each exception type extends from specific enum cases:

### Exception Hierarchy

```
ClientThrowable (interface)
├── ClientException
│   └── Backed by ClientError enum:
│       ├── Validation
│       ├── Configuration
│       ├── Authentication
│       ├── Network
│       └── Serialization
├── NetworkException
│   └── Backed by NetworkError enum:
│       ├── Timeout
│       ├── Conflict
│       ├── Forbidden
│       ├── Invalid
│       ├── Request
│       ├── Server
│       ├── Unauthenticated
│       ├── UndefinedEndpoint
│       └── Unexpected
├── AuthenticationException
│   └── Backed by AuthenticationError enum:
│       ├── TokenExpired
│       └── TokenInvalid
├── ConfigurationException
│   └── Backed by ConfigurationError enum:
│       ├── HttpClientMissing
│       ├── HttpRequestFactoryMissing
│       ├── HttpResponseFactoryMissing
│       └── HttpStreamFactoryMissing
└── SerializationException
    └── Backed by SerializationError enum:
        ├── Response
        ├── MissingRequiredParam
        ├── InvalidItemType
        ├── UndefinedItemType
        ├── EmptyCollection
        └── CouldNotAddItems
```

### When to Use Each Exception Type

#### ClientException
General client-side errors that don't fit other categories:

```php
use OpenFGA\Exceptions\ClientError;

// Validation errors
throw ClientError::Validation->exception(context: [
    'message' => 'Store ID cannot be empty'
]);

// Generic client errors
throw ClientError::Network->exception(context: [
    'message' => 'Unable to connect to OpenFGA'
]);
```

#### NetworkException
HTTP and network-related errors:

```php
use OpenFGA\Exceptions\NetworkError;

// From HTTP status codes
$error = match($statusCode) {
    400 => NetworkError::Invalid,
    401 => NetworkError::Unauthenticated,
    403 => NetworkError::Forbidden,
    404 => NetworkError::UndefinedEndpoint,
    409 => NetworkError::Conflict,
    422 => NetworkError::Timeout,
    500 => NetworkError::Server,
    default => NetworkError::Unexpected,
};

throw new NetworkException(
    kind: $error,
    request: $request,
    response: $response
);
```

#### AuthenticationException
OAuth/token-related errors:

```php
use OpenFGA\Exceptions\AuthenticationError;

// Token validation
if ($token->isExpired()) {
    throw AuthenticationError::TokenExpired->exception();
}

if (!$token->isValid()) {
    throw AuthenticationError::TokenInvalid->exception();
}
```

#### ConfigurationException
Setup and configuration errors:

```php
use OpenFGA\Exceptions\ConfigurationError;

// Missing dependencies
if ($httpClient === null) {
    throw ConfigurationError::HttpClientMissing->exception();
}
```

#### SerializationException
JSON encoding/decoding and data transformation errors:

```php
use OpenFGA\Exceptions\SerializationError;

// Invalid response data
if (!is_array($data)) {
    throw SerializationError::Response->exception(context: [
        'message' => 'Expected array, got ' . gettype($data)
    ]);
}
```

## Pattern Matching with `match`

PHP 8.3's match expression provides elegant error handling:

### Basic Pattern Matching

```php
$result = $client->check(/* ... */);

$accessLevel = match(true) {
    $result->failed() => 'ERROR',
    $result->unwrap()->getAllowed() => 'ALLOWED',
    default => 'DENIED',
};
```

### Matching on Exception Types

```php
$result->failure(function ($error) {
    $response = match($error::class) {
        NetworkException::class => $this->handleNetworkError($error),
        AuthenticationException::class => $this->refreshTokenAndRetry(),
        ClientException::class => $this->logClientError($error),
        default => throw $error,
    };
});
```

### Matching on Error Enums

```php
use OpenFGA\Exceptions\NetworkError;

$result->failure(function ($error) {
    if ($error instanceof NetworkException) {
        $action = match($error->kind) {
            NetworkError::Timeout => $this->retry(),
            NetworkError::Unauthenticated => $this->authenticate(),
            NetworkError::Forbidden => $this->requestAccess(),
            NetworkError::Server => $this->notifyOps(),
            default => $this->logError($error),
        };
    }
});
```

### Exhaustive Matching

Use match for exhaustive error handling:

```php
use OpenFGA\Exceptions\ClientError;

public function translateError(ClientException $error): string
{
    return match($error->kind) {
        ClientError::Validation => 'Invalid input provided',
        ClientError::Configuration => 'SDK is not properly configured',
        ClientError::Authentication => 'Authentication failed',
        ClientError::Network => 'Network error occurred',
        ClientError::Serialization => 'Data format error',
        // No default - ensures all cases are handled
    };
}
```

### Benefits Over if/else

```php
// ❌ Verbose if/else chains
if ($error instanceof NetworkException) {
    if ($error->kind === NetworkError::Timeout) {
        return $this->retry();
    } elseif ($error->kind === NetworkError::Unauthenticated) {
        return $this->authenticate();
    } else {
        return $this->logError($error);
    }
}

// ✅ Concise match expression
return match([$error::class, $error->kind ?? null]) {
    [NetworkException::class, NetworkError::Timeout] => $this->retry(),
    [NetworkException::class, NetworkError::Unauthenticated] => $this->authenticate(),
    [NetworkException::class, $_] => $this->logError($error),
    default => throw $error,
};
```

## Anti-Patterns to Avoid

### ❌ String Comparison

Never compare error messages as strings:

```php
// ❌ BAD - Breaks with i18n, brittle
try {
    $client->check(/* ... */);
} catch (Exception $e) {
    if ($e->getMessage() === 'Store not found') {
        // This breaks when messages are translated!
    }
}

// ✅ GOOD - Type-safe enum comparison
$result = $client->check(/* ... */);
$result->failure(function ($error) {
    if ($error instanceof NetworkException && 
        $error->kind === NetworkError::UndefinedEndpoint) {
        // Handle store not found
    }
});
```

### ❌ Catching Generic Exception

Avoid catching overly broad exception types:

```php
// ❌ BAD - Catches everything, loses type information
try {
    $response = $client->check(/* ... */)->unwrap();
} catch (Exception $e) {
    logger()->error('Something went wrong');
}

// ✅ GOOD - Handle specific error types
$client->check(/* ... */)
    ->failure(function ($error) {
        match($error::class) {
            NetworkException::class => logger()->error('Network error', ['kind' => $error->kind]),
            AuthenticationException::class => logger()->error('Auth error'),
            default => logger()->error('Unexpected error', ['type' => $error::class]),
        };
    });
```

### ❌ Ignoring Result Types

Never ignore the Result wrapper:

```php
// ❌ BAD - Ignores potential failures
$response = $client->check(/* ... */)->unwrap(); // Throws on failure!

// ✅ GOOD - Handle both success and failure
$result = $client->check(/* ... */);
if ($result->succeeded()) {
    $response = $result->unwrap();
    // Use response
} else {
    // Handle error appropriately
}
```

### ❌ String-Based Error Handling

Avoid string-based error detection due to i18n:

```php
// ❌ BAD - Breaks with different locales
if (str_contains($error->getMessage(), 'expired')) {
    // Assumes English error messages!
}

// ✅ GOOD - Use enum-based detection
if ($error instanceof AuthenticationException && 
    $error->kind === AuthenticationError::TokenExpired) {
    // Works regardless of locale
}
```

## Code Examples

### Complete Error Handling Flow

Here's a real-world example showing proper error handling:

```php
use OpenFGA\{Client, Messages};
use OpenFGA\Exceptions\{
    AuthenticationError,
    NetworkError,
    NetworkException,
    AuthenticationException
};
use OpenFGA\Translation\Translator;

class AuthorizationService
{
    private Client $client;
    private string $storeId;
    
    public function checkAccess(string $userId, string $resource): AccessResult
    {
        return $this->client->check(
            store: $this->storeId,
            tupleKey: tuple($userId, 'reader', $resource)
        )
        ->then(function ($response) {
            // Transform successful response
            return new AccessResult(
                allowed: $response->getAllowed(),
                reason: $response->getAllowed() ? 'GRANTED' : 'DENIED'
            );
        })
        ->recover(function ($error) {
            // Handle specific errors gracefully
            return match([$error::class, $error->kind ?? null]) {
                // Network timeouts - fail open
                [NetworkException::class, NetworkError::Timeout] => 
                    new AccessResult(true, 'TIMEOUT_FAIL_OPEN'),
                
                // Authentication errors - deny access
                [AuthenticationException::class, $_] => 
                    new AccessResult(false, 'AUTH_ERROR'),
                
                // Server errors - check cache
                [NetworkException::class, NetworkError::Server] => 
                    $this->checkCachedAccess() ?? new AccessResult(false, 'SERVER_ERROR'),
                
                // Everything else - deny by default
                default => new AccessResult(false, 'UNKNOWN_ERROR'),
            };
        })
        ->success(function ($result) {
            // Log successful checks
            $this->logger->info('Access check completed', [
                'allowed' => $result->allowed,
                'reason' => $result->reason,
            ]);
        })
        ->failure(function ($error) {
            // Log errors with context
            $this->logger->error('Access check failed', [
                'error_type' => $error::class,
                'error_kind' => $error->kind ?? null,
                'message' => $error->getMessage(),
            ]);
        })
        ->unwrap();
    }
    
    public function grantAccess(string $userId, string $resource): void
    {
        $this->client->writeTuples(
            store: $this->storeId,
            writes: tuples(
                tuple($userId, 'reader', $resource)
            )
        )
        ->failure(function ($error) use ($userId, $resource) {
            // Log with translated message
            $this->logger->error(
                Translator::trans(Messages::CLIENT_ERROR_NETWORK),
                [
                    'user' => $userId,
                    'resource' => $resource,
                    'error' => $error::class,
                ]
            );
        })
        ->rethrow(); // Convert to exception if failed
    }
}
```

### Testing Error Conditions

```php
use OpenFGA\Exceptions\{ClientError, NetworkError, NetworkException};
use OpenFGA\Results\Failure;

class AuthorizationServiceTest extends TestCase
{
    public function testHandlesNetworkTimeout(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('check')
            ->willReturn(new Failure(
                new NetworkException(
                    kind: NetworkError::Timeout,
                    request: $this->createMock(RequestInterface::class)
                )
            ));
        
        $service = new AuthorizationService($client, 'store123');
        $result = $service->checkAccess('user:anne', 'document:budget');
        
        // Should fail open on timeout
        expect($result->allowed)->toBeTrue();
        expect($result->reason)->toBe('TIMEOUT_FAIL_OPEN');
    }
    
    public function testHandlesValidationErrors(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('check')
            ->willReturn(new Failure(
                ClientError::Validation->exception(context: [
                    'message' => 'Invalid user format'
                ])
            ));
        
        $service = new AuthorizationService($client, 'store123');
        $result = $service->checkAccess('invalid-user', 'document:budget');
        
        // Should deny on validation errors
        expect($result->allowed)->toBeFalse();
        expect($result->reason)->toBe('UNKNOWN_ERROR');
    }
}
```

### Real SDK Method Example

Here's how the SDK itself handles errors internally:

```php
namespace OpenFGA;

use OpenFGA\Exceptions\{ClientError, NetworkException};
use OpenFGA\Results\{Success, Failure};

class Client implements ClientInterface
{
    public function check(
        string $store,
        TupleKeyInterface $tupleKey,
        ?string $model = null,
    ): ResultInterface {
        // Validate inputs
        if (empty($store)) {
            return new Failure(
                ClientError::Validation->exception(context: [
                    'message' => Translator::trans(
                        Messages::REQUEST_STORE_ID_EMPTY
                    )
                ])
            );
        }
        
        try {
            // Build and send request
            $request = $this->buildCheckRequest($store, $tupleKey, $model);
            $response = $this->httpClient->sendRequest($request);
            
            // Handle response
            return match($response->getStatusCode()) {
                200 => new Success($this->parseCheckResponse($response)),
                400 => new Failure(NetworkException::fromResponse(
                    NetworkError::Invalid,
                    $request,
                    $response
                )),
                401 => new Failure(NetworkException::fromResponse(
                    NetworkError::Unauthenticated,
                    $request,
                    $response
                )),
                default => new Failure(NetworkException::fromResponse(
                    NetworkError::Unexpected,
                    $request,
                    $response
                )),
            };
        } catch (NetworkExceptionInterface $e) {
            // Network-level failures
            return new Failure(new NetworkException(
                kind: NetworkError::Request,
                request: $request ?? null,
                previous: $e
            ));
        } catch (Throwable $e) {
            // Unexpected failures
            return new Failure(ClientError::Network->exception(
                previous: $e
            ));
        }
    }
}
```

## Best Practices Summary

1. **Always handle Result types** - Never call `unwrap()` without checking success first
2. **Use enum comparisons** - Compare error types and kinds, not message strings  
3. **Leverage match expressions** - Use PHP 8.3+ match for clean error handling
4. **Log with context** - Include error type, kind, and relevant data
5. **Fail safely** - Define sensible defaults for error cases
6. **Test error paths** - Write tests for failure scenarios
7. **Use type hints** - Let the type system help catch errors at compile time

Remember: The goal is to make errors impossible to ignore, easy to handle, and consistent across locales.