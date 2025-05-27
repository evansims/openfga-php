# Working with Results: Success and Failure

The OpenFGA PHP SDK uses a **Result pattern** for operations that can either succeed or fail. Instead of throwing exceptions directly for many expected operational outcomes (like "not found" from the server, or invalid input that the server might reject), SDK methods that interact with the OpenFGA API typically return either:

- A `Success` object, containing the successfully retrieved value or response.
- A `Failure` object, containing an error, which is usually an `Exception` or `Throwable` instance detailing what went wrong.

This approach encourages more explicit and robust error handling in your application.

## Benefits of the Result Pattern

- **Explicit Error Handling:** It makes the possibility of failure a first-class citizen. Your code is naturally guided to consider both the success path and the failure path, leading to more resilient applications.
- **Type Safety (with static analysis):** Tools like PHPStan can often infer the type of the value within a `Success` object or the type of the error within a `Failure` object, improving code quality.
- **Reduced Try-Catch Boilerplate:** For many non-exceptional failures (e.g., an entity not found, a validation error from the server), you don't need to litter your code with `try-catch` blocks. Instead, you handle the `Failure` type directly. Critical runtime exceptions (e.g., network issues if not caught by the HTTP client, internal SDK errors) might still be thrown.
- **Functional Chaining:** `Result` objects often come with methods that allow you to chain operations in a clean, readable way, transforming data or performing side effects only if the previous step was successful.

## The `ResultInterface`

Both `Success<TValue>` and `Failure<TError>` objects implement the `OpenFGA\Results\ResultInterface<TValue, TError>`. This interface provides a common set of methods to work with the outcome of an operation. Key methods include:

- `succeeded(): bool`: Returns `true` if the result is a `Success`, `false` otherwise.
- `failed(): bool`: Returns `true` if the result is a `Failure`, `false` otherwise.

- `recover(callable $fn): ResultInterface`: When a `Failure` chains an operation that itself returns a `ResultInterface`.
- `failure(callable $fn): ResultInterface`: Performs a side effect if the Result is `Failure`.
- `rethrow(?Throwable $throwable = null): ResultInterface`: Throws the error of a `Failure`, or continues the chain.

- `then(callable $fn): ResultInterface`: When a `Success` chains an operation that itself returns a `ResultInterface`.
- `success(callable $fn): ResultInterface`: Performs a side effect if the Result is `Success`.
- `unwrap(mixed $default = null): mixed`: Extracts the value from `Success` or returns a default if it's a `Failure`.

- `val(): mixed`: Returns the value of the `Success` or throws an exception if the result is a `Failure`.
- `err(): mixed`: Returns the error of the `Failure` or throws an exception if the result is a `Success`.

The SDK also provides helper functions (e.g., `OpenFGA\Results\result()`, `OpenFGA\Results\success()`, `OpenFGA\Results\failure()`, `OpenFGA\Results\unwrap()`, `OpenFGA\Results\ok()`, `OpenFGA\Results\err()`) that can make working with `Result` objects more straightforward in some contexts.

## Guidance: Choosing How to Handle Results

Here's practical advice on when to use different approaches for handling `Result` objects:

**1. Using `unwrap()` (Method on `ResultInterface`)**

- **When:**
  - When you have a sensible default value to fall back on, and the failure case doesn't require complex logic.
- **Behavior:**
  - `$result->unwrap($defaultValue)`: If `Success`, returns its value. If `Failure`, returns `$defaultValue`.
  - `$result->unwrap()`: If `Success`, returns its value. If `Failure`, it returns `null`.

```php
<?php
use OpenFGA\Results\Success;
use OpenFGA\Results\Failure;

use Exception;

$successResult = new Success("Operation succeeded!");
echo $successResult->unwrap("Default value") . "\n"; // Output: Operation succeeded!

$failureResult = new Failure(new Exception("Something went wrong"));
echo $failureResult->unwrap("Default value for failure") . "\n"; // Output: Default value for failure
?>
```

**2. Using `success()` and `failure()` (Helper Functions)**

