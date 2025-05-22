# Results

Results are returned by client methods as either a `Success` or `Failure` object with identical interfaces. Results are used to represent the outcome of an operation, and can be used to handle errors in a more structured way. A number of helper functions are provided to make working with Results easier.

## Guidance

- Your application is responsible for handling `Failure` states.
- Use the `fold()` [helper](#helpers) when you need to resolve a Result to a value.
- Use the `success()` and `failure()` [helpers](#helpers) when you need to perform side effects based on the result.
- For transformations, use the `map()`, `mapError()`, `then()`, `tap()`, and `tapError()` [Result methods](#result-methods).

## Example

```php
use OpenFGA\{Client, Authentication};
use OpenFGA\Responses\CreateStoreResponseInterface;

use function OpenFGA\Result\{fold, success, failure};

$client = new Client(
    url: $_ENV['FGA_API_URL'],
);

$store = $client->createStore(name: 'my-store');

$message = fold(
    $store,
    fn(CreateStoreResponseInterface $store) => "Store created: {$store->getId()}",
    fn(Throwable $err) => "Store creation failed: {$err->getMessage()}"
);

failure($store, fn(Throwable $error) => {
    // Store creation failed. Log or gracefully handle the error.
    throw $error;
});

success($store, fn(CreateStoreResponseInterface $store) => {
    // Delete the store we created with our `createStore()` call above.
    $client->deleteStore(store: $store->getId());
});
```

## Helpers

### fold()

Resolves a Result to a value.

```php
use OpenFGA\Results\Success;
use function OpenFGA\Result\fold;

$result = new Success('We did it!');

$value = fold(
    result: $result,
    onSuccess: fn($value) => "Yay! $value",
    onFailure: fn($err) => "Nay! $err"
);

// $value === 'Yay! We did it!'
```

### success()

```php
use OpenFGA\Results\Success;
use function OpenFGA\Result\success;

$result = new Success('We did it!');

success($result, fn($value) => {
    echo "Yay! $value";
});
```

### failure()

```php
use OpenFGA\Results\Failure;
use function OpenFGA\Result\failure;

$result = new Failure(new Exception('We failed!'));

failure($result, fn(Throwable $error) => {
    throw $error; // We failed!
});
```

## Result Methods

Results can also be manipulated using a number of built-in methods.

| Method        | Purpose                | Works On  |
| ------------- | ---------------------- | --------- |
| then(fn)      | Transform result       | Both      |
| map(fn)       | Transform value        | `Success` |
| mapError(fn)  | Transform error        | `Failure` |
| tap(fn)       | Side-effect            | `Success` |
| tapError(fn)  | Side-effect            | `Failure` |
| onSuccess(fn) | Fluent branching       | Both      |
| onFailure(fn) | Fluent branching       | Both      |
| fold(...)     | Resolve into one value | Both      |
| unwrap()      | Return value           | Both      |

### Usage

- `then(callable $fn): ResultInterface` transforms the value into another Result.

  ```php
  function findUser(int $id): ResultInterface {
      return $id > 0
          ? new Success(['id' => $id, 'name' => 'Alice'])
          : new Failure(new RuntimeException('User not found'));
  }

  $result = (new Success(5))
      ->then('findUser'); // ResultInterface<array, Throwable>
  ```

- `map(callable $fn): ResultInterface` transforms the value when a `Success`.

  ```php
  $result = new Success(['id' => 123]);
  // $result is Success<array<string, int>>

  $transformed = $result->map(fn(array $data) => $data['id']);
  // $transformed is Success<int>

- `mapError(callable $fn): ResultInterface` transforms the error value when a `Failure`.

  ```php
  $result = Failure(new Exception('We failed!'));
  // $result is Failure<Throwable>

  $transformed = $result->mapError(fn(Throwable $error) => $error->getMessage());
  // $transformed is Failure<string>
  ```

- `tap(callable $fn): ResultInterface` performs a side-effect when a `Success`, without altering the result.

  ```php
  $result = Success(['id' => 123]);
  // $result is Success<array<string, int>>

  $result->tap(fn(array $data) => print "ID: {$data['id']}\n");
  // $result is still Success<array<string, int>>
  ```

- `tapError(callable $fn): ResultInterface` performs a side-effect when a `Failure`, without altering the result.

  ```php
  $result = Failure(new Exception('We failed!'));
  // $result is Failure<Throwable>

  $result->tapError(fn(Throwable $error) => echo "Error: {$error->getMessage()}\n");
  // $result is still Failure<Throwable>
  ```

- `onSuccess(callable $fn): ResultInterface` branches on `Success`, without altering the result.

  ```php
  $result = Success(['id' => 123]);
  // $result is Success<array<string, int>>

  $result->onSuccess(fn(array $data) => echo "ID: {$data['id']}");
  // $result is still Success<array<string, int>>
  ```

- `onFailure(callable $fn): ResultInterface` branches on `Failure`, without altering the result.

  ```php
  $result = Failure(new Exception('We failed!'));
  // $result is Failure<Throwable>

  $result->onFailure(fn(Throwable $error) => echo "Error: {$error->getMessage()}");
  // $result is still Failure<Throwable>
  ```

- `unwrap(mixed $default = null): mixed` returns the success value, or a default value if the result is a `Failure`.

  ```php
  $success = Success(['id' => 123]);
  $value = $success->unwrap([]); // Returns ['id' => 123]

  $failure = Failure(new Exception('We failed!'));
  $value = $failure->unwrap([]); // Returns []
  ```

### Feature Comparisons

| Feature      | `tap()` / `tapError()`           | `onSuccess()` / `onFailure()`       |
| ------------ | -------------------------------- | ----------------------------------- |
| Return value | Original `ResultInterface<T, E>` | Usually `void`                      |
| Chainable?   | ✅ Yes                           | ❌ Usually not                      |
| Side-effect  | ✅ Yes                           | ✅ Yes                              |
| Control flow | ❌ No branching                  | ✅ Yes — used to act based on state |
