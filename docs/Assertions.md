# Testing Your Authorization Model

Think of assertions as unit tests for your permission system. They let you define what should and shouldn't be allowed, then verify your authorization model works correctly before deploying it to production.

## What are assertions?

Assertions are test cases that specify expected outcomes for permission checks. Each assertion says "user X should (or shouldn't) have permission Y on resource Z" and verifies this against your authorization model.

```php
use OpenFGA\Client;
use function OpenFGA\{tuple, tuples};
use OpenFGA\Models\Assertion;
use OpenFGA\Collections\Assertions;

$client = new Client(url: $_ENV['FGA_API_URL']);
```

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

**Start with critical paths**: Test the most important permission checks first - admin access, user data privacy, billing permissions.

**Test both positive and negative cases**: Don't just test what should work, test what should be blocked.

**Use realistic data**: Test with actual user IDs, resource names, and permission types from your application.

**Update tests when models change**: Assertions should evolve with your authorization model. Treat them like any other test suite.

**Validate before deployment**: Run assertions in your CI/CD pipeline to catch permission regressions before they reach production.

```php
// Reading existing assertions for review
$response = $client->readAssertions(store: $storeId, model: $modelId)->unwrap();

foreach ($response->getAssertions() as $assertion) {
    $key = $assertion->getTupleKey();
    $expected = $assertion->getExpectation() ? 'CAN' : 'CANNOT';
    
    echo "{$key->getUser()} {$expected} {$key->getRelation()} {$key->getObject()}\n";
}
```

Remember: assertions replace all existing tests for a model when you call `writeAssertions()`. Always include your complete test suite in each call.
