<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;

use function OpenFGA\{allowed, dsl, model, store, tuple, write};

/*
 * OpenFGA Hello World Example
 *
 * The simplest possible introduction to OpenFGA: Can Alice view the budget document?
 * This example demonstrates the core concept in just a few lines.
 */

try {
    echo "👋 OpenFGA Hello World\n\n";

    // Initialize client
    $client = new Client(
        url: 'http://localhost:8080',
        httpClient: new FileGetContents(new Psr17Factory),
        httpResponseFactory: new Psr17Factory,
        httpStreamFactory: new Psr17Factory,
        httpRequestFactory: new Psr17Factory,
    );

    // Create workspace
    echo "Creating workspace...\n";
    $storeId = store($client, 'hello-world');

    // Define permission rule: users can view documents
    echo "Defining permission rule...\n";
    $authModel = dsl($client, '
        model
          schema 1.1
        type user
        type document
          relations
            define viewer: [user]
    ');
    $modelId = model($client, $storeId, $authModel);

    // Grant permission: Alice can view budget
    echo "Granting permission...\n";
    write($client, $storeId, $modelId, tuple('user:alice', 'viewer', 'document:budget'));

    // Check permission
    echo "Checking permission...\n";
    $canView = allowed($client, $storeId, $modelId, tuple('user:alice', 'viewer', 'document:budget'));

    // Show result
    echo "\n🎯 Result:\n";
    echo $canView ? '✅ Alice CAN view the budget document!' : '❌ Alice CANNOT view the budget document';
    echo "\n\n";

    echo "🧹 Cleaning up...\n";
    $client->deleteStore(store: $storeId);

    echo "✨ That's it! You've just implemented authorization with OpenFGA.\n\n";

    echo "🚀 Next steps:\n";
    echo "   • Try the comprehensive quick-start: examples/quick-start/example.php\n";
    echo "   • Read the documentation: docs/README.md\n";
    echo "   • Learn about authorization models: docs/Models.md\n";
} catch (Throwable $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
    echo "💡 Make sure OpenFGA is running on http://localhost:8080\n";
    echo "   You can start it with: docker run -p 8080:8080 openfga/openfga run\n";

    exit(1);
}
