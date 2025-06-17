<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: groups
use function OpenFGA\{allowed,tuple, tuples, write};

// Working with groups using write() helper
$result = write(
    tuples: tuples(
        // Add user to a group
        tuple('user:anne', 'member', 'team:engineering'),
        // Grant permission to the entire group
        tuple('team:engineering#member', 'editor', 'document:technical-specs'),
    ),
    client: $client,
    store: $storeId,
    model: $modelId,
);

echo "✓ Anne added to engineering team\n";
echo "✓ Engineering team granted editor access to technical specs\n";

// Now Anne can edit the technical specs because she's a member of the engineering team
// Let's verify this:
$canEdit = allowed(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:anne', 'editor', 'document:technical-specs'),
);

if ($canEdit) {
    echo "✓ Confirmed: Anne can edit technical-specs through team membership\n";
}
// end-example: groups
