# Framework Integration

Ready to integrate OpenFGA into your existing application? This guide shows you how to add authorization to popular PHP frameworks and patterns.

## Laravel Integration

### Service Provider Setup

Create a service provider to configure OpenFGA:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenFGA\Client;
use OpenFGA\ClientInterface;
use OpenFGA\Authentication\ClientCredentialAuthentication;

class OpenFgaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ClientInterface::class, function () {
            return new Client(
                url: config('openfga.url'),
                authentication: new ClientCredentialAuthentication(
                    clientId: config('openfga.client_id'),
                    clientSecret: config('openfga.client_secret'),
                    issuer: config('openfga.issuer'),
                    audience: config('openfga.audience'),
                ),
            );
        });
    }
}
```

Add to your `config/openfga.php`:

```php
<?php

return [
    'url' => env('OPENFGA_URL', 'http://localhost:8080'),
    'store_id' => env('OPENFGA_STORE_ID'),
    'model_id' => env('OPENFGA_MODEL_ID'),
    'client_id' => env('OPENFGA_CLIENT_ID'),
    'client_secret' => env('OPENFGA_CLIENT_SECRET'),
    'issuer' => env('OPENFGA_ISSUER'),
    'audience' => env('OPENFGA_AUDIENCE'),
];
```

### Permission Middleware

Create middleware for route-level authorization:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenFGA\ClientInterface;
use function OpenFGA\{tuple, allowed};

class CheckPermission
{
    private array $routeToResourceMap;

    public function __construct(private ClientInterface $client, array $routeToResourceMap = [])
    {
        $this->routeToResourceMap = $routeToResourceMap ?: [
            'documents.*' => 'document',
            'users.*' => 'user',
            'projects.*' => 'project',
            'teams.*' => 'team',
            // Add more mappings as needed
        ];
    }

    public function handle(Request $request, Closure $next, string $relation, ?string $resourceParam = null): Response|JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Extract resource from route parameter or use a default pattern
        $resource = $resourceParam
            ? $request->route($resourceParam)
            : $this->extractResourceFromRoute($request);

        $canAccess = allowed(
            client: $this->client,
            store: config('openfga.store_id'),
            model: config('openfga.model_id'),
            tuple: tuple("user:{$user->id}", $relation, $resource)
        );

        if (!$canAccess) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }

    protected function extractResourceFromRoute(Request $request): string
    {
        $routeName = $request->route()->getName();
        $resourceType = $this->resolveResourceType($routeName, $request);
        $resourceId = $request->route('id') ?? $request->route('uuid') ?? $request->route('document') ?? $request->route('project');

        return "{$resourceType}:{$resourceId}";
    }

    protected function resolveResourceType(string $routeName, Request $request): string
    {
        // Check if route name matches any configured patterns
        foreach ($this->routeToResourceMap as $pattern => $resourceType) {
            if (fnmatch($pattern, $routeName)) {
                return $resourceType;
            }
        }

        // Fallback: extract resource type from route name
        // For routes like 'documents.show', 'users.edit', etc.
        $parts = explode('.', $routeName);
        if (count($parts) >= 2) {
            return rtrim($parts[0], 's'); // Remove trailing 's' for plurals
        }

        // Last resort: use the first segment of the URL path
        $pathSegments = explode('/', trim($request->path(), '/'));
        return rtrim($pathSegments[0] ?? 'resource', 's');
    }
}
```

Use in your routes:

```php
// routes/web.php
Route::middleware(['auth', 'permission:editor,document'])->group(function () {
    Route::put('/documents/{document}', [DocumentController::class, 'update']);
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
});

Route::middleware(['auth', 'permission:viewer,document'])->group(function () {
    Route::get('/documents/{document}', [DocumentController::class, 'show']);
});
```

### Eloquent Model Integration

Add authorization helpers to your models:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenFGA\ClientInterface;
use function OpenFGA\{tuple, allowed};

