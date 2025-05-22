# Working with Results: Success and Failure

The OpenFGA PHP SDK uses a **Result pattern** for operations that can either succeed or fail. Instead of throwing exceptions directly for many expected operational outcomes (like "not found" from the server, or invalid input that the server might reject), SDK methods that interact with the OpenFGA API typically return either:

*   A `Success` object, containing the successfully retrieved value or response.
*   A `Failure` object, containing an error, which is usually an `Exception` or `Throwable` instance detailing what went wrong.

This approach encourages more explicit and robust error handling in your application.

## Benefits of the Result Pattern

*   **Explicit Error Handling:** It makes the possibility of failure a first-class citizen. Your code is naturally guided to consider both the success path and the failure path, leading to more resilient applications.
*   **Type Safety (with static analysis):** Tools like PHPStan can often infer the type of the value within a `Success` object or the type of the error within a `Failure` object, improving code quality.
*   **Reduced Try-Catch Boilerplate:** For many non-exceptional failures (e.g., an entity not found, a validation error from the server), you don't need to litter your code with `try-catch` blocks. Instead, you handle the `Failure` type directly. Critical runtime exceptions (e.g., network issues if not caught by the HTTP client, internal SDK errors) might still be thrown.
*   **Functional Chaining:** `Result` objects often come with methods that allow you to chain operations in a clean, readable way, transforming data or performing side effects only if the previous step was successful.

## The `ResultInterface`

Both `Success<TValue>` and `Failure<TError>` objects implement the `OpenFGA\Interfaces\ResultInterface<TValue, TError>`. This interface provides a common set of methods to work with the outcome of an operation. Key methods include:

