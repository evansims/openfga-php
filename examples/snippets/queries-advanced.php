<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

use OpenFGA\Exceptions\{AuthenticationException, ClientException, NetworkException};
use OpenFGA\Exceptions\{ClientError, NetworkError};
use OpenFGA\Models\TupleKey;
use RuntimeException;

// example: error-handling
// Advanced error handling service
final class PermissionService
{
    public function __construct(
        private Client $client,
        private string $storeId,
        private string $modelId,
    ) {
    }

    public function checkAccess(string $user, string $relation, string $object): bool
    {
        $result = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tuple: new TupleKey($user, $relation, $object),
        );

        // Handle specific error cases
        if ($result->failed()) {
            $error = $result->err();

            if ($error instanceof NetworkException) {
                // Handle network-specific errors
                match ($error->kind()) {
                    NetworkError::Timeout => $this->logTimeout($user, $object),
                    NetworkError::Server => $this->alertOpsTeam(),
                    NetworkError::Request => $this->logGenericNetworkError($error),
                    default => $this->logGenericNetworkError($error),
                };

                // Fallback to cached permissions or safe default
                return $this->getCachedPermission($user, $relation, $object) ?? false;
            }

            if ($error instanceof AuthenticationException) {
                // Authentication errors should be fatal
                throw new RuntimeException('Service authentication failed');
            }

            if ($error instanceof ClientException) {
                // Handle client errors
                match ($error->kind()) {
                    ClientError::Validation => $this->logInvalidRequest($error),
                    ClientError::Configuration => $this->reconfigureStore(),
                    default => $this->logClientError($error),
                };

                return false;
            }

            // Unknown error type - log and deny access
            $this->logUnknownError($error);

            return false;
        }

        return $result->unwrap()->getAllowed();
    }

    private function alertOpsTeam(): void
    {
        // Send alert to operations team
    }

    private function getCachedPermission(string $user, string $relation, string $object): ?bool
    {
        // Check cache for recent permission result
        return null;
    }

    private function logClientError(Throwable $error): void
    {
        error_log('Client error: ' . $error->getMessage());
    }

    private function logGenericNetworkError(Throwable $error): void
    {
        error_log('Network error: ' . $error->getMessage());
    }

    private function logInvalidRequest(Throwable $error): void
    {
        error_log('Invalid request: ' . $error->getMessage());
    }

    private function logTimeout(string $user, string $object): void
    {
        error_log("Permission check timeout for {$user} on {$object}");
    }

    private function logUnknownError(Throwable $error): void
    {
        error_log('Unknown error: ' . $error::class . ' - ' . $error->getMessage());
    }

    private function reconfigureStore(): void
    {
        // Attempt to reconfigure with valid store
    }
}
// end-example: error-handling

// example: permission-gates
// Usage
$service = new PermissionService($client, $storeId, $modelId);
$canAccess = $service->checkAccess('user:alice', 'viewer', 'document:roadmap');

// Permission-aware middleware
final class FgaAuthMiddleware
{
    public function __construct(
        private Client $client,
        private string $storeId,
        private string $modelId,
    ) {
    }

    public function handle($request, $next)
    {
        $user = $request->user();
        $resource = $request->route('resource');
        $action = $this->mapHttpMethodToRelation($request->method());

        $allowed = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tuple: new TupleKey("user:{$user->id}", $action, "document:{$resource}"),
        )->unwrap()->getAllowed();

        if (! $allowed) {
            throw new RuntimeException('Access denied');
        }

        return $next($request);
    }

    private function mapHttpMethodToRelation(string $method): string
    {
        return match ($method) {
            'GET' => 'viewer',
            'PUT', 'PATCH' => 'editor',
            'DELETE' => 'owner',
            default => 'viewer',
        };
    }
}
// end-example: permission-gates

// example: data-filtering
// Efficient data filtering
function getEditableDocuments(Client $client, string $storeId, string $modelId, string $userId): array
{
    // Get all documents the user can edit
    $result = $client->streamedListObjects(
        store: $storeId,
        model: $modelId,
        user: "user:{$userId}",
        relation: 'editor',
        type: 'document',
    );

    if ($result->failed()) {
        return [];
    }

    $generator = $result->unwrap();
    $documentIds = [];

    foreach ($generator as $streamedResponse) {
        $documentIds[] = $streamedResponse->getObject();
    }

    // Convert to your document IDs
    return array_map(
        fn ($objectId) => str_replace('document:', '', $objectId),
        $documentIds,
    );
}
// end-example: data-filtering

// example: debugging
// Debugging permission issues
function debugUserAccess(Client $client, string $storeId, string $modelId, string $user, string $object): void
{
    echo "Debugging access for {$user} on {$object}\n";

    // Check all possible relations
    $relations = ['viewer', 'editor', 'owner'];

    foreach ($relations as $relation) {
        $result = $client->check(
            store: $storeId,
            model: $modelId,
            tuple: new TupleKey($user, $relation, $object),
        );

        if ($result->succeeded()) {
            $allowed = $result->unwrap()->getAllowed() ? '✓' : '✗';
            echo "{$relation}: {$allowed}\n";

            if ($allowed) {
                // Expand to see why they have access
                $tree = $client->expand(
                    store: $storeId,
                    tuple: new TupleKey($user, $relation, $object),
                    model: $modelId,
                )->unwrap()->getTree();

                echo '  Access path: ';
                // Analyze tree structure to show access path
                analyzeAccessPath($tree, $user);
                echo "\n";
            }
        }
    }
}

function analyzeAccessPath($tree, $targetUser, $path = []): bool
{
    if (null === $tree) {
        return false;
    }

    // Check leaf nodes for direct user assignment
    if (null !== $tree->getLeaf()) {
        $users = $tree->getLeaf()->getUsers();

        if (null !== $users) {
            foreach ($users as $user) {
                if ($user->getUser() === $targetUser) {
                    echo implode(' → ', array_merge($path, [$targetUser]));

                    return true;
                }
            }
        }
    }

    // Check union nodes (OR relationships)
    if (null !== $tree->getUnion()) {
        $nodes = $tree->getUnion()->getNodes();

        foreach ($nodes as $index => $node) {
            $nodePath = $path;

            if (1 < count($nodes)) {
                $nodePath[] = "branch_{$index}";
            }

            if (analyzeAccessPath($node, $targetUser, $nodePath)) {
                return true;
            }
        }
    }

    return false;
}
// end-example: debugging
