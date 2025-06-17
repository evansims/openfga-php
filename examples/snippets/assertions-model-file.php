<?php

declare(strict_types=1);

use OpenFGA\Models\{Assertion, AssertionTupleKey};
use OpenFGA\Models\Collections\Assertions;

// authorization-models/document-system.assertions.php

return new Assertions(
    // Document ownership tests
    new Assertion(
        tupleKey: new AssertionTupleKey(
            user: 'user:alice',
            relation: 'owner',
            object: 'document:1',
        ),
        expectation: true,
    ),
    new Assertion(
        tupleKey: new AssertionTupleKey(
            user: 'user:bob',
            relation: 'owner',
            object: 'document:1',
        ),
        expectation: false,
    ),

    // Inherited permissions tests
    new Assertion(
        tupleKey: new AssertionTupleKey(
            user: 'user:alice',
            relation: 'editor',
            object: 'document:1',
        ),
        expectation: true, // Owner can edit
    ),
    new Assertion(
        tupleKey: new AssertionTupleKey(
            user: 'user:alice',
            relation: 'viewer',
            object: 'document:1',
        ),
        expectation: true, // Owner can view
    ),

    // Team permissions tests
    new Assertion(
        tupleKey: new AssertionTupleKey(
            user: 'team:engineering#member',
            relation: 'viewer',
            object: 'document:roadmap',
        ),
        expectation: true,
    ),
    new Assertion(
        tupleKey: new AssertionTupleKey(
            user: 'team:marketing#member',
            relation: 'viewer',
            object: 'document:roadmap',
        ),
        expectation: false,
    ),

    // Conditional permissions tests
    new Assertion(
        tupleKey: new AssertionTupleKey(
            user: 'user:contractor',
            relation: 'viewer',
            object: 'document:sensitive',
        ),
        expectation: true,
        // Note: Conditional assertions require context to be set during testing
    ),
);
