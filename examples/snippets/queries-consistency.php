<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: consistency
use OpenFGA\Models\Enums\Consistency;

use function OpenFGA\tuple;

// Use MINIMIZE_LATENCY for fast reads (default)
$result = $client->check(
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:anne', 'viewer', 'document:report'),
    consistency: Consistency::MINIMIZE_LATENCY,
);

// Use HIGHER_CONSISTENCY for critical operations
$criticalCheck = $client->check(
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:admin', 'owner', 'document:configuration'),
    consistency: Consistency::HIGHER_CONSISTENCY,
);

echo "Write completed\n";

if ($criticalCheck->succeeded()) {
    $allowed = $criticalCheck->unwrap()->getAllowed();
    echo $allowed
        ? 'Admin has owner permission (with high consistency)'
        : 'Admin does not have owner permission';
    echo "\n";
}
// end-example: consistency
