# Results

## Why use Results?

Tired of wrapping every API call in try-catch blocks? The OpenFGA SDK uses Results to make error handling explicit and chainable:

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

All SDK methods return either `Success` or `Failure` objects instead of throwing exceptions for expected failures like "not found" or validation errors.

## Basic usage

The most common patterns you'll need:

### Just get the value

```php
// Get the value or throw on failure
$store = $client->getStore($storeId)->unwrap();

// Get the value with a fallback
$store = $client->getStore($storeId)->unwrap(
    fn($error) => Store::default()
);
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
$config = $client->getStore($storeId)
    ->then(fn($store) => $store->getConfiguration())
    ->recover(fn($error) => Configuration::default())
    ->unwrap();
```

### Pipeline with side effects

```php
$client->writeTuples($store, $model, $tuples)
    ->success(fn($response) => $this->logSuccess($response))
    ->failure(fn($error) => $this->logError($error))
    ->then(fn($response) => $this->notifyWebhooks($response))
    ->recover(fn($error) => $this->handleFailure($error));
```

## Error handling patterns

### Fail gracefully

```php
// Return a sensible default when things go wrong
function getUserPermissions(string $userId): array 
{
    return $client->listObjects(user: $userId, relation: 'can_access')
        ->then(fn($response) => $response->getObjects())
        ->recover(fn($error) => []) // Empty permissions on error
        ->unwrap();
}
```

### Collect multiple results

```php
$permissions = collect(['read', 'write', 'delete'])
    ->map(fn($action) => $client->check(user: $userId, relation: $action, object: $resourceId))
    ->filter(fn($result) => $result->succeeded())
    ->map(fn($result) => $result->unwrap()->getIsAllowed())
    ->toArray();
```

### Add context to errors

```php
$result = $client->writeTuples($store, $model, $tuples)
    ->failure(function(Throwable $e) use ($store) {
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
        )->unwrap()->getIsAllowed();
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
            'allowed' => $result->succeeded() && $result->unwrap()->getIsAllowed(),
            'error' => $result->failed() ? $result->err()->getMessage() : null
        ])
        ->toArray();
}
```

### Framework integration

#### Laravel Service

```php
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
            ->then(fn($response) => $response->getIsAllowed())
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
            ->then(fn($response) => $response->getIsAllowed())
            ->recover(fn() => false) // Deny on error
            ->unwrap();
    }
}
```

## When to use what

- **`unwrap()`** - When you want simple exception-based error handling
- **`success()` / `failure()`** - For side effects like logging without changing the result  
- **`then()`** - To transform success values or chain operations
- **`recover()`** - To provide fallbacks or convert failures to successes

The Result pattern makes error handling explicit and composable. Chain operations confidently knowing failures won't break your pipeline.
