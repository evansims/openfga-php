# Changelog

## [Unreleased]

## [1.6.0] - 2025-07-07

### Added

- **New Helper Functions**<br />
  - **`grant` - Grant permissions between users and objects**<br />
    A more intuitive alias for `write()` that clearly expresses the intent of granting permissions.
    
    ```php
    use function OpenFGA\{grant, tuple};
    
    grant(tuple('user:anne', 'viewer', 'document:budget'));
    ```
    
  - **`revoke` - Revoke permissions between users and objects**<br />
    A more intuitive alias for `delete()` that clearly expresses the intent of revoking permissions.
    
    ```php
    use function OpenFGA\{revoke, tuple};
    
    revoke(tuple('user:anne', 'editor', 'document:budget'));
    ```

## [1.5.0] - 2025-06-22

### Added

- **Context Management**<br />
  This release introduces a new `context` helper function that provides a way to manage ambient context.
  This allows you to set a client, store, and model once and use them implicitly by helper functions.

  ```php
  use function OpenFGA\{context, objects};

  $userId = 'user:alice';

  context(function() use ($userId) {
    // Uses ambient context
    $editable = objects(
      user: "user:{$userId}",
      relation: 'editor',
      type: 'document',
    );

    print_r($editable);
  }, client: $client, store: 'my-store', model: 'my-model');
  ```

- **New Helper Functions**<br />

  - **`users` - Identify who has a particular permission with a resource**<br />
    There's also new `filters` and `filter` helpers that provide a more concise way to create `UserTypeFilters` collections and `UserTypeFilter` objects for `listUsers` requests.

    ```php
    use function OpenFGA\{users, filter, filters};

    $result = users(
      object: 'document:roadmap',
      relation: 'viewer',
      filters: filters(
        filter('user'),
        filter('group', 'member'),
      ),
    );
    ```

  - **`objects` - Retrieve a list of objects a particular user has access to.**<br />

    ```php
    use function OpenFGA\objects;

    $result = objects(
      type: 'document',
      relation: 'viewer',
      user: 'user:anne',
    );
    ```

  - **`checks` - Check multiple permissions at once**<br />
    There's also a new `check` helper that provides a more concise way to create `BatchCheckItem` objects for `batchCheck` requests.

    ```php
    use function OpenFGA\{check, checks};

    $result = checks(
      checks: check(
        user: 'user:anne',
        relation: 'viewer',
        object: 'document:roadmap',
      ),
    );
    ```

  - **`read` - Retrieves all tuples for a particular object.**<br />

    ```php
    use function OpenFGA\read;

    $result = read(
      object: 'document:roadmap',
      relation: 'viewer',
    );
    ```

  - **`changes` - Retrieves all tuple changes.**<br />

    ```php
    use function OpenFGA\changes;

    $result = changes(
      store: 'my-store',
      startTime: new DateTimeImmutable('-1 hour'),
    );
    ```

### Changed

- All helper functions now support omitting `client`, `store`, and `model` parameters when using the `context()` helper.
- The `batch` helper has been renamed to `writes`.
- The `allowed` helper now supports omitting `tuple` parameter.
  It now directly accepts user, relation, object and condition as optional parameters.
  When no tuple is provided, it will create a tuple for you based on the parameters.
  It will throw an exception if no tuple is provided and no parameters are provided.

## [1.4.0] - June 14, 2025

> _New languages spread love, twelve brand new,<br />
> From Chinese to Swedish, German too!<br />
> Pride month brings colors, code brings inclusion,<br />
> Language enum ends all confusion.<br />
> Fixed loops and docs with care so bright,<br />
> OpenFGA shines in rainbow light! 🏳️‍🌈✨_

### Added

- **Expanded Internationalization**<br />
  This release adds i18n support for Chinese (Simplified), Dutch, French, German, Italian, Japanese,
  Korean, Portuguese (Brazilian), Russian, Swedish, Turkish, and Ukrainian, alongside our existing
  Spanish and English support. We'd eagerly welcome contributions to improve the accuracy of these
  translations.

  We've also introduced a new Language enum to simplify working with i18n:

  ```php
  use OpenFGA\{Client, Language};

  $client = new Client(
      url: 'http://127.0.0.1:8080',
      language: Language::English,
  );
  ```

  You can use the new `lang()` helper to quickly return a Language enum based on a language code string:

  ```php
  use OpenFGA\Client;
  use function OpenFGA\lang;

  $client = new Client(
      url: 'http://127.0.0.1:8080',
      language: lang('en'),
  );
  ```

