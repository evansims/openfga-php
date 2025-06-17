**Assertions can be thought of as unit tests for your permission system.** They let you define what should and shouldn't be allowed, then verify your authorization model works correctly before deploying it to production.

## Prerequisites

Before working with assertions, ensure you have the following setup:

[Snippet](../../examples/snippets/assertions-setup.php)

## What are assertions

Assertions are test cases that specify expected outcomes for permission checks. Each assertion says "user X should (or shouldn't) have permission Y on resource Z" and verifies this against your authorization model.

## Writing your first test

Let's say you're building a document management system. You want to test that owners can edit documents but viewers cannot:

[Snippet](../../examples/snippets/assertions-basic.php#quickstart)

## Testing permission inheritance

Complex authorization models often have inherited permissions. Test these relationships to ensure they work as expected:

[Snippet](../../examples/snippets/assertions-basic.php#inheritance)

## Testing edge cases

Test boundary conditions and special cases in your permission model:

[Snippet](../../examples/snippets/assertions-basic.php#edge-cases)

## Managing test data

Organize your assertions logically and keep them maintainable:

[Snippet](../../examples/snippets/assertions-test-class.php#intro)

## Best practices

**Start with critical paths**: test the most important permission checks first - admin access, user data privacy, billing permissions.

**Test both positive and negative cases**: don't just test what should work, test what should be blocked.

**Use realistic data**: test with actual user IDs, resource names, and permission types from your application.

**Update tests when models change**: assertions should evolve with your authorization model. Treat them like any other test suite.

**Validate before deployment**: run assertions in your CI/CD pipeline to catch permission regressions before they reach production.

## CI/CD Integration

Integrate assertion testing into your deployment pipeline to catch permission regressions before they reach production.

### GitHub Actions Example

```yaml
# .github/workflows/authorization-tests.yml
name: Authorization Model Tests

on:
  push:
    paths:
      - "authorization-models/**"
      - "tests/authorization/**"
  pull_request:
    paths:
      - "authorization-models/**"
      - "tests/authorization/**"

jobs:
  test-authorization:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: "8.3"

      - name: Install dependencies
        run: composer install --no-dev --optimize-autoloader

      - name: Start OpenFGA Server
        run: |
          docker run -d --name openfga \
            -p 8080:8080 \
            openfga/openfga:latest \
            run --playground-enabled

      - name: Wait for OpenFGA
        run: |
          timeout 30 bash -c 'until curl -f http://localhost:8080/healthz; do sleep 1; done'

      - name: Run Authorization Tests
        run: php tests/authorization/run-assertions.php
        env:
          FGA_API_URL: http://localhost:8080
```

### Test Runner Script

[Snippet](../../examples/snippets/assertions-test-runner.php)

### Model Assertions File Example

[Snippet](../../examples/snippets/assertions-model-file.php)

### Integration with Testing Frameworks

#### PHPUnit Integration

[Snippet](../../examples/snippets/assertions-phpunit.php)

### Docker Compose for Local Testing

[Snippet](../../examples/snippets/assertions-docker-compose.yml)

Run with: `docker-compose -f docker-compose.test.yml up --build`

Remember: assertions replace all existing tests for a model when you call `writeAssertions()`. Always include your complete test suite in each call.