*   `isSuccess(): bool`: Returns `true` if the result is a `Success`, `false` otherwise.
*   `isFailure(): bool`: Returns `true` if the result is a `Failure`, `false` otherwise.
*   `unwrap(mixed $default = null): mixed`: Extracts the value from `Success` or returns a default (or throws an exception if it's a `Failure` and no default is provided).
*   `fold(callable $onSuccess, callable $onFailure): mixed`: Processes both outcomes, transforming the Result into a single value.
*   `map(callable $fn): ResultInterface`: Transforms the value inside a `Success` object.
*   `mapError(callable $fn): ResultInterface`: Transforms the error inside a `Failure` object.
*   `then(callable $fn): ResultInterface`: Chains an operation that itself returns a `ResultInterface`.
*   `tap(callable $fn): ResultInterface`: Performs a side effect on a `Success` without changing the Result.
*   `tapError(callable $fn): ResultInterface`: Performs a side effect on a `Failure` without changing the Result.
*   `onSuccess(callable $fn): ResultInterface`: Performs a side effect if the Result is `Success`.
*   `onFailure(callable $fn): ResultInterface`: Performs a side effect if the Result is `Failure`.

The SDK also provides helper functions (e.g., `OpenFGA\Results\fold()`, `OpenFGA\Results\success()`, `OpenFGA\Results\failure()`) that can make working with `Result` objects more straightforward in some contexts.

## Guidance: Choosing How to Handle Results

Here's practical advice on when to use different approaches for handling `Result` objects:

**1. Using `unwrap()` (Method on `ResultInterface`)**

*   **When:**
    *   For quick examples, demonstrations, or simple scripts where detailed error handling isn't the primary focus.
    *   When you have a sensible default value to fall back on, and the failure case doesn't require complex logic.
    *   Inside a `try-catch` block if you specifically want to convert a `Failure` into an exception.
*   **Behavior:**
    *   `$result->unwrap($defaultValue)`: If `Success`, returns its value. If `Failure`, returns `$defaultValue`.
    *   `$result->unwrap()`: If `Success`, returns its value. If `Failure`, it **throws an `UnwrapException`** (or the original exception if the `Failure` was created with one that is an instance of `UnwrapException`).
*   **Caution:** Using `unwrap()` without a default value or a surrounding `try-catch` in production code can lead to unhandled exceptions if a `Failure` occurs. If you provide a default, ensure that the default value is handled meaningfully by subsequent code.

```php
<?php
use OpenFGA\Results\Success;
use OpenFGA\Results\Failure;

$successResult = new Success("Operation succeeded!");
echo $successResult->unwrap("Default value") . "\n"; // Output: Operation succeeded!

$failureResult = new Failure(new \Exception("Something went wrong"));
echo $failureResult->unwrap("Default value for failure") . "\n"; // Output: Default value for failure

try {
    $failureResult->unwrap(); // This will throw UnwrapException
} catch (\OpenFGA\Exceptions\UnwrapException $e) {
    echo "Unwrap failed: " . $e->getPrevious()->getMessage() . "\n"; // Output: Unwrap failed: Something went wrong
}
?>
```

**2. Using `fold()` (Helper Function or Method)**

*   **When:** This is often the **most robust and recommended way** to handle both `Success` and `Failure` outcomes, especially when you need to transform the `Result` into a single, definite value or execute different logic paths.
*   **Behavior:** Takes two callables: one for `Success` (receives the value) and one for `Failure` (receives the error/exception). It executes the appropriate callable and returns its result.

```php
<?php
use OpenFGA\Client; // Assuming $client is an instance
use OpenFGA\Responses\CreateStoreResponseInterface;
use function OpenFGA\Results\fold; // Helper function

// $createStoreResult = $client->createStore(name: 'my-new-store');
// For demonstration, let's simulate a result:
$createStoreResult = new Success(/* mock CreateStoreResponseInterface */); 

$message = fold(
    $createStoreResult,
    fn(CreateStoreResponseInterface $response) => "Store created with ID: " . $response->getId(),
    fn(Throwable $error) => "Store creation failed: " . $error->getMessage()
);
echo $message . "\n";
?>
```

**3. Using `success()` and `failure()` (Helper Functions)**

*   **When:** Use these for performing **side effects** based on the outcome, when you don't necessarily need to transform the `Result`'s inner value into something else to be returned.
*   **Behavior:**
    *   `success($result, $callable)`: Executes `$callable` (passing the success value) only if `$result` is `Success`.
    *   `failure($result, $callable)`: Executes `$callable` (passing the error/exception) only if `$result` is `Failure`.

```php
<?php
use OpenFGA\Client; // Assuming $client is an instance
use OpenFGA\Responses\CreateStoreResponseInterface;
use function OpenFGA\Results\{success, failure};

// $createStoreResult = $client->createStore(name: 'another-store');
// For demonstration:
$createStoreResult = new Failure(new \Exception("Network timeout"));

success($createStoreResult, function(CreateStoreResponseInterface $response) {
    // Log success, update UI, etc.
    error_log("Store creation successful: " . $response->getId());
});

failure($createStoreResult, function(Throwable $error) {
    // Log error, show error message to user, re-throw as application-specific exception
    error_log("Store creation failed: " . $error->getMessage());
    // throw new MyApplicationException("Failed to create store", 0, $error);
});
?>
```

**4. Using `Result` Methods (`map`, `then`, `tap`, `onSuccess`, etc.)**

*   **When:** Ideal for creating **fluent processing pipelines**, transforming the `Result` or its contents, or conditionally chaining operations.
*   **Behavior:** These methods are called directly on the `Result` object. See detailed explanations below.

```php
<?php
// Mini-scenario:
// $apiResponseResult = $client->someOperation(); // Returns ResultInterface<HttpResponseInterface, ApiError>
// For demonstration:
$apiResponseResult = new Success(['body' => '{"id": 123, "name": "Test"}', 'statusCode' => 200]);

$processedResult = $apiResponseResult
    ->map(function(array $response) { // Assuming $response is like ['body' => string, 'statusCode' => int]
        if ($response['statusCode'] !== 200) {
            throw new \Exception("Non-200 status code: " . $response['statusCode']);
        }
        return json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
    }) // Now ResultInterface<array, Exception|JsonException>
    ->tap(function(array $data) {
        error_log("Successfully decoded data for ID: " . ($data['id'] ?? 'unknown'));
    }) // Still ResultInterface<array, Exception|JsonException>
    ->mapError(function(Throwable $error) {
        return "Processing failed: " . $error->getMessage();
    }); // Now ResultInterface<array, string>

// Handle the final outcome
fold(
    $processedResult,
    fn(array $data) => print_r($data),
    fn(string $errorMessage) => error_log($errorMessage)
);
?>
```

## Detailed `ResultInterface` Methods

Here's a closer look at the key methods available on `ResultInterface` objects:

*   **`isSuccess(): bool`**
    *   **Purpose:** Checks if the result is a `Success`.
    *   **Works On:** Both `Success` and `Failure`.
    *   ```php
        $result = new Success("It worked!");
        if ($result->isSuccess()) { echo "Success!\n"; }
        ```

*   **`isFailure(): bool`**
    *   **Purpose:** Checks if the result is a `Failure`.
    *   **Works On:** Both `Success` and `Failure`.
    *   ```php
        $result = new Failure(new \Exception("It failed."));
        if ($result->isFailure()) { echo "Failure!\n"; }
        ```

*   **`unwrap(mixed $default = null): mixed`** (Already covered in Guidance)

*   **`fold(callable $onSuccess, callable $onFailure): mixed`** (Already covered in Guidance, also available as a method)
    *   ```php
        $result = new Success("Data");
        $message = $result->fold(
            fn($value) => "Got value: $value",
            fn($error) => "Got error: " . $error->getMessage()
        );
        echo $message . "\n"; // Output: Got value: Data
        ```

*   **`map(callable $fn): ResultInterface`**
    *   **Purpose:** Transforms the value inside a `Success` object using the provided callable `$fn`. If it's a `Failure`, it remains a `Failure` with the original error. The callable `$fn` should return the new success value. If `$fn` throws an exception, the result becomes a `Failure` wrapping that exception.
    *   **Works On:** `Success` (transforms its value), `Failure` (passes through).
    *   ```php
        $result = new Success(5);
        $mappedResult = $result->map(fn($value) => $value * 2); // Success(10)
        echo $mappedResult->unwrap() . "\n"; // Output: 10

        $failureResult = new Failure(new \Exception("Initial error"));
        $mappedFailure = $failureResult->map(fn($value) => $value * 2); // Failure(Exception("Initial error"))
        echo $mappedFailure->unwrap("default") . "\n"; // Output: default
        ```

*   **`mapError(callable $fn): ResultInterface`**
    *   **Purpose:** Transforms the error inside a `Failure` object using `$fn`. If it's a `Success`, it remains `Success`. `$fn` receives the error and should return the new error.
    *   **Works On:** `Failure` (transforms its error), `Success` (passes through).
    *   ```php
        $result = new Failure(new \Exception("DB Error"));
        $mappedResult = $result->mapError(fn(Throwable $err) => "Application Error: " . $err->getMessage()); // Failure("Application Error: DB Error")
        // Note: The error type might change here if $fn returns a different type.
        ```

*   **`then(callable $fn): ResultInterface`**
    *   **Purpose:** Chains an operation where the callable `$fn` itself returns a `ResultInterface`. This is crucial for sequencing multiple operations that can each succeed or fail. If the original result is `Success`, `$fn` is called with its value. If `Failure`, `$fn` is not called, and the original `Failure` is passed through.
    *   **Works On:** `Success` (applies `$fn`), `Failure` (passes through).
    *   **`map` vs. `then`:**
        *   `map($fn)`: `$fn` returns a new value `V`. `map` wraps `V` in `Success<V>`.
        *   `then($fn)`: `$fn` returns a `ResultInterface<V, E>`. `then` returns that result directly.
    *   ```php
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

*   **`tap(callable $fn): ResultInterface`**
    *   **Purpose:** Performs a side effect with the value of a `Success` (e.g., logging) without changing the `Result` itself. The original `Result` is returned.
    *   **Works On:** `Success` (executes `$fn`), `Failure` (passes through).
    *   ```php
        $result = new Success("Important Data");
        $result->tap(fn($value) => error_log("Processing value: $value"));
        // $result is still Success("Important Data")
        ```

*   **`tapError(callable $fn): ResultInterface`**
    *   **Purpose:** Performs a side effect with the error of a `Failure` (e.g., logging) without changing the `Result`. The original `Result` is returned.
    *   **Works On:** `Failure` (executes `$fn`), `Success` (passes through).
    *   ```php
        $result = new Failure(new \Exception("Tap This Error"));
        $result->tapError(fn(Throwable $err) => error_log("Error encountered: " . $err->getMessage()));
        // $result is still Failure(Exception("Tap This Error"))
        ```

*   **`onSuccess(callable $fn): ResultInterface`**
    *   **Purpose:** Executes the callable `$fn` if the result is `Success`. Similar to `tap`, but often used more for explicit branching logic rather than just a passthrough side effect in a chain. The original `Result` is returned.
    *   **Works On:** `Success` (executes `$fn`), `Failure` (passes through).
    *   ```php
        $result = new Success("Success payload");
        $result->onSuccess(fn($value) => echo "Operation successful with: $value\n");
        ```

*   **`onFailure(callable $fn): ResultInterface`**
    *   **Purpose:** Executes the callable `$fn` if the result is `Failure`. Similar to `tapError`. The original `Result` is returned.
    *   **Works On:** `Failure` (executes `$fn`), `Success` (passes through).
    *   ```php
        $result = new Failure(new \Exception("Something failed"));
        $result->onFailure(fn(Throwable $err) => echo "Operation failed: " . $err->getMessage() . "\n");
        ```

## Feature Comparison: `tap` vs. `onSuccess`/`onFailure`

| Feature      | `tap(fn)` / `tapError(fn)`       | `onSuccess(fn)` / `onFailure(fn)`   |
| ------------ | -------------------------------- | ----------------------------------- |
| Primary Use  | Side-effects within a fluent chain | Side-effects, often for branching or final actions |
| Return value | The original `ResultInterface`   | The original `ResultInterface`      |
| Chainable?   | ✅ Yes                           | ✅ Yes                              |

The primary distinction is often idiomatic:
*   `tap` and `tapError` are typically used for "tapping into" the chain to perform a side effect (like logging) without derailing the main flow of data transformation.
*   `onSuccess` and `onFailure` are often used at points where you might want to perform a more significant action based on the state, which might not strictly be part of a continuous data transformation pipeline. However, since both return the original `Result`, they are technically similar in chainability.

## Example: Typical SDK Usage

Let's revisit the example of creating a store and handling the result, showing a more complete approach.

```php
<?php
use OpenFGA\Client;
use OpenFGA\Responses\CreateStoreResponseInterface;
use OpenFGA\Exceptions\FGAApiError; // Specific exception type for API errors
use function OpenFGA\Results\fold;

// Assume $client is an initialized OpenFGA Client
// $client = new Client(...);

$createStoreResult = $client->createStore(name: 'my-production-store');

$message = fold(
    $createStoreResult,
    function(CreateStoreResponseInterface $response) {
        // Success path: The store was created
        // Perform side effects like logging or setting the store ID on the client
        error_log("Store '{$response->getName()}' created successfully with ID: {$response->getId()}");
        // $this->client->setStore($response->getId()); // If in a class context
        return "Successfully provisioned store: " . $response->getId();
    },
    function(Throwable $error) {
        // Failure path: Store creation failed
        if ($error instanceof FGAApiError) {
            // Handle specific FGA API errors (e.g., validation error, already exists)
            error_log("FGA API Error creating store: " . $error->getMessage() . " (Status: " . $error->getApiStatus() . ")");
            return "Failed to create store due to API error: " . $error->getMessage();
        } elseif ($error instanceof \OpenFGA\Exceptions\HttpRequestException) {
            // Handle network or HTTP client related errors
            error_log("HTTP Request Error creating store: " . $error->getMessage());
            return "Failed to create store due to network issues.";
        } else {
            // Handle other unexpected errors
            error_log("Unexpected error creating store: " . $error->getMessage());
            // Optionally re-throw or convert to a standard application exception
            // throw new \RuntimeException("Critical error during store creation.", 0, $error);
            return "An unexpected error occurred during store creation.";
        }
    }
);

echo $message . "\n";
?>
```
This example demonstrates using `fold` to robustly handle different success and failure scenarios, log appropriately, and produce a user-friendly message or take further action.
```
