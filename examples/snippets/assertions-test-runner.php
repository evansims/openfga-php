<?php

declare(strict_types=1);

use OpenFGA\Client;
use OpenFGA\Models\{Assertion, AssertionTupleKey};
use OpenFGA\Models\Collections\Assertions;

use function OpenFGA\{tuple, tuples, write};

final class AuthorizationTestRunner
{
    private ?Client $client = null;

    public function __construct(
        private string $apiUrl,
        private bool $verbose = false,
    ) {
    }

    public function runTests(string $storeId, string $modelId): bool
    {
        $this->log("Running authorization tests...\n");

        // Read and validate assertions
        $result = $this->getClient()->readAssertions(
            store: $storeId,
            model: $modelId,
        );

        if ($result->failed()) {
            $this->log('❌ Failed to read assertions: ' . $result->err()->getMessage());

            return false;
        }

        $assertions = $result->unwrap()->getAssertions();
        $this->log(sprintf("Found %d assertions to test\n", count($assertions)));

        $passed = 0;
        $failed = 0;

        foreach ($assertions as $assertion) {
            $checkResult = $this->getClient()->check(
                store: $storeId,
                model: $modelId,
                tuple: tuple(
                    user: $assertion->getTupleKey()->getUser(),
                    relation: $assertion->getTupleKey()->getRelation(),
                    object: $assertion->getTupleKey()->getObject(),
                ),
            );

            if ($checkResult->failed()) {
                $this->log(sprintf(
                    "❌ Error checking %s %s %s: %s\n",
                    $assertion->getTupleKey()->getUser(),
                    $assertion->getTupleKey()->getRelation(),
                    $assertion->getTupleKey()->getObject(),
                    $checkResult->err()->getMessage(),
                ));
                $failed++;

                continue;
            }

            $allowed = $checkResult->unwrap()->getAllowed();
            $expected = $assertion->getExpectation();

            if ($allowed === $expected) {
                $passed++;

                if ($this->verbose) {
                    $this->log(sprintf(
                        "✅ %s %s %s = %s (expected)\n",
                        $assertion->getTupleKey()->getUser(),
                        $assertion->getTupleKey()->getRelation(),
                        $assertion->getTupleKey()->getObject(),
                        $allowed ? 'allowed' : 'denied',
                    ));
                }
            } else {
                $failed++;
                $this->log(sprintf(
                    "❌ %s %s %s = %s (expected %s)\n",
                    $assertion->getTupleKey()->getUser(),
                    $assertion->getTupleKey()->getRelation(),
                    $assertion->getTupleKey()->getObject(),
                    $allowed ? 'allowed' : 'denied',
                    $expected ? 'allowed' : 'denied',
                ));
            }
        }

        $this->log(sprintf(
            "\nTest Results: %d passed, %d failed\n",
            $passed,
            $failed,
        ));

        return 0 === $failed;
    }

    private function getClient(): Client
    {
        if (null === $this->client) {
            $this->client = new Client(
                url: $this->apiUrl,
            );
        }

        return $this->client;
    }

    private function log(string $message): void
    {
        echo $message;
    }
}

// Usage in CI/CD
$apiUrl = $_ENV['FGA_API_URL'] ?? 'http://localhost:8080';
$runner = new AuthorizationTestRunner(
    apiUrl: $apiUrl,
    verbose: true, // Set to true for demonstration
);

// Setup: Create some assertions for demonstration
if (isset($_ENV['FGA_STORE_ID'], $_ENV['FGA_MODEL_ID'])) {
    $client = new Client(url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080');

    // Create some sample assertions
    $client->writeAssertions(
        store: $_ENV['FGA_STORE_ID'],
        model: $_ENV['FGA_MODEL_ID'],
        assertions: new Assertions(
            new Assertion(
                tupleKey: new AssertionTupleKey('user:alice', 'viewer', 'document:budget'),
                expectation: true,
            ),
            new Assertion(
                tupleKey: new AssertionTupleKey('user:bob', 'editor', 'document:budget'),
                expectation: false,
            ),
        ),
    );

    // Write some actual tuples to match the assertions
    write(
        client: $client,
        store: $_ENV['FGA_STORE_ID'],
        model: $_ENV['FGA_MODEL_ID'],
        tuples: tuples(
            tuple('user:alice', 'viewer', 'document:budget'),
        ),
    );
}

$success = $runner->runTests(
    storeId: $_ENV['FGA_STORE_ID'],
    modelId: $_ENV['FGA_MODEL_ID'],
);

exit($success ? 0 : 1);
