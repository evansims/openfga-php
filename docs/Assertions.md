# Testing Your Authorization Model

Think of assertions as unit tests for your permission system. They let you define what should and shouldn't be allowed, then verify your authorization model works correctly before deploying it to production.

## Prerequisites

Before working with assertions, ensure you have the following setup:

```php
use OpenFGA\Client;
use OpenFGA\Models\Assertion;
use OpenFGA\Collections\Assertions;
use function OpenFGA\{tuple, tuples};

// Initialize the client
$client = new Client(url: $_ENV['FGA_API_URL']);

// These variables are used throughout the examples
$storeId = 'your-store-id';
$modelId = 'your-authorization-model-id';
```

## What are assertions

Assertions are test cases that specify expected outcomes for permission checks. Each assertion says "user X should (or shouldn't) have permission Y on resource Z" and verifies this against your authorization model.

## Writing your first test

Let's say you're building a document management system. You want to test that owners can edit documents but viewers cannot:

```php
// Test: Document owners can edit
$ownerCanEdit = new Assertion(
    tupleKey: tuple(
        user: 'user:alice',
        relation: 'can_edit',
        object: 'document:quarterly-report'
    ),
    expectation: true
);

// Test: Viewers cannot edit
$viewerCannotEdit = new Assertion(
    tupleKey: tuple(
        user: 'user:bob',
        relation: 'can_edit',
        object: 'document:quarterly-report'
    ),
    expectation: false
);

$tests = new Assertions([$ownerCanEdit, $viewerCannotEdit]);

$client->writeAssertions(
    store: $storeId,
    model: $modelId,
    assertions: $tests
)->unwrap();
```

## Testing permission inheritance

Complex authorization models often have inherited permissions. Test these relationships to ensure they work as expected:

```php
// In a team workspace, team members inherit folder permissions
$teamFolderAccess = [
    // Direct team member access
    new Assertion(
        tupleKey: tuple('user:sarah', 'can_read', 'folder:team-docs'),
        expectation: true
    ),

    // Inherited document access through folder membership
    new Assertion(
        tupleKey: tuple('user:sarah', 'can_read', 'document:team-meeting-notes'),
        expectation: true
    ),

    // Non-team members should be denied
    new Assertion(
        tupleKey: tuple('user:outsider', 'can_read', 'folder:team-docs'),
        expectation: false
    ),
];
```

## Testing edge cases

Test boundary conditions and special cases in your permission model:

```php
$edgeCases = [
    // Public documents should be readable by anyone
    new Assertion(
        tupleKey: tuple('user:*', 'can_read', 'document:company-handbook'),
        expectation: true
    ),

    // Deleted users should lose all access
    new Assertion(
        tupleKey: tuple('user:former-employee', 'can_read', 'document:confidential'),
        expectation: false
    ),

    // Admin override permissions
    new Assertion(
        tupleKey: tuple('user:admin', 'can_delete', 'document:any-document'),
        expectation: true
    ),

    // Cross-organization access should be blocked
    new Assertion(
        tupleKey: tuple('user:competitor', 'can_read', 'document:internal-strategy'),
        expectation: false
    ),
];
```

## Managing test data

Organize your assertions logically and keep them maintainable:

```php
class DocumentPermissionTests
{
    public static function getBasicPermissions(): array
    {
        return [
            // Owner permissions
            new Assertion(tuple('user:owner', 'can_read', 'document:doc1'), true),
            new Assertion(tuple('user:owner', 'can_edit', 'document:doc1'), true),
            new Assertion(tuple('user:owner', 'can_delete', 'document:doc1'), true),

            // Editor permissions
            new Assertion(tuple('user:editor', 'can_read', 'document:doc1'), true),
            new Assertion(tuple('user:editor', 'can_edit', 'document:doc1'), true),
            new Assertion(tuple('user:editor', 'can_delete', 'document:doc1'), false),

            // Viewer permissions
            new Assertion(tuple('user:viewer', 'can_read', 'document:doc1'), true),
            new Assertion(tuple('user:viewer', 'can_edit', 'document:doc1'), false),
            new Assertion(tuple('user:viewer', 'can_delete', 'document:doc1'), false),
        ];
    }

    public static function getInheritanceTests(): array
    {
        return [
            // Team lead inherits team permissions
            new Assertion(tuple('user:team-lead', 'can_manage', 'team:engineering'), true),
            new Assertion(tuple('user:team-lead', 'can_read', 'document:team-roadmap'), true),
        ];
    }
}

// Write different test suites
$client->writeAssertions(
    store: $storeId,
    model: $modelId,
    assertions: new Assertions([
        ...DocumentPermissionTests::getBasicPermissions(),
        ...DocumentPermissionTests::getInheritanceTests(),
    ])
)->unwrap();
```

## Best practices

**Start with critical paths**: test the most important permission checks first - admin access, user data privacy, billing permissions.

**Test both positive and negative cases**: don't just test what should work, test what should be blocked.

**Use realistic data**: test with actual user IDs, resource names, and permission types from your application.

**Update tests when models change**: assertions should evolve with your authorization model. Treat them like any other test suite.

**Validate before deployment**: run assertions in your CI/CD pipeline to catch permission regressions before they reach production.

```php
// Reading existing assertions for review
$response = $client->readAssertions(
    store: $storeId,
    model: $modelId,
)->unwrap();

foreach ($response->getAssertions() as $assertion) {
    $key = $assertion->getTupleKey();
    $expected = $assertion->getExpectation() ? 'CAN' : 'CANNOT';

    echo "{$key->getUser()} {$expected} {$key->getRelation()} {$key->getObject()}\n";
}
```

## CI/CD Integration

