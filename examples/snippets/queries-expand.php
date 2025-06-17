<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: client
use function OpenFGA\tuple;

// Expand relationships using the client
$result = $client->expand(
    store: $storeId,
    model: $modelId,
    tuple: tuple('', 'viewer', 'document:planning-doc'),
);

if ($result->succeeded()) {
    $tree = $result->unwrap()->getTree();
} else {
    echo 'Error expanding: ' . $result->err()->getMessage();
    $tree = null;
}

if (null !== $tree) {
    echo "user:anne has owner access through:\n";
    printTree($tree->getRoot());
}
// end-example: client

// Expand another relationship
$result2 = $client->expand(
    store: $storeId,
    model: $modelId,
    tuple: tuple('', 'editor', 'folder:project-files'),
);

if ($result2->succeeded()) {
    $response = $result2->unwrap();
    $tree = $response->getTree();

    // Process the tree structure
    echo "Expanded permission tree:\n";
    printTree($tree->getRoot());
}

function printTree($node, $indent = ''): void
{
    if (null === $node) {
        return;
    }

    // Handle leaf nodes (direct assignments)
    if (null !== $node->getLeaf()) {
        $users = $node->getLeaf()->getUsers();

        if (null !== $users) {
            foreach ($users as $user) {
                echo "{$indent}- {$user->getUser()}\n";
            }
        }
    }

    // Handle union nodes (OR relationships)
    if (null !== $node->getUnion()) {
        $nodes = $node->getUnion()->getNodes();

        foreach ($nodes as $childNode) {
            printTree($childNode, $indent . '  ');
        }
    }
}