### Fixed

- Resolved an issue with operator precedence in the DSL transformer.
- Resolved a potential infinite loop issue in fiber concurrency.

### Documentation

- Various improvements to our written guides, particularly around Observability.
- Improvements to source code comment blocks to improve generated API documentation and IDE hinting.

## [1.3.0] - June 10, 2025

> _Threads and fibers dance,<br />
> Concurrent requests take their chance,<br />
> Speed finds new romance._

### Added

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

- A new `batch` helper function has been added to simplify batch tuple operations. This new helper
  supports the full range of parameters available in `writeTuples` and provides a more concise API
  for common use cases.

### Changed

- **Tuple Operations**

  - New parameters have been added to `writeTuples` to support configurable parallelism and chunking,
    retries and retry delay, and stop-on-first-error behavior.

  - The `writeTuples` method now filters out duplicate tuples before issuing requests.

  - A new `transactional` parameter has been added to the `write` and `delete` helper functions to support
    the `writeTuples` changes.

  - The `WriteTuplesResponse` class returned by `writeTuples` now includes detailed results about the operations.

- Enhanced API documentation generator with translation tables for Messages class
- Added OPENAPI_MODEL constants to BatchCheckItem and BatchCheckSingleResult classes

### Documentation

- Added [a new example](examples/non-transactional-writes/example.php) of non-transactional tuple writes
- Added [a new example](examples/duplicate-filtering/example.php) demonstrating duplicate tuple filtering
- Added [a new guide](docs/Concurrency.md) on concurrency and parallelism
- Added [a new guide](docs/Exceptions.md) on exception handling

## [1.2.0] - June 2, 2025

> _Streaming objects flow,<br />
> Batched checks in one swift go,<br />
> Watch your limits grow._

### Added

- **Larger result sets with `StreamedListObjects`**<br />
  While `ListObjects` has a limit of 1000 results, the `StreamedListObjects` endpoint has no specific
  result limits. When using this API, a network connection will remain open until the server has no
  more results to return, or after a timeout of 3 seconds, whichever occurs first.

- **Batched authorization checks with `BatchCheck`**<br />
  This endpoint allows you to perform up to 50 authorization checks at once in a single request.

- **Automatic retry with smart backoff**<br />
  Network hiccups happen. The SDK now automatically retries failed requests using exponential
  backoff with jitter, preventing thundering herd problems while ensuring your requests
  eventually succeed.

  - Configure retry attempts with the `httpMaxRetries` parameter (default: 3, max: 15)
  - Set to 0 if you prefer to handle retries yourself
  - The backoff algorithm spaces out retries intelligently to avoid overwhelming your FGA instance

- **Observability support**<br />
  Understanding what's happening in production just got easier. The SDK now supports OpenTelemetry
  for metrics, tracing, and logging out of the box.

  - Track authorization latencies, error rates, and throughput
  - Trace requests across your entire stack
  - Debug issues faster with correlated logs
  - Configure with the `telemetry` Client constructor parameter
  - See [the observability example](examples/observability/example.php) for a complete setup guide

- **Internationalization**<br />
  Error messages can now be displayed in your users' languages, making debugging easier for
  global teams.

  - Configure with the language parameter (defaults to English)
  - Currently supports Spanish and English

### Changed

- **Cleaner imports for helper functions**<br />
  We've streamlined how you import helper functions. Instead of multiple import statements, you can
  now grab everything you need in one line:

  ```php
  use function OpenFGA\{allowed, dsl, model, store, tuple, write, ...};
  ```

  _To avoid confusion, previous release notes have been updated to reflect this change._

- **Centralized exception handling**<br />
  All exception messages now come from a single source, making them more consistent and easier to
  maintain. This sets the foundation for better error messages in future releases.

## [1.1.0] - May 30, 2025