Integrate assertion testing into your deployment pipeline to catch permission regressions before they reach production.

### GitHub Actions Example

```yaml
# .github/workflows/authorization-tests.yml
name: Authorization Model Tests

on:
  push:
    paths:
      - 'authorization-models/**'
      - 'tests/authorization/**'
  pull_request:
    paths:
      - 'authorization-models/**'
      - 'tests/authorization/**'

jobs:
  test-authorization:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          
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

```php
<?php
// tests/authorization/run-assertions.php

require_once __DIR__ . '/../../vendor/autoload.php';

use OpenFGA\Client;
use OpenFGA\Models\Assertions;
use function OpenFGA\{store, model};

/**
 * Authorization test runner for CI/CD pipelines.
 * 
 * This script validates authorization models against assertions to ensure
 * permission logic works correctly before deployment.
 */
class AuthorizationTestRunner
{
    private Client $client;
    
    public function __construct()
    {
        $this->client = new Client(
            url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080'
        );
    }
    
    public function runAllTests(): bool
    {
        $success = true;
        
        // Test each model file in your project
        $modelFiles = glob(__DIR__ . '/../../authorization-models/*.fga');
        
        foreach ($modelFiles as $modelFile) {
            $modelName = basename($modelFile, '.fga');
            echo "Testing model: {$modelName}\n";
            
            $success = $this->testModel($modelFile) && $success;
        }
        
        return $success;
    }
    
    private function testModel(string $modelFile): bool
    {
        try {
            // Create test store
            $storeId = store($this->client, "test-{$modelFile}-" . time());
            
            // Load and create model from DSL file
            $dsl = file_get_contents($modelFile);
            $authModel = $this->client->dsl($dsl)->unwrap();
            $modelId = model($this->client, $storeId, $authModel);
            
            // Load assertions for this model
            $assertionsFile = str_replace('.fga', '.assertions.php', $modelFile);
            if (!file_exists($assertionsFile)) {
                echo "  Warning: No assertions file found for {$modelFile}\n";
                return true;
            }
            
            $assertions = require $assertionsFile;
            
            // Write assertions and validate
            $this->client->writeAssertions(
                store: $storeId,
                model: $modelId,
                assertions: new Assertions($assertions)
            )->unwrap();
            
            echo "  ✓ All assertions passed\n";
            
            // Clean up test store
            $this->client->deleteStore($storeId)->unwrap();
            
            return true;
            
        } catch (Exception $e) {
            echo "  ✗ Test failed: {$e->getMessage()}\n";
            return false;
        }
    }
}

// Run tests
$runner = new AuthorizationTestRunner();
$success = $runner->runAllTests();

exit($success ? 0 : 1);
```

### Model Assertions File Example

```php
<?php
// authorization-models/document-system.assertions.php

use OpenFGA\Models\Assertion;
use function OpenFGA\{tuple};

return [
    // Document ownership tests
    new Assertion(tuple('user:alice', 'owner', 'document:1'), true),
    new Assertion(tuple('user:bob', 'owner', 'document:1'), false),
    
    // Inherited permissions tests
    new Assertion(tuple('user:alice', 'editor', 'document:1'), true), // Owner can edit
    new Assertion(tuple('user:alice', 'viewer', 'document:1'), true), // Owner can view
    
    // Team permissions tests
    new Assertion(tuple('team:engineering#member', 'viewer', 'document:roadmap'), true),
    new Assertion(tuple('team:marketing#member', 'viewer', 'document:roadmap'), false),
    
    // Conditional permissions tests
    new Assertion(tuple('user:contractor', 'viewer', 'document:sensitive'), true),
    // Note: Conditional assertions require context to be set during testing
];
```

### Integration with Testing Frameworks

#### PHPUnit Integration

```php
<?php
// tests/Unit/AuthorizationModelTest.php

use PHPUnit\Framework\TestCase;
use OpenFGA\Client;
use OpenFGA\Models\Assertions;

class AuthorizationModelTest extends TestCase
{
    private Client $client;
    private string $storeId;
    private string $modelId;
    
    protected function setUp(): void
    {
        $this->client = new Client(url: $_ENV['FGA_API_URL']);
        
        // Create test store and model
        $this->storeId = store($this->client, 'test-' . uniqid());
        $dsl = file_get_contents(__DIR__ . '/../../authorization-models/main.fga');
        $authModel = $this->client->dsl($dsl)->unwrap();
        $this->modelId = model($this->client, $this->storeId, $authModel);
    }
    
    protected function tearDown(): void
    {
        // Clean up test store
        $this->client->deleteStore($this->storeId);
    }
    
    public function testDocumentPermissions(): void
    {
        $assertions = require __DIR__ . '/../../authorization-models/document-system.assertions.php';
        
        $result = $this->client->writeAssertions(
            store: $this->storeId,
            model: $this->modelId,
            assertions: new Assertions($assertions)
        );
        
        $this->assertTrue($result->succeeded());
    }
}
```

### Docker Compose for Local Testing

```yaml
# docker-compose.test.yml
version: '3.8'

services:
  openfga:
    image: openfga/openfga:latest
    command: run --playground-enabled
    ports:
      - "8080:8080"
    environment:
      - OPENFGA_DATASTORE_ENGINE=memory
      
  php-tests:
    build: .
    depends_on:
      - openfga
    environment:
      - FGA_API_URL=http://openfga:8080
    volumes:
      - .:/app
    working_dir: /app
    command: php tests/authorization/run-assertions.php
```

Run with: `docker-compose -f docker-compose.test.yml up --build`

Remember: assertions replace all existing tests for a model when you call `writeAssertions()`. Always include your complete test suite in each call.
