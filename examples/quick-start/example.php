<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;

use function OpenFGA\{allowed, dsl, model, store, tuple, tuples, write};

/*
 * OpenFGA PHP SDK Quick Start Example
 *
 * This example demonstrates the essential steps to get started with OpenFGA:
 * creating a store, defining permissions, writing relationships, and checking authorization.
 */

try {
    echo "🚀 OpenFGA PHP SDK Quick Start\n\n";

    // 1. Initialize the SDK Client
    echo "1️⃣ Initializing OpenFGA client...\n";

    $client = new Client(
        url: 'http://localhost:8080',
        httpClient: new FileGetContents(new Psr17Factory),
        httpResponseFactory: new Psr17Factory,
        httpStreamFactory: new Psr17Factory,
        httpRequestFactory: new Psr17Factory,
    );

    echo "✅ Client initialized\n\n";

    // 2. Create a Store
    echo "2️⃣ Creating store...\n";

    $storeId = store($client, 'quick-start-demo');

    echo "✅ Store created: {$storeId}\n\n";

    // 3. Define Authorization Model with DSL
    echo "3️⃣ Creating authorization model...\n";

    $modelDsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define owner: [user]
            define editor: [user] or owner
            define viewer: [user] or editor or owner
    ';

    $authModel = dsl($client, $modelDsl);
    $modelId = model($client, $storeId, $authModel);

    echo "✅ Authorization model created: {$modelId}\n\n";

    // 4. Write Relationship Tuples
    echo "4️⃣ Writing relationship tuples...\n";

    write(
        client: $client,
        store: $storeId,
        model: $modelId,
        tuples: tuples(
            tuple('user:anne', 'owner', 'document:roadmap'),
            tuple('user:bob', 'viewer', 'document:roadmap'),
        ),
    );

    echo "✅ Relationships established:\n";
    echo "   • Anne owns the roadmap document\n";
    echo "   • Bob can view the roadmap document\n\n";

    // 5. Check Authorization
    echo "5️⃣ Checking permissions...\n";

    $canAnneView = allowed(
        client: $client,
        store: $storeId,
        model: $modelId,
        tuple: tuple('user:anne', 'viewer', 'document:roadmap'),
    );

    $canAnneEdit = allowed(
        client: $client,
        store: $storeId,
        model: $modelId,
        tuple: tuple('user:anne', 'editor', 'document:roadmap'),
    );

    $canBobView = allowed(
        client: $client,
        store: $storeId,
        model: $modelId,
        tuple: tuple('user:bob', 'viewer', 'document:roadmap'),
    );

    $canBobEdit = allowed(
        client: $client,
        store: $storeId,
        model: $modelId,
        tuple: tuple('user:bob', 'editor', 'document:roadmap'),
    );

    echo "✅ Authorization results:\n";
    echo '   • Anne can view roadmap: ' . ($canAnneView ? 'YES' : 'NO') . " (owner inherits viewer)\n";
    echo '   • Anne can edit roadmap: ' . ($canAnneEdit ? 'YES' : 'NO') . " (owner inherits editor)\n";
    echo '   • Bob can view roadmap: ' . ($canBobView ? 'YES' : 'NO') . " (explicit viewer)\n";
    echo '   • Bob can edit roadmap: ' . ($canBobEdit ? 'YES' : 'NO') . " (not an editor)\n\n";

    // 6. Clean up (delete the temporary store)
    echo "6️⃣ Cleaning up...\n";

    $client->deleteStore(store: $storeId)
        ->success(fn () => print "✅ Store deleted\n\n");

    echo "🎉 Quick start completed successfully!\n\n";

    echo "📖 Next steps:\n";
    echo "   • Read the documentation: docs/README.md\n";
    echo "   • Learn about authorization models: docs/Models.md\n";
    echo "   • Explore advanced queries: docs/Queries.md\n";
    echo "   • Set up authentication: docs/Authentication.md\n";
} catch (Throwable $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
    echo "💡 Make sure OpenFGA is running on http://localhost:8080\n";
    echo "   You can start it with: docker run -p 8080:8080 openfga/openfga run\n";

    exit(1);
}