- **When:** Use these for performing **side effects** based on the outcome, when you don't necessarily need to transform the `Result`'s inner value into something else to be returned.
- **Behavior:**
  - `success($result, $callable)`: Executes `$callable` (passing the success value) only if `$result` is `Success`.
  - `failure($result, $callable)`: Executes `$callable` (passing the error/exception) only if `$result` is `Failure`.

```php
<?php
use OpenFGA\Client; // Assuming $client is an instance
use OpenFGA\Responses\CreateStoreResponseInterface;
use function OpenFGA\Results\{recover, then, success, failure, unwrap, ok, err};

use Exception;

// $createStoreResult = $client->createStore(name: 'another-store');
// For demonstration:
$createStoreResult = new Failure(new Exception("Network timeout"));

success($createStoreResult, function(CreateStoreResponseInterface $response) {
    // Log success, update frontend, etc.
    error_log("Store creation successful: " . $response->getId());
});

failure($createStoreResult, function(Throwable $error) {
    // Log error, show error message to user, re-throw as application-specific exception
    error_log("Store creation failed: " . $error->getMessage());
    // throw new MyApplicationException("Failed to create store", 0, $error);
});
?>
```

**3. Using `Result` Methods (`recover`, `then`, `success`, `failure`, etc.)**

- **When:** Ideal for creating **fluent processing pipelines**, transforming the `Result` or its contents, or conditionally chaining operations.
- **Behavior:** These methods are called directly on the `Result` object. See detailed explanations below.

```php
<?php
use OpenFGA\Results\{success, failure};
use Exception;

// Mini-scenario:
// $apiResponseResult = $client->someOperation(); // Returns ResultInterface<HttpResponseInterface, ApiError>
// For demonstration:
$apiResponseResult = success(['body' => '{"id": 123, "name": "Test"}', 'statusCode' => 200]);

$processedResult = $apiResponseResult
    ->then(function(array $response) { // Assuming $response is like ['body' => string, 'statusCode' => int]
        if ($response['statusCode'] !== 200) {
            throw new Exception("Non-200 status code: " . $response['statusCode']);
        }

        return success(json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR));
    }) // Now ResultInterface<array, Exception|JsonException>
    ->success(fn(array $data) => print("Successfully decoded data for ID: " . ($data['id'] ?? 'unknown')))
    ->failure(fn(Throwable $error) => print("Processing failed: " . $error->getMessage()));

// Handle the final outcome
match ($processedResult) {
    success($data) => print_r($data),
    failure($error) => error_log($error->getMessage()),
};
?>
```

## Detailed `ResultInterface` Methods

Here's a closer look at the key methods available on `ResultInterface` objects:

- **`success(): bool`**

  - **Purpose:** Checks if the result is a `Success`.
  - **Works On:** Both `Success` and `Failure`.

  - ```php
    $result = new Success("It worked!");
    if ($result->success()) { echo "Success!\n"; }
    ```

- **`failure(): bool`**

  - **Purpose:** Checks if the result is a `Failure`.
  - **Works On:** Both `Success` and `Failure`.

  - ```php
    $result = new Failure(new \Exception("It failed."));
    if ($result->failure()) { echo "Failure!\n"; }
    ```

- **`unwrap(mixed $default = null): mixed`** (Already covered in Guidance)

- **`recover(callable $fn): ResultInterface`**

  - **Purpose:** Transforms the error inside a `Failure` object using `$fn`. If it's a `Success`, it remains `Success`. `$fn` receives the error and should return the new error.
  - **Works On:** `Failure` (transforms its error), `Success` (passes through).

  - ```php
    $result = new Failure(new Exception("Account Already Exists"));
    $caught = $result->recover(function (Throwable $err) {
        $userDetails = $this->getUserDetails($err->getMessage());
        return success($userDetails); // Success($userDetails)
    });
    ```

