<?php

declare(strict_types=1);

use OpenFGA\Client;

// Initialize the OpenFGA client
$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

// Store configuration
$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: quickstart
use OpenFGA\Models\{Assertion, AssertionTupleKey};
use OpenFGA\Models\Collections\Assertions;

// Write your first test
$result = $client->writeAssertions(
    store: $storeId,
    model: $modelId,
    assertions: new Assertions(
        // Document owners can edit their documents
        new Assertion(
            tupleKey: new AssertionTupleKey('user:alice', 'can_edit', 'document:strategy'),
            expectation: true,
        ),
        // Users without any relationship cannot edit
        new Assertion(
            tupleKey: new AssertionTupleKey('user:bob', 'can_edit', 'document:strategy'),
            expectation: false,
        ),
        // Viewers cannot edit documents
        new Assertion(
            tupleKey: new AssertionTupleKey('user:charlie', 'can_edit', 'document:strategy'),
            expectation: false,
        ),
    ),
);

if ($result->succeeded()) {
    echo "✓ Assertions written successfully\n";
}
// end-example: quickstart

// example: inheritance
// Test permission inheritance
$result = $client->writeAssertions(
    store: $storeId,
    model: $modelId,
    assertions: new Assertions(
        // Team members can access workspace documents
        new Assertion(
            tupleKey: new AssertionTupleKey('user:alice', 'can_view', 'document:q4-report'),
            expectation: true,
        ),
        // Non-team members cannot access workspace documents
        new Assertion(
            tupleKey: new AssertionTupleKey('user:external', 'can_view', 'document:q4-report'),
            expectation: false,
        ),
        // Workspace admins can delete documents
        new Assertion(
            tupleKey: new AssertionTupleKey('user:admin', 'can_delete', 'document:q4-report'),
            expectation: true,
        ),
    ),
);
// end-example: inheritance

// example: edge-cases
// Test edge cases
$edgeCases = $client->writeAssertions(
    store: $storeId,
    model: $modelId,
    assertions: new Assertions(
        // Users cannot edit their own profile indirectly through groups
        new Assertion(
            tupleKey: new AssertionTupleKey('user:alice', 'can_edit', 'profile:alice'),
            expectation: false,
        ),
        // Super admins can access all resources
        new Assertion(
            tupleKey: new AssertionTupleKey('user:superadmin', 'can_manage', 'system:settings'),
            expectation: true,
        ),
        // Deleted users lose all permissions
        new Assertion(
            tupleKey: new AssertionTupleKey('user:deleted_user', 'can_view', 'document:any'),
            expectation: false,
        ),
        // Service accounts have limited permissions
        new Assertion(
            tupleKey: new AssertionTupleKey('service:backup', 'can_read', 'database:production'),
            expectation: true,
        ),
        new Assertion(
            tupleKey: new AssertionTupleKey('service:backup', 'can_write', 'database:production'),
            expectation: false,
        ),
    ),
);
// end-example: edge-cases
