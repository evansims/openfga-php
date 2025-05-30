# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Essential Commands

### Testing

- `composer test` - Run unit tests with coverage and linting
- `composer test:unit` - Run unit tests with coverage
- `composer test:integration` - Run integration tests (requires Docker containers: `composer integration:start`)
- `composer test:contract` - Run OpenAPI contract tests
- Run a single test: `./vendor/bin/pest tests/Unit/Path/To/Test.php --filter="test name"`
- If PEST exits with code 255, it means there is a syntax error in the test or codebase that needs fixed

### Code Quality

- `composer lint` - Run all linters (PHPStan, Psalm, Rector, PHP-CS-Fixer)
- `composer phpstan` - Static analysis at max level
- `composer psalm` - Static analysis at max level
- `composer phpcs:fix` - Auto-fix code style issues
- `composer rector:fix` - Auto-modernize code

### Documentation

- `composer docs` - Regenerate API documentation from code
- `composer wiki` - Regenerate and upload the GitHub repository's wiki documentation from code

## Architecture Overview

This is an OpenFGA PHP SDK implementing relationship-based access control. Key architectural patterns:

### Result Pattern

All client methods return `Success`/`Failure` objects instead of throwing exceptions:

```php
$result = $client->check(...);
$result->success(fn($response) => /* handle success */)
       ->failure(fn($error) => /* handle error */)
       ->unwrap(); // Get the value or throw
```

### Interface-First Design

Every major class has a corresponding interface (e.g., `Client` implements `ClientInterface`). Always code against interfaces when possible.

### Domain Model Structure

- **Models/** - Immutable domain objects with strict typing
- **Collections/** - Type-safe collections extending `IndexedCollection` or `KeyedCollection`
- **Requests/** - API request DTOs with validation
- **Responses/** - API response DTOs
- **Schema/** - JSON schema validation system for all models

### DSL Transformer

The SDK includes a DSL parser (`Language/DslTransformer`) that converts human-readable authorization models to API objects:

```php
$model = $client->dsl($dslString)->unwrap();
```

### PSR Compliance

- Uses PSR-7 for HTTP messages
- PSR-17 for HTTP factories
- PSR-18 for HTTP clients
- Client accepts any PSR-compatible implementations

## Development Guidelines

### Adding New Features

1. Create interface first in the corresponding `*Interface.php` file
2. Implement the interface with full PHPDoc
3. Add schema validation if it's a model class
4. Write unit tests covering all paths
5. Update relevant documentation files

### Testing Requirements

- Unit tests are required for all new functionality
- Integration tests for new API endpoints
- Contract tests must pass against OpenAPI spec
- Minimum 80% code coverage

### Code Standards

- PHP 8.3+ features are encouraged (e.g., readonly properties, typed class constants)
- All public methods must have complete PHPDoc with @throws annotations
- Use strict typing (`declare(strict_types=1)`)
- Follow existing patterns for consistency
