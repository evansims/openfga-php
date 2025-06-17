## Prerequisites

The examples in this guide assume you have the following setup:

```php
use OpenFGA\{Client, ClientInterface};
use OpenFGA\Models\{Store, AuthorizationModel};
use OpenFGA\Exceptions\{ClientError, ClientException, NetworkError, NetworkException};

use function OpenFGA\{result, ok, err, unwrap, success, failure, tuple, allowed};

$client = new Client(url: 'http://localhost:8080');

$storeId = $_ENV['FGA_STORE_ID'] ?? 'your-store-id';
$modelId = $_ENV['FGA_MODEL_ID'] ?? 'your-model-id';

$store = $client->getStore($storeId)->unwrap();
$model = $client->getAuthorizationModel($storeId, $modelId)->unwrap();
```

## Why use Results

The OpenFGA SDK uses Results to make error handling explicit and chainable:

```php
// Instead of this mess:
try {
    $store = $client->getStore($storeId);
    try {
        $model = $client->getAuthorizationModel($store->getId(), $modelId);
        // Do something with $model
    } catch (Exception $e) {
        // Handle model error
    }
} catch (Exception $e) {
    // Handle store error
}

// You get this:
$client->getStore($storeId)
    ->then(fn($store) => $client->getAuthorizationModel($store->getId(), $modelId))
    ->success(fn($model) => $this->processModel($model))
    ->failure(fn($error) => $this->logError($error));
```

All SDK methods return either [`Success`](../API/Results/Success.md) or [`Failure`](../API/Results/Failure.md) objects instead of throwing exceptions for expected failures like "not found" or validation errors.

## Basic usage

The most common patterns you'll need:

### Just get the value

```php
// Get the value or throw on failure
$store = $client->getStore($storeId)->unwrap();

// Get the value with a fallback
$store = $client->getStore($storeId)->unwrap(fn($error) => /* fallback */);
```

### Handle success and failure

```php
$result = $client->createStore(name: 'my-store');

$result
    ->success(fn($store) => logger()->info("Created store: {$store->getId()}"))
    ->failure(fn($error) => logger()->error("Failed: {$error->getMessage()}"));
```

### Check the outcome

```php
if ($result->succeeded()) {
    $store = $result->unwrap();
    // Do something with $store
}

if ($result->failed()) {
    $error = $result->err();
    // Handle the error
}
```

## Chaining operations

Results really shine when you need to chain multiple operations:

### Transform success values with `then()`

```php
$result = $client->getStore($storeId)
    ->then(fn($store) => $client->getAuthorizationModel($store->getId(), $modelId))
    ->then(fn($model) => $this->processModel($model));
```

### Provide fallbacks with `recover()`

```php
$model = $client->getAuthorizationModel($storeId, $modelId)
    ->recover(fn($error) => $this->getDefaultModel())
    ->unwrap();
```

### Pipeline with side effects

```php
$tuples = [/* your tuples here */];

$client->writeTuples($store, $model, $tuples)
    ->success(fn($response) => $this->logSuccess($response))
    ->failure(fn($error) => $this->logError($error))
    ->then(fn($response) => $this->notifyWebhooks($response))
    ->recover(fn($error) => $this->handleFailure($error));
```

## Error handling patterns

### Fail gracefully with helper functions

```php
// Return a sensible default when things go wrong
function getUserPermissions(string $userId): array
{
    return result(function() use ($userId) {
        return $this->client->listObjects(
            user: $userId,
            relation: 'can_access'
        );
    })
    ->then(fn($response) => $response->getObjects())
    ->recover(function(Throwable $error) {
        logger()->warning('Failed to get user permissions', [
            'error_type' => $error::class,
            'message' => $error->getMessage()
        ]);
        return []; // Empty permissions on error
    })
    ->unwrap();
}
```

### Handling specific error types with enum-based exceptions