class Document extends Model
{
    public function userCan(User $user, string $relation): bool
    {
        $client = app(ClientInterface::class);

        return allowed(
            client: $client,
            store: config('openfga.store_id'),
            model: config('openfga.model_id'),
            tuple: tuple("user:{$user->id}", $relation, "document:{$this->id}")
        );
    }

    public function scopeAccessibleBy($query, User $user, string $relation)
    {
        $client = app(ClientInterface::class);

        $accessibleIds = $client->listObjects(
            store: config('openfga.store_id'),
            model: config('openfga.model_id'),
            user: "user:{$user->id}",
            relation: $relation,
            type: 'document'
        )->unwrap()->getObjects();

        return $query->whereIn('id', $accessibleIds);
    }
}
```

Usage in controllers:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        // Only show documents the user can view
        $documents = Document::accessibleBy($request->user(), 'viewer')->get();

        return view('documents.index', compact('documents'));
    }

    public function show(Document $document, Request $request)
    {
        // Middleware already checked permission, but you could double-check here
        if (!$document->userCan($request->user(), 'viewer')) {
            abort(403);
        }

        return view('documents.show', compact('document'));
    }
}
```

## Symfony Integration

### Service Configuration

Configure OpenFGA as a service in `config/services.yaml`:

```yaml
# config/services.yaml
services:
  OpenFGA\ClientInterface:
    class: OpenFGA\Client
    arguments:
      $url: "%env(OPENFGA_URL)%"
      $authentication: "@openfga.authentication"

  openfga.authentication:
    class: OpenFGA\Authentication\ClientCredentialAuthentication
    arguments:
      $clientId: "%env(OPENFGA_CLIENT_ID)%"
      $clientSecret: "%env(OPENFGA_CLIENT_SECRET)%"
      $issuer: "%env(OPENFGA_ISSUER)%"
      $audience: "%env(OPENFGA_AUDIENCE)%"

parameters:
  openfga.store_id: "%env(OPENFGA_STORE_ID)%"
  openfga.model_id: "%env(OPENFGA_MODEL_ID)%"
```

### Voter Implementation

Create a custom voter for authorization decisions:

```php
<?php

namespace App\Security\Voter;

use OpenFGA\ClientInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use function OpenFGA\{tuple, allowed};

class OpenFgaVoter extends Voter
{
    public function __construct(
        private ClientInterface $client,
        private string $storeId,
        private string $modelId,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Support any attribute that follows the pattern "openfga.{relation}"
        return str_starts_with($attribute, 'openfga.');
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Extract relation from attribute (e.g., "openfga.edit" -> "edit")
        $relation = substr($attribute, 7);

        // Build resource identifier from subject
        $resource = $this->buildResourceIdentifier($subject);

        return allowed(
            client: $this->client,
            store: $this->storeId,
            model: $this->modelId,
            tuple: tuple("user:{$user->getUserIdentifier()}", $relation, $resource)
        );
    }

    private function buildResourceIdentifier(mixed $subject): string
    {
        if (is_string($subject)) {
            return $subject;
        }

        if (is_object($subject) && method_exists($subject, 'getId')) {
            $type = strtolower((new \ReflectionClass($subject))->getShortName());
            return "{$type}:{$subject->getId()}";
        }

        throw new \InvalidArgumentException('Cannot build resource identifier from subject');
    }
}
```

Use in your controllers:

```php
<?php

namespace App\Controller;

use App\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    #[Route('/documents/{id}', methods: ['GET'])]
    public function show(Document $document): Response
    {
        $this->denyAccessUnlessGranted('openfga.viewer', $document);

        return $this->render('document/show.html.twig', [
            'document' => $document,
        ]);
    }

    #[Route('/documents/{id}/edit', methods: ['GET', 'POST'])]
    public function edit(Document $document): Response
    {
        $this->denyAccessUnlessGranted('openfga.editor', $document);

        // Edit logic here
        return $this->render('document/edit.html.twig', [
            'document' => $document,
        ]);
    }
}
```

