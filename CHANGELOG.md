# Changelog

## [Unreleased]

### Added

- **Support for multithreading**<br />
  The SDK now supports multithreading using PHP's [threads](https://www.php.net/manual/en/language.threads.php) language feature,
  providing support for concurrent HTTP requests and improved performance.

- **Support for concurrency using Fibers**<br />
  The SDK now internally uses PHP's [Fibers](https://www.php.net/manual/en/language.fibers.php) language feature,
  providing support for concurrent HTTP requests and improved performance.

- **Support for non-transactional tuple writes**<br />
  `writeTuples` now supports both transactional and non-transactional modes.

  - In **transactional** mode, all changes must succeed or the entire operation fails (atomic).
    Use when consistency matters - like granting a user multiple permissions at once.

  - In **non-transactional** mode, each batch succeeds or fails independently.
    If one batch fails, others may still succeed.
    Use for bulk syncs where you can retry failed batches.

  See [the docs](docs/Concurrency.md) for more information.

- A new `batch` helper function has been added to simplify batch tuple operations.
  This new helper supports the full range of parameters available in `writeTuples`
  and provides a more concise API for common use cases.

### Changed

- **Tuple Operations**

  - New parameters have been added to `writeTuples` to support configurable parallelism and chunking,
    retries and retry delay, and stop-on-first-error behavior.

  - The `writeTuples` method now filters out duplicate tuples before issuing requests.

  - A new `transactional` parameter has been added to the `write` and `delete` helper functions to support
    the `writeTuples` changes.

  - The `WriteTuplesResponse` class returned by `writeTuples` now includes detailed results about the operations.

- **Networking**

  - The `RequestManager` has been refactored.
    All requests now use the same Fiber-based concurrent infrastructure internally.

  - The `RetryHandler` class has been refactored into an abstract base class `AbstractRetryHandler` that can be
    extended for custom retry behavior (e.g., testing with reduced delays).

- **Architecture**

  - The chunk processing logic for non-transactional writes has been moved from `Client` to `WriteTuplesRequest`.
    This improves separation of concerns and encapsulates all write-related logic within the request object.

### Documentation

- Added [a new example](examples/non-transactional-writes/example.php) of non-transactional tuple writes
- Added [a new example](examples/duplicate-filtering/example.php) demonstrating duplicate tuple filtering
- Added [a new guide](docs/Concurrency.md) on concurrency and parallelism
- Added [a new guide](docs/Exceptions.md) on exception handling

## [1.2.0] - 2025-06-02

### Added

- New endpoints for `StreamedListObjects` and `BatchCheck`

- Automatic retry of failed network requests

  - Uses exponential backoff with jitter
  - Configurable with the `httpMaxRetries` Client constructor parameter
    - Defaults to 3 retries, max 15
    - Set to 0 to disable retries

- Observability support introduced

  - OpenTelemetry metrics, tracing and logging support
  - Configurable with the `telemetry` Client constructor parameter
  - See [examples/observability/example.php](examples/observability/example.php) for example usage.

- Added i18n support for exception messages

  - Configurable with the `language` Client constructor parameter
  - Defaults to `en` (English)

### Changed

- Simplified helper function imports

  ```php
  use function OpenFGA\{allowed, dsl, model, store, tuple, write, ...};
  ```

- Updated exception message handling to use a central location for all messages

## [1.1.0] - 2025-05-30

### Added

- DSL transformer enhancements
- `store` model helper function

  ```php
  use function OpenFGA\Models\store;

  $store = store(
    client: $client,
    name: 'my-php-store',
  );
  ```

- `dsl` model helper function

  ```php
  use function OpenFGA\Models\dsl;

  $dsl = dsl(<<<'DSL'
    model
    schema 1.1

    type user

    type document
      relations
        define viewer: [user]
        define editor: [user] and viewer
  DSL);
  ```

- `model` model helper function

  ```php
  use function OpenFGA\Models\model;

  $model = model(
    client: $client,
    store: $store,
    typeDefinitions: $dsl->getTypeDefinitions(),
  );
  ```

- `write` request helper function

  ```php
  use function OpenFGA\Requests\write;

  $tuple = tuple('user:anne', 'viewer', 'document:roadmap');

  // "Anne has viewer access to roadmap"
  write(
    client: $client,
    store: $store,
    model: $model,
    tuples: $tuple,
  );
  ```

- `delete` request helper function

  ```php
  use function OpenFGA\Requests\delete;

  $tuple = tuple('user:anne', 'viewer', 'document:roadmap');

  // "Anne no longer has viewer access to roadmap"
  delete(
    client: $client,
    store: $store,
    model: $model,
    tuples: $tuple,
  );
  ```

- `allowed` request helper function

  ```php
  use function OpenFGA\Requests\allowed;

  $tuple = tuple('user:anne', 'viewer', 'document:roadmap');

  // "Can Anne view the roadmap?"
  allowed(
    client: $client,
    store: $store,
    model: $model,
    tupleKey: $tuple,
  );
  ```

### Fixed

- Various fixes to schema validation to better handle edge cases

## [1.0.0] - 2025-05-29

<i>Fine-grained auth flows,<br />
PHP types guard each request,<br />
Permissions granted.</i>

### Added

- Complete OpenFGA API implementation with full type safety
- Result pattern for elegant error handling without exceptions
- PSR-7/17/18 HTTP compliance for maximum compatibility
- DSL transformer for human-readable authorization models
- Comprehensive schema validation with detailed error reporting
- Extensive test coverage (90%+) with integration and contract tests
- Rich documentation with GitHub Pages deployment
- PHP 8.3+ support with modern language features

### Features

- **Client SDK**: Full-featured client with all OpenFGA endpoints
- **Type Safety**: Strict typing throughout with interface-first design
- **Authentication**: Multiple auth methods including client credentials
- **Models**: Complete domain object model with collections
- **Validation**: JSON schema validation for all requests/responses
- **DSL**: Parse and generate authorization models from readable syntax
- **Results**: Success/Failure pattern inspired by Rust and functional languages
- **Testing**: Comprehensive test suite with contract validation
