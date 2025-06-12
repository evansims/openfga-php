<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;

use function OpenFGA\{dsl, model, store, tuple, tuples, write};

/*
 * Duplicate Filtering Example
 *
 * This example demonstrates how the OpenFGA SDK automatically filters
 * duplicate tuples and handles delete precedence in write operations.
 */

$storeId = null;
$client = null;

try {
    echo "🔍 OpenFGA Duplicate Filtering Example\n\n";

    // Initialize client
    $client = new Client(
        url: 'http://localhost:8080',
        httpClient: new FileGetContents(new Psr17Factory),
        httpResponseFactory: new Psr17Factory,
        httpStreamFactory: new Psr17Factory,
        httpRequestFactory: new Psr17Factory,
    );

    // Create workspace
    echo "Setting up workspace...\n";
    $storeId = store($client, 'duplicate-filtering-demo');

    // Define authorization model
    $authModel = dsl($client, '
        model
          schema 1.1
        type user
        type document
          relations
            define reader: [user]
            define editor: [user]
            define viewer: [user]
            define owner: [user]
    ');
    $modelId = model($client, $storeId, $authModel);

    echo "✅ Workspace ready\n\n";

    // Example 1: Duplicate writes are automatically filtered
    echo "1️⃣ Example 1: Automatic duplicate filtering\n";
    echo "Writing 5 tuples with 2 duplicates...\n";

    $writes = tuples(
        tuple('user:anne', 'reader', 'document:budget'),
        tuple('user:bob', 'editor', 'document:budget'),
        tuple('user:anne', 'reader', 'document:budget'), // duplicate - will be filtered out
        tuple('user:charlie', 'viewer', 'document:budget'),
        tuple('user:bob', 'editor', 'document:budget'), // duplicate - will be filtered out
    );

    // Only 3 unique tuples will be written (anne, bob, charlie)
    $result = $client->writeTuples(
        store: $storeId,
        model: $modelId,
        writes: $writes,
    );

    if ($result->succeeded()) {
        echo "✅ Successfully wrote 3 unique tuples (5 input, 2 duplicates filtered)\n\n";
    } else {
        echo "❌ Failed to write tuples\n\n";
    }

    // Example 2: Delete takes precedence when tuple appears in both writes and deletes
    echo "2️⃣ Example 2: Delete precedence\n";
    echo "Processing writes and deletes where same tuple appears in both...\n";

    $writes = tuples(
        tuple('user:anne', 'reader', 'document:budget'),
        tuple('user:bob', 'editor', 'document:budget'),
        tuple('user:charlie', 'viewer', 'document:budget'),
    );

    $deletes = tuples(
        tuple('user:bob', 'editor', 'document:budget'), // This will remove bob from writes
        tuple('user:david', 'owner', 'document:old-file'),
    );

    // Result: writes anne and charlie, deletes bob and david
    $result = $client->writeTuples(
        store: $storeId,
        model: $modelId,
        writes: $writes,
        deletes: $deletes,
    );

    if ($result->succeeded()) {
        echo "✅ Successfully processed with delete precedence (bob removed from writes)\n\n";
    } else {
        echo "❌ Failed to process tuples\n\n";
    }

    // Example 3: Batch operations with duplicate filtering
    echo "3️⃣ Example 3: Batch operations with filtering\n";
    echo "Creating batch with intentional duplicates...\n";

    // Create a batch with duplicates to demonstrate filtering
    $batchTuples = [];

    for ($i = 1; 10 >= $i; $i++) {
        $batchTuples[] = tuple("user:user{$i}", 'reader', 'document:report');

        if (0 === $i % 3) {
            // Add some duplicates
            $batchTuples[] = tuple("user:user{$i}", 'reader', 'document:report');
        }
    }
    $largeBatch = tuples(...$batchTuples);

    echo 'Input: ' . count($batchTuples) . " tuples (with duplicates)\n";

    // Duplicates are filtered before processing, improving efficiency
    $result = write(
        client: $client,
        store: $storeId,
        model: $modelId,
        tuples: $largeBatch,
    );

    if ($result->succeeded()) {
        echo "✅ Successfully processed batch with automatic duplicate filtering\n\n";
    } else {
        echo "❌ Failed to process batch\n\n";
    }

    echo "✨ Duplicate filtering demonstration complete!\n\n";

    echo "🎯 Key Takeaways:\n";
    echo "   • Duplicate tuples are automatically filtered before API calls\n";
    echo "   • Delete operations take precedence over write operations\n";
    echo "   • Filtering improves performance by reducing unnecessary API calls\n";
    echo "   • Works seamlessly with both single and batch operations\n";
} catch (Throwable $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
    echo "💡 Make sure OpenFGA is running on http://localhost:8080\n";
    echo "   You can start it with: docker run -p 8080:8080 openfga/openfga run\n";

    exit(1);
} finally {
    // Clean up the store regardless of success or failure
    if (null !== $storeId && null !== $client) {
        try {
            echo "\n🧹 Cleaning up...\n";
            $client->deleteStore(store: $storeId);
            echo "✅ Store deleted successfully\n";
        } catch (Throwable $cleanupError) {
            echo '⚠️  Failed to delete store: ' . $cleanupError->getMessage() . "\n";
        }
    }
}
