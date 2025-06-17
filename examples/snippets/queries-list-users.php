<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: helper
use OpenFGA\Models\Collections\UserTypeFilters;
use OpenFGA\Models\UserTypeFilter;

use function OpenFGA\{filter, users};

// Find users with permission using the helper
$viewers = users(
    object: 'document:budget',
    relation: 'viewer',
    filters: filter('user'),
    client: $client,
    store: $storeId,
    model: $modelId,
);

echo "Users who can view document:budget:\n";

foreach ($viewers as $user) {
    echo "- {$user}\n";
}
// end-example: helper

// example: client
// Find users using the client directly
$result = $client->listUsers(
    store: $storeId,
    model: $modelId,
    object: 'document:budget-2024',
    relation: 'editor',
    userFilters: new UserTypeFilters([
        new UserTypeFilter('user'),
        new UserTypeFilter('team', 'member'),
    ]),
);

if ($result->succeeded()) {
    $response = $result->unwrap();
    $users = $response->getUsers();

    echo "Users and teams who can edit budget-2024:\n";

    foreach ($users as $user) {
        $identifier = $user->getObject();

        if ($user->getRelation()) {
            echo "- {$identifier} (relation: {$user->getRelation()})\n";
        } else {
            echo "- {$identifier}\n";
        }
    }
}
// end-example: client