## Generic PHP Integration

### Simple Authorization Service

For frameworks without built-in DI, create a simple service:

```php
<?php

namespace App\Services;

use OpenFGA\Client;
use OpenFGA\ClientInterface;
use OpenFGA\Authentication\ClientCredentialAuthentication;
use function OpenFGA\{tuple, tuples, allowed, write, delete};

class AuthorizationService
{
    private ClientInterface $client;
    private string $storeId;
    private string $modelId;

    public function __construct(array $config)
    {
        $this->client = new Client(
            url: $config['url'],
            authentication: new ClientCredentialAuthentication(
                clientId: $config['client_id'],
                clientSecret: $config['client_secret'],
                issuer: $config['issuer'],
                audience: $config['audience'],
            ),
        );

        $this->storeId = $config['store_id'];
        $this->modelId = $config['model_id'];
    }

    public function can(string $userId, string $relation, string $resource): bool
    {
        return allowed(
            client: $this->client,
            store: $this->storeId,
            model: $this->modelId,
            tuple: tuple("user:{$userId}", $relation, $resource)
        );
    }

    public function grant(string $userId, string $relation, string $resource): void
    {
        write(
            client: $this->client,
            store: $this->storeId,
            model: $this->modelId,
            tuples: tuples(tuple("user:{$userId}", $relation, $resource))
        );
    }

    public function revoke(string $userId, string $relation, string $resource): void
    {
        delete(
            client: $this->client,
            store: $this->storeId,
            model: $this->modelId,
            tuples: tuples(tuple("user:{$userId}", $relation, $resource))
        );
    }

    public function listUserResources(string $userId, string $relation, string $type): array
    {
        return $this->client->listObjects(
            store: $this->storeId,
            model: $this->modelId,
            user: "user:{$userId}",
            relation: $relation,
            type: $type
        )->unwrap()->getObjects();
    }
}
```

Usage:

```php
<?php

// Bootstrap
$config = [
    'url' => $_ENV['OPENFGA_URL'],
    'store_id' => $_ENV['OPENFGA_STORE_ID'],
    'model_id' => $_ENV['OPENFGA_MODEL_ID'],
    'client_id' => $_ENV['OPENFGA_CLIENT_ID'],
    'client_secret' => $_ENV['OPENFGA_CLIENT_SECRET'],
    'issuer' => $_ENV['OPENFGA_ISSUER'],
    'audience' => $_ENV['OPENFGA_AUDIENCE'],
];

$auth = new AuthorizationService($config);

// Check permissions
if (!$auth->can($currentUserId, 'editor', 'document:readme')) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Grant permission
$auth->grant($newUserId, 'viewer', 'document:readme');

// List accessible documents
$editableDocuments = $auth->listUserResources($currentUserId, 'editor', 'document');
```

## Common Integration Patterns

### Caching Layer

Add caching to improve performance:

```php
<?php

use Psr\SimpleCache\CacheInterface;

class CachedAuthorizationService
{
    public function __construct(
        private AuthorizationService $auth,
        private CacheInterface $cache,
        private int $ttl = 300 // 5 minutes
    ) {}

    public function can(string $userId, string $relation, string $resource): bool
    {
        $cacheKey = "auth:{$userId}:{$relation}:{$resource}";

        $cached = $this->cache->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $result = $this->auth->can($userId, $relation, $resource);

        // Cache the result and maintain the user index
        $this->cache->set($cacheKey, $result, $this->ttl);
        $this->addToUserCacheIndex($userId, $cacheKey);

        return $result;
    }

    public function grant(string $userId, string $relation, string $resource): void
    {
        $this->auth->grant($userId, $relation, $resource);

        // Invalidate related cache entries
        $this->invalidateUserCache($userId);
    }

    private function invalidateUserCache(string $userId): void
    {
        // PSR-16 compliant approach: maintain an index of user cache keys
        $userIndexKey = "auth:index:{$userId}";
        $userCacheKeys = $this->cache->get($userIndexKey, []);

        if (!empty($userCacheKeys)) {
            // Delete all cached permissions for this user
            $this->cache->deleteMultiple($userCacheKeys);
            
            // Clear the index
            $this->cache->delete($userIndexKey);
        }
    }

    private function addToUserCacheIndex(string $userId, string $cacheKey): void
    {
        $userIndexKey = "auth:index:{$userId}";
        $userCacheKeys = $this->cache->get($userIndexKey, []);
        
        if (!in_array($cacheKey, $userCacheKeys)) {
            $userCacheKeys[] = $cacheKey;
            $this->cache->set($userIndexKey, $userCacheKeys, 86400); // 24 hours
        }
    }
}
```

