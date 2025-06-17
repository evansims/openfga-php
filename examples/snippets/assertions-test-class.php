<?php

declare(strict_types=1);

use OpenFGA\Client;
use OpenFGA\Models\{Assertion, AssertionTupleKey};

final class DocumentPermissionTests
{
    public function __construct(
        private Client $client,
    ) {
    }

    public function getAssertions(): array
    {
        return [
            // Owner permissions
            new Assertion(
                tupleKey: new AssertionTupleKey(
                    user: 'user:document_owner',
                    relation: 'owner',
                    object: 'document:strategy_2024',
                ),
                expectation: true,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey(
                    user: 'user:document_owner',
                    relation: 'can_edit',
                    object: 'document:strategy_2024',
                ),
                expectation: true,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey(
                    user: 'user:document_owner',
                    relation: 'can_share',
                    object: 'document:strategy_2024',
                ),
                expectation: true,
            ),

            // Editor permissions
            new Assertion(
                tupleKey: new AssertionTupleKey(
                    user: 'user:editor',
                    relation: 'can_edit',
                    object: 'document:strategy_2024',
                ),
                expectation: true,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey(
                    user: 'user:editor',
                    relation: 'can_share',
                    object: 'document:strategy_2024',
                ),
                expectation: false, // Editors cannot share
            ),

            // Viewer permissions
            new Assertion(
                tupleKey: new AssertionTupleKey(
                    user: 'user:viewer',
                    relation: 'can_view',
                    object: 'document:strategy_2024',
                ),
                expectation: true,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey(
                    user: 'user:viewer',
                    relation: 'can_edit',
                    object: 'document:strategy_2024',
                ),
                expectation: false,
            ),
        ];
    }
}

// example: reading-assertions
// Reading existing assertions
$assertions = $client->readAssertions()->unwrap();

echo "Current test assertions:\n";

foreach ($assertions->getAssertions() as $assertion) {
    $status = $assertion->getExpectation() ? '✓' : '✗';
    echo sprintf(
        "%s %s %s %s\n",
        $status,
        $assertion->getUser(),
        $assertion->getRelation(),
        $assertion->getObject(),
    );
}
// end-example: reading-assertions