- **`then(callable $fn): ResultInterface`**

  - **Purpose:** Chains an operation where the callable `$fn` itself returns a `ResultInterface`. This is crucial for sequencing multiple operations that can each succeed or fail. If the original result is `Success`, `$fn` is called with its value. If `Failure`, `$fn` is not called, and the original `Failure` is passed through.
  - **Works On:** `Success` (applies `$fn`), `Failure` (passes through).

  - ```php
    function step1(int $value): ResultInterface {
        return new Success($value + 1);
    }
    function step2_can_fail(int $value): ResultInterface {
        if ($value > 5) {
            return new Success("Value $value is large enough.");
        }
        return new Failure(new \InvalidArgumentException("Value $value is too small."));
    }

    $initialResult = new Success(3);
    $finalResult = $initialResult
        ->then('step1')       // Success(4)
        ->then('step2_can_fail'); // Failure(InvalidArgumentException("Value 4 is too small."))

    echo $finalResult->unwrap("default") . "\n"; // Output: default
    ```

- **`success(callable $fn): ResultInterface`**

  - **Purpose:** Executes the callable `$fn` if the result is `Success`. Similar to `tap`, but often used more for explicit branching logic rather than just a passthrough side effect in a chain. The original `Result` is returned.
  - **Works On:** `Success` (executes `$fn`), `Failure` (passes through).

  - ```php
    $result = new Success("Success payload");
    $result->success(fn($value) => echo "Operation successful with: $value\n");
    ```

- **`failure(callable $fn): ResultInterface`**

  - **Purpose:** Executes the callable `$fn` if the result is `Failure`. Similar to `tapError`. The original `Result` is returned.
  - **Works On:** `Failure` (executes `$fn`), `Success` (passes through).

  - ```php
    $result = new Failure(new \Exception("Something failed"));
    $result->failure(fn(Throwable $err) => echo "Operation failed: " . $err->getMessage() . "\n");
    ```

## Best Practices

### When to Use Which Pattern

1. **Use `unwrap()` when:**
   - You want exceptions to bubble up naturally
   - You're in a context where exception handling is already established
   - You need the simplest possible API

2. **Use `success()`/`failure()` callbacks when:**
   - You want to handle success/failure cases inline
   - You need side effects (logging, metrics) without changing the result
   - You're building a pipeline of operations

3. **Use `then()` when:**
   - You need to transform success values
   - You're chaining multiple operations
   - You want functional programming style

4. **Use `recover()` when:**
   - You have fallback values for specific errors
   - You want to convert certain failures to successes
   - You're implementing retry logic

### Common Patterns

#### Pattern 1: Pipeline with Fallback

```php
$config = $client->getStore($storeId)
    ->then(fn($store) => $store->getConfiguration())
    ->recover(fn() => Configuration::default())
    ->unwrap();
```

#### Pattern 2: Collecting Multiple Results

```php
$results = [
    $client->check(...),
    $client->check(...),
    $client->check(...)
];

$allAllowed = array_reduce($results, 
    fn($carry, $result) => $carry && $result->unwrap()->getIsAllowed(),
    true
);
```

#### Pattern 3: Error Context

```php
$result = $client->writeTuples($store, $model, $tuples)
    ->failure(function(Throwable $e) use ($store) {
        error_log("Failed to write tuples to store {$store->getId()}: " . $e->getMessage());
    })
    ->rethrow(fn($e) => new ApplicationException("Permission update failed", previous: $e));
```

## Integration with Frameworks

### Laravel Example

```php
class PermissionService
{
    public function __construct(
        private ClientInterface $client,
        private string $storeId,
        private string $modelId
    ) {}

    public function checkPermission(string $user, string $relation, string $object): bool
    {
        return $this->client
            ->check($this->storeId, $this->modelId, tuple($user, $relation, $object))
            ->then(fn($response) => $response->getIsAllowed())
            ->recover(function(Throwable $e) {
                Log::error('Permission check failed', ['error' => $e->getMessage()]);
                return false; // Fail closed
            })
            ->unwrap();
    }
}
```

### Symfony Example

```php
class FgaVoter extends Voter
{
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUserIdentifier();
        
        return $this->client
            ->check(store: $this->store, model: $this->model, tupleKey: tuple($user, $attribute, $subject->getId()))
            ->then(fn($response) => $response->getIsAllowed())
            ->unwrap();
    }
}
```

## Summary

The Result pattern provides a flexible way to handle operations that might fail without relying solely on exceptions. It encourages explicit error handling while remaining ergonomic for common use cases. Choose the method that best fits your application's error handling strategy and coding style.
