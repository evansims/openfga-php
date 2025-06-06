# CONTRIBUTING

Contributions are welcome, and are accepted via pull requests.
Please review these guidelines before submitting any pull requests.

## Process

1. Fork the project
1. Create a new branch
1. Code, test, commit and push
1. Open a pull request detailing your changes. Make sure to follow the [template](.github/PULL_REQUEST_TEMPLATE.md)

## Guidelines

- Please ensure the coding style running `composer lint`.
- Send a coherent commit history, making sure each individual commit in your pull request is meaningful.
- If your commit history is long, please squash your commits.
- You may need to [rebase](https://git-scm.com/book/en/v2/Git-Branching-Rebasing) to avoid merge conflicts.
- Please remember that we follow [SemVer](http://semver.org/).

## Error Handling Strategy

The OpenFGA PHP SDK uses the Result pattern for all public API methods. This provides a consistent, predictable way to handle both success and failure cases without relying on exceptions.

### Basic Usage

```php
$result = $client->check(...);

// Handle success and failure
$result->success(fn($response) => echo "Allowed: {$response->getAllowed()}")
       ->failure(fn($error) => error_log("Check failed: {$error->getMessage()}"));

// Get the value or throw exception
try {
    $response = $result->unwrap();
} catch (Throwable $e) {
    // Handle error
}
```

### Result Methods

- `success(callable $callback): self` - Execute callback on success
- `failure(callable $callback): self` - Execute callback on failure
- `then(callable $callback): self` - Transform success value
- `recover(callable $callback): self` - Transform failure to success
- `unwrap(?callable $callback = null): mixed` - Get value or throw/transform
- `isSuccess(): bool` - Check if result is successful
- `isFailure(): bool` - Check if result is failure

### Why Result Pattern?

1. **Explicit Error Handling**: Forces developers to consider both success and failure cases
2. **Composability**: Chain operations without nested try-catch blocks
3. **Type Safety**: Return types clearly indicate possibility of failure
4. **Consistency**: All SDK methods behave the same way
5. **Flexibility**: Choose between exception-based or value-based error handling

## Setup

Clone your fork, then install the dev dependencies:

```bash
composer install
```

## Lint

Lint your code:

```bash
composer lint
```

Lint and fix:

```bash
composer lint:fix
```

## Tests

Everything:

```bash
composer test
```

Unit tests:

```bash
composer test:unit
```

Integration tests require Docker. The container starts automatically:

```bash
composer test:integration
```

Contract tests download the OpenAPI spec and validate the SDK's models against it:

```bash
composer test:contract
```

## Documentation

Update the documentation:

```bash
composer docs:api
```

Update the wiki:

```bash
composer docs:wiki
```

Note: You must have maintainer privileges to update the wiki.

## Making a Release

```bash
composer release X.Y.Z
```
