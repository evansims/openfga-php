# CONTRIBUTING

Contributions are welcome, and are accepted via pull requests.
Please review these guidelines before submitting any pull requests.

## Process

1. Fork the project
1. Create a new branch
1. Code, test, commit and push
1. Open a pull request detailing your changes

## Development Workflow

Clone your fork, then install the dev dependencies:

```bash
composer install
```

## Running Linters

Lint your code:

```bash
composer lint
```

Lint and fix:

```bash
composer lint:fix
```

## Running Tests

Run all tests:

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

## Releasing

Use the `release` Composer command to create a new release.

```bash
composer release X.Y.Z
```

This command triggers a workflow that will:

- Update the CHANGELOG.md file
  - Renames the "Unreleased" section to the new version
- Update the version const in `Client.php`
- Run our linters and test suite
- Create a new git tag
- Push the tag to GitHub
- Regenerate the API documentation
- Update the GitHub wiki
- Update the LLM-friendly llms.txt
- Draft a new release on GitHub