> _Tokens and configs bright,<br />
> Type safety brings such delight,<br />
> Code shines clear and right._

### Added

- **Streamlined model management with helper functions**<br />
  Managing FGA stores and models just got a lot simpler. We've introduced a suite of helper functions
  that reduce boilerplate and make your authorization code more readable.

  - **`store` - Find or create stores effortlessly**<br />
    No more manual store lookups. This helper finds your store by name or creates it if it doesn't exist:

    ```php
    use function OpenFGA\store;

    $store = store(
        client: $client,
        name: 'my-php-store',
    );
    ```

  - **`dsl` - Write models in plain text**<br />
    Define your authorization models using FGA's intuitive DSL syntax instead of verbose JSON:

    ```php
    use function OpenFGA\dsl;

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

  - **`model` - Deploy models with one call**<br />
    Transform your DSL into a live authorization model with automatic version management:

    ```php
    use function OpenFGA\model;

    $model = model(
        client: $client,
        store: $store,
        typeDefinitions: $dsl->getTypeDefinitions(),
    );
    ```

- **Simplified permission management**<br />
  Three new helper functions make it dead simple to manage permissions in your application:

  - **`write` - Grant permissions**

    ```php
    use function OpenFGA\write;

    $tuple = tuple('user:anne', 'viewer', 'document:roadmap');

    // "Anne has viewer access to roadmap"
    write(
        client: $client,
        store: $store,
        model: $model,
        tuples: $tuple,
    );
    ```

  - **`delete` - Revoke permissions**

    ```php
    use function OpenFGA\delete;

    $tuple = tuple('user:anne', 'viewer', 'document:roadmap');

    // "Anne no longer has viewer access to roadmap"
    delete(
        client: $client,
        store: $store,
        model: $model,
        tuples: $tuple,
    );
    ```

  - **`allowed` - Check permissions**

    ```php
    use function OpenFGA\allowed;

    $tuple = tuple('user:anne', 'viewer', 'document:roadmap');

    // "Can Anne view the roadmap?"
    allowed(
        client: $client,
        store: $store,
        model: $model,
        tupleKey: $tuple,
    );
    ```

These helpers eliminate the need to manually construct request objects,
making your authorization code cleaner and less error-prone.

### Fixed

- **More robust schema validation**<br />
  We've strengthened the DSL validator to handle edge cases that previously slipped through.
  Your models will now be validated more thoroughly before deployment, catching potential issues early.

## [1.0.0] - May 29, 2025

> _First release takes flight,<br />
> OpenFGA PHP shines bright,<br />
> Auth made clean and light._

### Introducing the OpenFGA PHP SDK

We're excited to release the first stable version of the OpenFGA PHP SDK - a modern, type-safe way
to add fine-grained authorization to your PHP applications.

### Core features

- **Complete OpenFGA API coverage**<br />
  Every OpenFGA endpoint is supported, fully typed, and ready to use.
  Whether you're checking permissions, managing stores, or writing tuples,
  we've got you covered with a clean, intuitive API.

- **Works with any HTTP client**<br />
  Built on PSR-7/17/18 standards, the SDK works with whatever HTTP client you're already using -
  Guzzle, Symfony HttpClient, or any PSR-18 compatible client. No forced dependencies.

- **Human-readable authorization models**<br />
  Write your authorization models in OpenFGA's DSL syntax.
  Our transformer handles the conversion and validates your models before deployment.

- **Comprehensive validation with helpful errors**<br />
  Every request and response is validated against OpenFGA's schemas.
  When something goes wrong, you get clear, actionable error messages that tell you exactly what to fix.

- **Built for modern PHP**<br />
  Takes full advantage of modern language features like named arguments, union types, and enums.
  Your IDE will love the strict typing and interface-first design.

- **Battle-tested reliability**<br />
  Over 95% test coverage including integration tests against real OpenFGA instances
  and contract tests to ensure compatibility. We test against multiple PHP versions
  and OpenFGA releases to ensure stability.

- **Authentication flexibility**<br />
  Support for multiple authentication methods including pre-shared keys and client credentials.

- **Rich documentation**<br />
  Comprehensive guides, API documentation, and real-world examples.
  Start with our quickstart guide to get up and running in minutes.