```php
function canUserAccess(string $userId, string $documentId): bool
{
    // Call client->check() to get a ResultInterface object
    $result = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple("user:{$userId}", 'viewer', "document:{$documentId}")
    );

    // Check if the operation failed
    if ($result->failed()) {
        $error = $result->err(); // Get the actual error/exception

        // Handle specific enum-based ClientException errors
        if ($error instanceof ClientException) {
            return match($error->getError()) { // getError() returns the enum
                // Network errors can be retried
                ClientError::Network => $this->retryAfterDelay(function() use ($userId, $documentId) {
                    // Recursive call or re-attempt logic
                    return $this->canUserAccess($userId, $documentId);
                }, $maxRetries = 3),

                // Authentication errors should trigger re-auth
                ClientError::Authentication => $this->handleAuthError($error), // Pass the error object

                // Fall back to cached permissions for other client errors
                default => $this->getCachedPermission($userId, $documentId, false)
            };
        }

        // Handle other types of Throwable or log unexpected errors
        logger()->error('Unexpected error checking permissions', [
            'error_type' => $error::class,
            'message' => $error->getMessage(),
            'user' => $userId,
            'document' => $documentId
        ]);

        return false; // Secure default for unhandled errors
    }

    // If successful, unwrap to get the CheckResponse and then getAllowed()
    return $result->unwrap()->getAllowed();
}
```

### Collect multiple results

```php
$userId = 'user:anne';
$resourceId = 'document:budget-2024';

$permissions = collect(['read', 'write', 'delete'])
    ->map(fn($action) => $client->check(user: $userId, relation: $action, object: $resourceId))
    ->filter(fn($result) => $result->succeeded())
    ->map(fn($result) => $result->unwrap()->getAllowed())
    ->toArray();
```

### Add context to errors

```php
$tuples = [/* your tuples here */];

$result = $client->writeTuples($store, $model, $tuples)
    ->failure(function(Throwable $e) use ($store, $tuples) {
        logger()->error("Failed to write tuples to store {$store->getId()}", [
            'error' => $e->getMessage(),
            'tuples_count' => count($tuples)
        ]);
    });
```

### Convert errors to application exceptions

```php
$model = $client->getAuthorizationModel($storeId, $modelId)
    ->recover(fn($error) => throw new ModelNotFoundException($modelId, previous: $error))
    ->unwrap();
```

## Advanced patterns

### Retry with exponential backoff

```php
function checkWithRetry(string $user, string $relation, string $object): bool
{
    return retry(3, function() use ($user, $relation, $object) {
        return $this->client->check(
            user: $user,
            relation: $relation,
            object: $object
        )->unwrap()->getAllowed();
    }, sleepMilliseconds: fn($attempt) => $attempt * 1000);
}
```

### Batch operations with partial failures

```php
function batchCheck(array $checks): array
{
    return collect($checks)
        ->map(fn($check) => $this->client->check(...$check))
        ->map(fn($result, $index) => [
            'index' => $index,
            'allowed' => $result->succeeded() && $result->unwrap()->getAllowed(),
            'error' => $result->failed() ? $result->err()->getMessage() : null
        ])
        ->toArray();
}
```

### Framework integration

#### Laravel Service

```php
// Note: This is an example helper class for a Laravel application and not part of the SDK.
class PermissionService
{
    public function __construct(
        private ClientInterface $client,
        private string $storeId,
        private string $modelId
    ) {}

    public function can(string $user, string $action, string $resource): bool
    {
        return $this->client
            ->check(
                store: $this->storeId,
                model: $this->modelId,
                tupleKey: tuple($user, $action, $resource)
            )
            ->then(fn($response) => $response->getAllowed())
            ->recover(function(Throwable $e) {
                Log::warning('Permission check failed', [
                    'user' => $user,
                    'action' => $action,
                    'resource' => $resource,
                    'error' => $e->getMessage()
                ]);
                return false; // Fail closed
            })
            ->unwrap();
    }
}
```

#### Symfony Voter

```php
class FgaVoter extends Voter
{
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->client
            ->check(
                store: $this->store,
                model: $this->model,
                tupleKey: tuple($token->getUserIdentifier(), $attribute, $subject->getId())
            )
            ->then(fn($response) => $response->getAllowed())
            ->recover(fn() => false) // Deny on error
            ->unwrap();
    }
}
```

## When to use what

- **`unwrap()`** **unwrap()** - When you want simple exception-based error handling
- **`success()`** **success()** **/** **`failure()`** **failure()** - For side effects like logging without changing the result
- **`then()`** **then()** - To transform success values or chain operations
- **`recover()`** **recover()** - To provide fallbacks or convert failures to successes

The Result pattern makes error handling explicit and composable. Chain operations confidently knowing failures won't break your pipeline.
