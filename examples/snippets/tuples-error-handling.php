<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: error-handling
use OpenFGA\Exceptions\{ClientError, ClientException};
use Throwable;

use function OpenFGA\{result, tuple, write};

// Example: Writing tuples with robust error handling
function addUserToDocument(Client $client, string $storeId, string $modelId, string $userId, string $documentId, string $role = 'viewer'): bool
{
    return result(fn () => write(
        tuples: tuple("user:{$userId}", $role, "document:{$documentId}"),
        client: $client,
        store: $storeId,
        model: $modelId,
    ))
        ->success(function () use ($userId, $documentId, $role): void {
            echo "✓ Access granted: {$userId} as {$role} on {$documentId}\n";
        })
        ->then(fn () => true)
        ->failure(function (Throwable $error): void {
            if ($error instanceof ClientException) {
                match ($error->kind()) {
                    ClientError::Validation => print ('⚠️  Validation error granting access: ' . json_encode($error->context()) . PHP_EOL),
                    ClientError::Configuration => print ("❌ Model configuration error: {$error->getMessage()}\n"),
                    default => print ("❌ Failed to grant access: {$error->kind()->name}\n"),
                };
            } else {
                print "❌ Unexpected error: {$error->getMessage()}\n";
            }
        })
        ->unwrap();
}

// Usage example
$success = addUserToDocument($client, $storeId, $modelId, 'anne', 'budget-2024', 'editor');

if ($success) {
    echo "Permission successfully granted!\n";
}
// end-example: error-handling