### Middleware Factory

Create reusable middleware for different frameworks:

```php
<?php

class AuthorizationMiddlewareFactory
{
    public static function createLaravelMiddleware(AuthorizationService $auth): \Closure
    {
        return function ($request, $next, $relation, $resourceParam = 'id') use ($auth) {
            $user = $request->user();
            $resourceId = $request->route($resourceParam);
            $resourceType = $request->route()->getActionName();

            if (!$auth->can($user->id, $relation, "{$resourceType}:{$resourceId}")) {
                abort(403);
            }

            return $next($request);
        };
    }

    public static function createSymfonyEventListener(AuthorizationService $auth): \Closure
    {
        return function ($event) use ($auth) {
            $request = $event->getRequest();
            $relation = $request->attributes->get('_openfga_relation');

            if (!$relation) {
                return; // No authorization required
            }

            // Extract user and resource from request
            // Implementation depends on your setup
        };
    }
}
```

## Testing Integration

### Mock Service for Tests

Create a mock authorization service for testing:

```php
<?php

class MockAuthorizationService implements AuthorizationInterface
{
    private array $permissions = [];

    public function __construct() {}

    public function can(string $userId, string $relation, string $resource): bool
    {
        return $this->permissions["{$userId}:{$relation}:{$resource}"] ?? false;
    }

    public function grant(string $userId, string $relation, string $resource): void
    {
        $this->permissions["{$userId}:{$relation}:{$resource}"] = true;
    }

    public function revoke(string $userId, string $relation, string $resource): void
    {
        unset($this->permissions["{$userId}:{$relation}:{$resource}"]);
    }

    // Test helper methods
    public function grantPermission(string $userId, string $relation, string $resource): void
    {
        $this->grant($userId, $relation, $resource);
    }

    public function clearPermissions(): void
    {
        $this->permissions = [];
    }
}
```

Use in your tests:

```php
<?php

class DocumentControllerTest extends TestCase
{
    private MockAuthorizationService $auth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auth = new MockAuthorizationService([]);
        $this->app->instance(AuthorizationService::class, $this->auth);
    }

    public function testUserCanViewDocument(): void
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();

        // Grant permission in mock
        $this->auth->grantPermission($user->id, 'viewer', "document:{$document->id}");

        $response = $this->actingAs($user)->get("/documents/{$document->id}");

        $response->assertOk();
    }

    public function testUserCannotEditWithoutPermission(): void
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();

        // No permissions granted

        $response = $this->actingAs($user)->put("/documents/{$document->id}");

        $response->assertForbidden();
    }
}
```

## What's Next?

Now that you have OpenFGA integrated:

**Optimize Performance:**

- **[Concurrency →](Concurrency.md)** - Batch operations efficiently
- Add caching layers for frequently checked permissions
- Use `listObjects` instead of individual checks where possible

**Monitor & Debug:**

- **[Observability →](Observability.md)** - Add telemetry to track authorization performance
- **[Results →](Results.md)** - Handle errors gracefully in production

**Advanced Features:**

- **[Assertions →](Assertions.md)** - Test your authorization logic
- **[Models →](Models.md)** - Build complex permission hierarchies
