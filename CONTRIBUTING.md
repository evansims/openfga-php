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

## Tests

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
composer docs
```

Update the wiki:

```bash
composer wiki
```

Note: You must have maintainer privileges to update the wiki.
