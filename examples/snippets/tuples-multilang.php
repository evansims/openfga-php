<?php

declare(strict_types=1);

use OpenFGA\{Client, Language};
use OpenFGA\Exceptions\{ClientError, ClientException};

use function OpenFGA\{tuple, tuples, write};

// Create a client with Spanish error messages
$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
    language: Language::Spanish,
);

// Store configuration
$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// Attempt to write an invalid tuple
try {
    // This will throw a validation exception because of invalid identifier format
    $tupleKey = tuple('user: anne', 'viewer', 'document:report');
} catch (ClientException $e) {
    // The error message will be in Spanish
    echo "Error (Spanish): {$e->getMessage()}\n";

    // But the error enum remains the same for consistent handling
    if (ClientError::Validation === $e->kind()) {
        echo "Validation error detected\n";
    }
}

// Switch to French for another example
$frenchClient = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
    language: Language::French,
);

$frenchResult = $frenchClient->writeTuples(
    store: $storeId,
    model: $modelId,
    writes: tuples(
        tuple('user:bob', 'invalid_relation', 'document:test'),
    ),
);

if ($frenchResult->failed()) {
    $error = $frenchResult->err();

    if ($error instanceof ClientException) {
        // Error message in French
        echo "Error (French): {$error->getMessage()}\n";

        // Same enum-based handling works regardless of language
        if (ClientError::Validation === $error->kind()) {
            echo "✓ Invalid relation error detected\n";
        }
    }
}
