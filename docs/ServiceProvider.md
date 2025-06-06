# Service Provider Integration

The OpenFGA PHP SDK supports automatic dependency injection container registration through the [tbachert/spi](https://packagist.org/packages/tbachert/spi) service provider interface pattern.

## What is this?

If your framework or application uses a dependency injection container that supports automatic service discovery, the OpenFGA SDK can automatically register its services without manual configuration.

This is particularly useful for:
- Laravel applications with service auto-discovery
- Symfony applications with auto-wiring
- Custom frameworks with SPI support
- Any application using tbachert/spi

## Installation

The SPI integration is completely optional. Install the service provider package if you want automatic registration:

```bash
composer require tbachert/spi
```

That's it! The OpenFGA services will be automatically available in your container.

## What gets registered?

The service provider automatically registers these configuration-free interfaces:

- **`OpenFGA\Observability\TelemetryInterface`** - No-op telemetry provider (can be overridden)
- **`OpenFGA\Language\TransformerInterface`** - DSL to model transformation
- **`OpenFGA\Schemas\SchemaValidatorInterface`** - JSON schema validation for models

**Note:** `ClientInterface` and `RequestManagerInterface` are NOT automatically registered because they require configuration (URL, authentication, etc.). You'll need to register these manually in your application.

## Usage examples

### Laravel

First, register the OpenFGA client in your service provider:

```php
// In your AppServiceProvider
public function register()
{
    // Register the client (requires manual configuration)
    $this->app->singleton(ClientInterface::class, function () {
        return new Client(
            url: config('openfga.api_url'),
            authentication: new ClientCredentialAuthentication(
                clientId: config('openfga.client_id'),
                clientSecret: config('openfga.client_secret')
            )
        );
    });
}
```

Then use it in controllers or services:

```php
use function OpenFGA\tuple;

// In any Laravel service, controller, or job
class DocumentController extends Controller
{
    public function __construct(
        private readonly ClientInterface $openfga
    ) {}

    public function show(Request $request, string $documentId)
    {
        $canView = $this->openfga->check(
            store: config('openfga.store_id'),
            model: config('openfga.model_id'),
            tupleKey: tuple(
                user: "user:{$request->user()->id}",
                relation: 'viewer',
                object: "document:{$documentId}"
            )
        )->unwrap()->getAllowed();

        if (!$canView) {
            abort(403, 'Access denied');
        }

        return view('documents.show', compact('documentId'));
    }
}
```

### Symfony

First, configure the client in your services configuration:

```yaml
# config/services.yaml
services:
  # Configure the OpenFGA client
  OpenFGA\ClientInterface:
    class: OpenFGA\Client
    arguments:
      url: '%env(OPENFGA_API_URL)%'
      authentication: '@openfga.auth'
      
  # Configure authentication
  openfga.auth:
    class: OpenFGA\Authentication\ClientCredentialAuthentication
    arguments:
      clientId: '%env(OPENFGA_CLIENT_ID)%'
      clientSecret: '%env(OPENFGA_CLIENT_SECRET)%'
```

Then use it in services or controllers:

```php
use function OpenFGA\tuple;

// In any Symfony service or controller
class DocumentService
{
    public function __construct(
        private readonly ClientInterface $openfga
    ) {}

    public function canUserEdit(string $userId, string $documentId): bool
    {
        return $this->openfga->check(
            store: $_ENV['OPENFGA_STORE_ID'],
            model: $_ENV['OPENFGA_MODEL_ID'],
            tupleKey: tuple(
                user: "user:{$userId}",
                relation: 'editor',
                object: "document:{$documentId}"
            )
        )->unwrap()->getAllowed();
    }
}
```

### Custom dependency injection

```php
// Basic services are automatically registered when tbachert/spi is installed
$container = new YourContainer();

// Register the client manually (requires configuration)
$container->singleton(ClientInterface::class, function() {
    return new Client(
        url: 'https://api.fga.example',
        authentication: new ClientCredentialAuthentication(
            clientId: 'your-client-id',
            clientSecret: 'your-client-secret'
        )
    );
});

// Use the configured client
$client = $container->get(ClientInterface::class);
$canEdit = $client->check(/* ... */)->unwrap()->getAllowed();
```

## Customizing services

You can override any of the registered services in your application:

### Laravel service provider

```php
// In your AppServiceProvider
public function register()
{
    // Override telemetry with OpenTelemetry implementation
    $this->app->singleton(TelemetryInterface::class, function () {
        return new OpenTelemetryProvider(/* your config */);
    });
    
    // Register the client (always required)
    $this->app->singleton(ClientInterface::class, function () {
        return new Client(
            url: config('openfga.api_url'),
            authentication: new ClientCredentialAuthentication(
                clientId: config('openfga.client_id'),
                clientSecret: config('openfga.client_secret')
            )
        );
    });
}
```

### Symfony services configuration

```yaml
# config/services.yaml
services:
  # Override telemetry with OpenTelemetry
  OpenFGA\Observability\TelemetryInterface:
    class: OpenFGA\Observability\OpenTelemetryProvider
    arguments:
      - '@open_telemetry.meter_provider'
      - '@open_telemetry.tracer_provider'

  # Register client configuration (always required)
  OpenFGA\ClientInterface:
    class: OpenFGA\Client
    arguments:
      url: '%env(OPENFGA_API_URL)%'
      authentication: '@openfga.auth'
```

## Manual registration

If you prefer manual control or don't want to use tbachert/spi, you can register services manually:

```php
use OpenFGA\Client;
use OpenFGA\ClientInterface;
use OpenFGA\Integration\ServiceProvider;

$container = new YourContainer();

// Manual registration using the service provider
$serviceProvider = new ServiceProvider();
$serviceProvider->register($container);

// Or register services individually
$container->singleton(ClientInterface::class, function () {
    return new Client(url: 'https://api.fga.example');
});
```

## Troubleshooting

**Services not being registered?**
- Ensure `tbachert/spi` is installed: `composer require tbachert/spi`
- Check that the plugin is enabled: `composer config allow-plugins.tbachert/spi true`
- Verify your container supports automatic service discovery

**Need different configuration?**
- Override services in your application's service provider
- Use manual registration instead of automatic discovery
- Configure the client directly without dependency injection

**Framework-specific issues?**
- Laravel: Services should be available immediately after installation
- Symfony: May require clearing cache: `bin/console cache:clear`
- Custom frameworks: Ensure your container supports one of the common DI methods

## Next steps

With automatic service registration working, you can focus on:

- **[Setting up authentication](Authentication.md)** for production environments
- **[Creating authorization models](Models.md)** to define your permission rules  
- **[Writing queries](Queries.md)** to check permissions in your application
- **[Adding observability](Observability.md)** with OpenTelemetry integration