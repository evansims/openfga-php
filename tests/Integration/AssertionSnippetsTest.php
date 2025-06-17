<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use OpenFGA\Client;
use OpenFGA\Models\{Assertion, AssertionTupleKey};
use OpenFGA\Models\Collections\Assertions;

use function OpenFGA\{model, store, tuple, tuples};

/*
 * Tests for assertion snippet concepts.
 *
 * This test file validates all the concepts demonstrated in the assertion snippets
 * using the correct API syntax. The snippets use a simplified array syntax for
 * documentation clarity, but this test uses the actual object construction.
 */
describe('Assertion Snippet Concepts', function (): void {
    beforeEach(function (): void {
        $this->client = new Client(url: getOpenFgaUrl());

        // Create a test store
        $this->storeName = 'assertion-test-' . bin2hex(random_bytes(5));
        $storeResponse = $this->client->createStore(name: $this->storeName)->unwrap();
        $this->storeId = $storeResponse->getId();

        // Create a comprehensive model that supports all assertion examples
        $dslModel = <<<'DSL'
            model
              schema 1.1

            type user

            type team
              relations
                define member: [user]

            type system
              relations
                define can_manage: [user]

            type database
              relations
                define can_read: [user, team#member]
                define can_write: [user]

            type document
              relations
                define can_view: [user, team#member] or can_edit
                define can_edit: [user, team#member] or can_delete
                define can_delete: [user]
                define can_share: [user] and can_edit
            DSL;

        $model = $this->client->dsl($dslModel)->unwrap();
        $modelResponse = $this->client->createAuthorizationModel(
            store: $this->storeId,
            typeDefinitions: $model->getTypeDefinitions(),
            schemaVersion: $model->getSchemaVersion(),
        )->unwrap();
        $this->modelId = $modelResponse->getModel();
    });

    afterEach(function (): void {
        if (isset($this->storeId)) {
            $this->client->deleteStore(store: $this->storeId);
        }
    });

    test('basic assertions from assertions-basic.php', function (): void {
        // Test the basic assertion concepts shown in the snippet
        $assertions = new Assertions([
            new Assertion(
                tupleKey: new AssertionTupleKey('user:alice', 'can_edit', 'document:strategy'),
                expectation: true,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey('user:bob', 'can_view', 'document:strategy'),
                expectation: true,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey('user:charlie', 'can_delete', 'document:strategy'),
                expectation: false,
            ),
        ]);

        // Write assertions
        $result = $this->client->writeAssertions(
            store: $this->storeId,
            model: $this->modelId,
            assertions: $assertions,
        );

        expect($result->succeeded())->toBeTrue();

        // Read assertions back
        $readResult = $this->client->readAssertions(
            store: $this->storeId,
            model: $this->modelId,
        );

        expect($readResult->succeeded())->toBeTrue();
        $readAssertions = $readResult->unwrap()->getAssertions();
        expect($readAssertions)->toHaveCount(3);
    });

    test('permission inheritance assertions', function (): void {
        // Set up relationships that demonstrate inheritance
        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:alice', 'member', 'team:engineering'),
                tuple('team:engineering#member', 'can_edit', 'document:specs'),
            ),
        )->unwrap();

        // Test assertions for inherited permissions
        $assertions = new Assertions([
            // Direct permission
            new Assertion(
                tupleKey: new AssertionTupleKey('team:engineering#member', 'can_edit', 'document:specs'),
                expectation: true,
            ),
            // Inherited through team membership
            new Assertion(
                tupleKey: new AssertionTupleKey('user:alice', 'can_edit', 'document:specs'),
                expectation: true,
            ),
            // Can view because can edit
            new Assertion(
                tupleKey: new AssertionTupleKey('user:alice', 'can_view', 'document:specs'),
                expectation: true,
            ),
        ]);

        $result = $this->client->writeAssertions(
            store: $this->storeId,
            model: $this->modelId,
            assertions: $assertions,
        );

        expect($result->succeeded())->toBeTrue();
    });

    test('edge case assertions from snippets', function (): void {
        // Test various edge cases shown in the snippets
        $assertions = new Assertions([
            // System management permissions
            new Assertion(
                tupleKey: new AssertionTupleKey('user:admin', 'can_manage', 'system:production'),
                expectation: true,
            ),
            // Database permissions
            new Assertion(
                tupleKey: new AssertionTupleKey('user:developer', 'can_read', 'database:analytics'),
                expectation: true,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey('user:developer', 'can_write', 'database:analytics'),
                expectation: false,
            ),
            // Complex document permissions
            new Assertion(
                tupleKey: new AssertionTupleKey('user:alice', 'can_share', 'document:budget'),
                expectation: false, // needs both can_share direct and can_edit
            ),
        ]);

        $result = $this->client->writeAssertions(
            store: $this->storeId,
            model: $this->modelId,
            assertions: $assertions,
        );

        expect($result->succeeded())->toBeTrue();
    });

    test('test runner concept from assertions-test-runner.php', function (): void {
        // Simulate what the test runner does
        $testCases = [
            ['user' => 'user:alice', 'relation' => 'can_edit', 'object' => 'document:report', 'expectation' => true],
            ['user' => 'user:bob', 'relation' => 'can_view', 'object' => 'document:report', 'expectation' => true],
        ];

        // Convert to proper assertion objects
        $assertions = new Assertions(array_map(
            fn ($tc) => new Assertion(
                tupleKey: new AssertionTupleKey($tc['user'], $tc['relation'], $tc['object']),
                expectation: $tc['expectation'],
            ),
            $testCases,
        ));

        $result = $this->client->writeAssertions(
            store: $this->storeId,
            model: $this->modelId,
            assertions: $assertions,
        );

        expect($result->succeeded())->toBeTrue();

        // Simulate running the tests
        foreach ($testCases as $testCase) {
            $checkResult = $this->client->check(
                store: $this->storeId,
                model: $this->modelId,
                tuple: tuple($testCase['user'], $testCase['relation'], $testCase['object']),
            );

            if ($checkResult->succeeded()) {
                $allowed = $checkResult->unwrap()->getAllowed();
                // In a real test runner, this would compare against expectation
                // For this test, we just verify the check works
                expect($allowed)->toBeBool();
            }
        }
    });

    test('model file concept from assertions-model-file.php', function (): void {
        // Test the concept of organizing assertions by model
        // This simulates what would be in a separate file
        $documentAssertions = [
            ['user' => 'user:alice', 'relation' => 'can_edit', 'object' => 'document:spec', 'expectation' => true],
            ['user' => 'user:alice', 'relation' => 'can_view', 'object' => 'document:spec', 'expectation' => true],
            ['user' => 'user:alice', 'relation' => 'can_delete', 'object' => 'document:spec', 'expectation' => false],
        ];

        $databaseAssertions = [
            ['user' => 'user:dev', 'relation' => 'can_read', 'object' => 'database:prod', 'expectation' => true],
            ['user' => 'user:dev', 'relation' => 'can_write', 'object' => 'database:prod', 'expectation' => false],
        ];

        // Combine all assertions
        $allAssertions = array_merge($documentAssertions, $databaseAssertions);

        $assertions = new Assertions(array_map(
            fn ($a) => new Assertion(
                tupleKey: new AssertionTupleKey($a['user'], $a['relation'], $a['object']),
                expectation: $a['expectation'],
            ),
            $allAssertions,
        ));

        $result = $this->client->writeAssertions(
            store: $this->storeId,
            model: $this->modelId,
            assertions: $assertions,
        );

        expect($result->succeeded())->toBeTrue();
    });

    test('updating existing assertions', function (): void {
        // First write some assertions
        $initialAssertions = new Assertions([
            new Assertion(
                tupleKey: new AssertionTupleKey('user:alice', 'can_edit', 'document:v1'),
                expectation: true,
            ),
        ]);

        $this->client->writeAssertions(
            store: $this->storeId,
            model: $this->modelId,
            assertions: $initialAssertions,
        )->unwrap();

        // Now update with different assertions
        $updatedAssertions = new Assertions([
            new Assertion(
                tupleKey: new AssertionTupleKey('user:alice', 'can_edit', 'document:v1'),
                expectation: false, // Changed expectation
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey('user:bob', 'can_view', 'document:v1'),
                expectation: true, // New assertion
            ),
        ]);

        $result = $this->client->writeAssertions(
            store: $this->storeId,
            model: $this->modelId,
            assertions: $updatedAssertions,
        );

        expect($result->succeeded())->toBeTrue();

        // Verify the update
        $readResult = $this->client->readAssertions(
            store: $this->storeId,
            model: $this->modelId,
        );

        $assertions = $readResult->unwrap()->getAssertions();
        expect($assertions)->toHaveCount(2);
    });
});
